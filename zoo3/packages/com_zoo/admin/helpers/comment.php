<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * The comments helper class.
 *
 * @package Component.Helpers
 * @since 2.0
 */
class CommentHelper extends AppHelper {

	/**
	 * The cookie prefix
	 *
	 * @var string
	 * @since 2.0
	 */
	const COOKIE_PREFIX   = 'zoo-comment_';

	/**
	 * The cookie lifetime
	 *
	 * @var int
	 * @since 2.0
	 */
	const COOKIE_LIFETIME = 15552000; // 6 months

	/**
	 * Active author.
	 *
	 * @var CommentAuthor
	 * @since 2.0
	 */
	protected $_author;

	/**
	 * Render comments and respond form html.
	 *
	 * @param AppView $view The view the comments are rendered on
	 * @param Item $item The item whos comments are rendered
	 *
	 * @return string The html output
	 *
	 * @since 2.0
	 */
	public function renderComments($view, $item) {

		if ($item->getApplication()->isCommentsEnabled()) {

			// get application params
			$params = $this->app->parameter->create($item->getApplication()->getParams()->get('global.comments.'));

			if ($params->get('twitter_enable') && !function_exists('curl_init')) {
				$this->app->error->raiseWarning(500, JText::_('To use Twitter, CURL needs to be enabled in your php settings.'));
				$params->set('twitter_enable', false);
			}

			// get active author
			$active_author = $this->activeAuthor();

			// get comment content from session
			$content = $this->app->system->session->get('com_zoo.comment.content');
			$params->set('content', $content);

			// get comments and build tree
			$approved = $item->canManageComments() ? Comment::STATE_UNAPPROVED : Comment::STATE_APPROVED;
			$comments = $item->getCommentTree($approved);

			// build captcha
			$captcha = false;
			if ($plugin = $params->get('captcha', false) and (!$params->get('captcha_guest_only', 0) or !$this->app->user->get()->id)) {
				$captcha = JCaptcha::getInstance($plugin);
			}

			if ($item->isCommentsEnabled() || count($comments)-1) {
				// create comments html
				return $view->partial('comments', compact('item', 'active_author', 'comments', 'params', 'captcha'));
			}
		}

		return null;

	}

	/**
	 * Retrieve currently active author object.
	 *
	 * @return CommentAuthor The active author object
	 *
	 * @since 2.0
	 */
	public function activeAuthor() {

		if (!isset($this->_author)) {

			// get login (joomla users always win)
			$login = $this->app->request->getString(self::COOKIE_PREFIX.'login', '', 'cookie');

			// get active user
			$user = $this->app->user->get();

			if ($user->id) {

				// create author object from user
				$this->_author = $this->app->commentauthor->create('joomla', array($user->name, $user->email, '', $user->id));

			} else if ($login == 'facebook'
						&& ($connection = $this->app->facebook->client())
						&& ($content = $connection->getCurrentUserProfile())
						&& isset($content->id)
						&& isset($content->name)) {

				// create author object from facebook user id
				$this->_author = $this->app->commentauthor->create('facebook', array($content->name, null, null, $content->id));

			} else if ($login == 'twitter'
						&& ($connection = $this->app->twitter->client())
						&& ($content = $connection->get('account/verify_credentials'))
						&& isset($content->screen_name)
						&& isset($content->id)) {

				// create author object from twitter user id
				$this->_author = $this->app->commentauthor->create('twitter', array($content->screen_name, null, null, $content->id));

			} else {

				$this->app->twitter->logout();
				$this->app->facebook->logout();

				// create author object from cookies
				$cookie = $this->readCookies();
				$this->_author = $this->app->commentauthor->create('', array($cookie['author'], $cookie['email'], $cookie['url']));

			}
		}

		setcookie(self::COOKIE_PREFIX.'login', $this->_author->getUserType(), time() + self::COOKIE_LIFETIME, '/');

		return $this->_author;
	}

	/**
	 * Retrieve and verify author, email, url from cookie.
	 *
	 * @return array values from cookie
	 *
	 * @since 2.0
	 */
	public function readCookies() {

		// get cookies
		$data = array();
		foreach (array('hash', 'author', 'email', 'url') as $key) {
			$data[$key] = $this->app->request->getString(self::COOKIE_PREFIX.$key, '', 'cookie');
		}

		// verify hash
		if ($this->getCookieHash($data['author'], $data['email'], $data['url']) == $data['hash']) {
			return $data;
		}

		return array('hash' => null, 'author' => null, 'email' => null, 'url' => null);
	}

	/**
	 * Render comments and respond form html.
	 *
	 * @param string $author The author name
	 * @param string $email The author email
	 * @param string $url The author url
	 *
	 * @return void
	 *
	 * @since 2.0
	 */
	public function saveCookies($author, $email, $url) {

		$hash = $this->getCookieHash($author, $email, $url);

		// set cookies
		foreach (compact('hash', 'author', 'email', 'url') as $key => $value) {
			setcookie(self::COOKIE_PREFIX.$key, $value, time() + self::COOKIE_LIFETIME);
		}

	}

	/**
	 * Retrieve hash of author and email.
	 *
	 * @param string $author The author name
	 * @param string $email The author email
	 * @param string $url The author url
	 *
	 * @return string the cookie hash
	 *
	 * @since 2.0
	 */
	public function getCookieHash($author, $email, $url) {
		return $this->app->system->getHash($author.$email.$url);
	}

	/**
	 * Match words against comments content, author, URL, Email or IP.
	 *
	 * @param Comment $comment The comment
	 * @param array $words The words to match against
	 *
	 * @return boolean true on match
	 *
	 * @since 2.0
	 */
	public function matchWords($comment, $words) {

		$vars = array('author', 'email', 'url', 'ip', 'content');

		if ($words = explode("\n", $words)) {
			foreach ($words as $word) {
				if ($word = trim($word)) {

					$pattern = '/'.preg_quote($word).'/i';

					foreach ($vars as $var) {
						if (preg_match($pattern, $comment->$var)) {
							return true;
						}
					}
				}
			}
		}

		return false;
	}

	/**
	 * Remove html from comment content
	 *
	 * @param string $content The content
	 *
	 * @return string the filtered content
	 *
	 * @since 2.0
	 */
	public function filterContentInput($content) {

		// remove all html tags or escape if in [code] tag
		$content = preg_replace_callback('/\[code\](.+?)\[\/code\]/is', create_function('$matches', 'return htmlspecialchars($matches[0]);'), $content);
		$content = strip_tags($content);

		return $content;
	}

	/**
	 * Auto linkify urls, emails
	 *
	 * @param string $content The content
	 *
	 * @return string the filtered content
	 *
	 * @since 2.0
	 */
	public function filterContentOutput($content) {

		$content = ' '.$content.' ';
	    $content = preg_replace_callback('/(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)(?:\([-A-Z0-9+&@#\/%=~_|$?!:;,.]*\)|[-A-Z0-9+&@#\/%=~_|$?!:;,.])*(?:\([-A-Z0-9+&@#\/%=~_|$?!:;,.]*\)|[A-Z0-9+&@#\/%=~_|$])/ix', array($this->app->comment, 'makeURLClickable'), $content);
	    $content = preg_replace("/\s([a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]*\@[a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]{2,6})([\s|\.|\,])/i"," <a href=\"mailto:$1\" rel=\"nofollow\">$1</a>$2", $content);
		$content = $this->app->string->substr($content, 1);
		$content = $this->app->string->substr($content, 0, -1);

		return nl2br($content);
	}

	/**
	 * Makes the url clickable (only used as callback internally)
	 *
	 * @param array $matches The url matches
	 *
	 * @return string the wrapped url
	 *
	 * @since 2.0
	 */
	protected function makeURLClickable($matches) {
		$url = $original_url = $matches[0];

		if (empty($url)) {
			return $url;
		}

		// Prepend scheme if URL appears to contain no scheme (unless a relative link starting with / or a php file).
		if (strpos($url, ':') === false &&	substr($url, 0, 1) != '/' && substr($url, 0, 1) != '#' && !preg_match('/^[a-z0-9-]+?\.php/i', $url)) {
			$url = 'http://' . $url;
		}

		return " <a href=\"$url\" rel=\"nofollow\">$original_url</a>";
	}

	/**
	 * Check if comment is spam using Akismet.
	 *
	 * @param Comment $comment The Comment object
	 * @param string $api_key The Akismet API key
	 *
	 * @return void
	 *
	 * @since 2.0
	 */
	public function akismet($comment, $api_key = '') {

		// load akismet class
		$this->app->loader->register('Akismet', 'libraries:akismet/akismet.php');

		// check comment
		$akismet = new Akismet(JURI::root(), $api_key);
		$akismet->setCommentAuthor($comment->author);
		$akismet->setCommentAuthorEmail($comment->email);
		$akismet->setCommentAuthorURL($comment->url);
		$akismet->setCommentContent($comment->content);

		// set state
		if ($akismet->isCommentSpam()) {
			$comment->state = Comment::STATE_SPAM;
		}

	}

	/**
	 * Check if comment is spam using Mollom.
	 *
	 * @param Comment $comment The Comment object
	 * @param string $public_key The Mollom public key
	 * @param string $private_key The Mollom private key
	 *
	 * @return void
	 *
	 * @since 2.0
	 */
	public function mollom($comment, $public_key = '', $private_key = '') {

		// check if curl functions are available
		if (!function_exists('curl_init')) return;

		// load mollom class
		$this->app->loader->register('Mollom', 'libraries:mollom/mollom.php');

		// set keys and get servers
		Mollom::setPublicKey($public_key);
		Mollom::setPrivateKey($private_key);
		Mollom::setServerList(Mollom::getServerList());

		// check comment
		$feedback = Mollom::checkContent(null, null, $comment->content, $comment->author, $comment->url, $comment->email);

		// set state
		if ($feedback['spam'] != 'ham') {
			$comment->state = Comment::STATE_SPAM;
		}

	}

	/**
	 * Send notification email
	 *
	 * @param Comment $comment The Comment object
	 * @param array $recipients The recipients email addresses (email => name)
	 * @param string $layout The layout
	 *
	 * @return void
	 *
	 * @since 2.0
	 */
	public function sendNotificationMail($comment, $recipients, $layout) {

		// workaround to make sure JSite is loaded
		$this->app->loader->register('JSite', 'root:includes/application.php');

		// init vars
		$item			  = $comment->getItem();
		$website_name	  = $this->app->system->config->get('sitename');
		$comment_link	  = $this->_getURL($this->app->route->comment($comment, false));
		$item_link		  = $this->_getURL($this->app->route->item($item, false));
		$website_link	  = $this->_getURL('index.php');

		// send email to $recipients
		foreach ($recipients as $email => $name) {

			if (empty($email) || $email == $comment->getAuthor()->email) {
				continue;
			}

			// build unsubscribe link
			$unsubscribe_link = JURI::root().'index.php?'.http_build_query(array(
				'option' => $this->app->component->self->name,
				'controller' => 'comment',
				'task' => 'unsubscribe',
				'item_id' => $item->id,
				'email' => $email,
				'hash' => $this->app->comment->getCookieHash($email, $item->id, '')
			), '', '&');

			$mail = $this->app->mail->create();
			$mail->setSubject(JText::_("Topic reply notification")." - ".$item->name);
			$mail->setBodyFromTemplate($item->getApplication()->getTemplate()->resource.$layout, compact(
				'item',
				'comment',
				'website_name',
				'email',
				'name',
				'comment_link',
				'item_link',
				'website_link',
				'unsubscribe_link'
			));
			$mail->addRecipient($email);
			$mail->Send();
		}
	}

	protected function _getURL($url) {

		// Get the router.
		$router = JApplication::getInstance('site')->getRouter();

		// Make sure that we have our router
		if (!$router) {
			return null;
		}

		if ((strpos($url, '&') !== 0) && (strpos($url, 'index.php') !== 0)) {
			return $url;
		}

		// Build route.
		$uri = $router->build($url);
		$url = $uri->toString();

		if (strpos(JPATH_BASE, 'administrator') !== false) {
			$url = preg_replace('#\/administrator#', '', $url, 1);
		}

		$prefix = JURI::getInstance()->toString(array('host', 'port'));

		// Make sure our URL path begins with a slash.
		if (!preg_match('#^/#', $url)) {
			$url = '/' . $url;
		}

		// Build the URL.
		$url = 'http://' . $prefix . $url;

		return $url;

	}

}

/**
 * CommentHelperException identifies an Exception in the CommentHelper class
 * @see CommentHelper
 */
class CommentHelperException extends AppException {}