<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: CommentController
		The controller class for comments
*/
class CommentController extends AppController {

    /*
       Variable: author
         Active author.
    */
	public $author;

	public $application;

 	/*
		Function: Constructor

		Parameters:
			$default - Array

		Returns:
			DefaultController
	*/
	public function __construct($default = array()) {
		parent::__construct($default);

		// get user
		$this->user = $this->app->user->get();

		// get application
		$this->application = $this->app->zoo->getApplication();

		// get comment params
		$this->params = $this->app->parameter->create($this->application->getParams()->get('global.comments.'));

	}

	public function save() {

		// check for request forgeries
		$this->app->session->checkToken() or jexit('Invalid Token');

		// set currently active author
		$this->author = $this->app->comment->activeAuthor();

		// init vars
		$redirect = $this->app->request->getString('redirect');
		$login 	  = $this->app->request->getCmd(CommentHelper::COOKIE_PREFIX.'login', '', 'cookie');

		if ($this->author->getUserType() == $login) {

			if ($this->params->get('enable_comments', false)) {

				// init vars
				$content   = $this->app->request->getVar('content', null, '', 'string', JREQUEST_ALLOWRAW);
				$item_id   = $this->app->request->getInt('item_id', 0);
				$parent_id = $this->app->request->getInt('parent_id', 0);

				// filter content
				$content = $this->app->comment->filterContentInput($content);

				// set content in session
				$this->app->session->set('com_zoo.comment.content', $content);

				// set author name, email and url, if author is guest
				if ($this->author->isGuest()) {

					$this->author->name  = $this->app->request->getString('author');
					$this->author->email = $this->app->request->getString('email');
					$this->author->url   = $this->app->request->getString('url');

					// save cookies
					$this->app->comment->saveCookies($this->author->name, $this->author->email, $this->author->url);

				}

				try {

					// Check captcha
					if ($plugin = $this->params->get('captcha', false) and (!$this->params->get('captcha_guest_only', 0) or !$this->app->user->get()->id)){

						$captcha = JCaptcha::getInstance($plugin);
		            	if (!$captcha->checkAnswer($this->app->request->getString('captcha', ''))) {
		            		$error = $captcha->getError();
							if (!($error instanceof Exception)) {
								$error = new JException($error);
							}
		                	throw new CommentControllerException(JText::_('ZOO_CHECK_CAPTCHA') . ' - ' . $error );
		            	}

					}

					// get comment table
					$table = $this->app->table->comment;

					// get parent
					$parent    = $table->get($parent_id);
					$parent_id = ($parent && $parent->item_id == $item_id) ? $parent->id : 0;

					// create comment
					$comment = $this->app->object->create('Comment');
					$comment->parent_id = $parent_id;
					$comment->item_id = $item_id;
					$comment->ip = $this->app->useragent->ip();
					$comment->created = $this->app->date->create()->toSQL();
					$comment->content = $content;
					$comment->state = Comment::STATE_UNAPPROVED;

					// auto approve comment
					$approved = $this->params->get('approved', 0);
					if ($this->author->isJoomlaAdmin()) {
						$comment->state = Comment::STATE_APPROVED;
					} else if ($approved == 1) {
						$comment->state = Comment::STATE_APPROVED;
					} else if ($approved == 2 && $table->getApprovedCommentCount($this->author)) {
						$comment->state = Comment::STATE_APPROVED;
					}

					// bind Author
					$comment->bindAuthor($this->author);

					// validate comment, if not an administrator
					if (!$this->author->isJoomlaAdmin()) {
						$this->_validate($comment);
					}

					// save comment
					$table->save($comment);

					// remove content from session, if comment was saved
					$this->app->session->set('com_zoo.comment.content', '');

				} catch (CommentControllerException $e) {

					// raise warning on exception
					$this->app->error->raiseWarning(0, (string) $e);

				} catch (AppException $e) {

					// raise warning on exception
					$this->app->error->raiseWarning(0, JText::_('ERROR_SAVING_COMMENT'));

					// add exception details, for super administrators only
					if ($this->user->superadmin) {
						$this->app->error->raiseWarning(0, (string) $e);
					}

				}

				// add anchor to redirect, if comment was saved
				if ($comment->id) {
					$redirect .= '#comment-'.$comment->id;
				}

			} else {
				// raise warning on comments not enabled
				$this->app->error->raiseWarning(0, JText::_('Comments are not enabled.'));
			}
		} else {

			// raise warning on exception
			$this->app->error->raiseWarning(0, JText::_('ERROR_SAVING_COMMENT'));

			// add exception details, for super administrators only
			if ($this->user->superadmin) {
				$this->app->error->raiseWarning(0, JText::_('User types didn\'t match.'));
			}
		}

		$this->setRedirect($redirect);
	}

	public function edit() {
		// init vars
		$id       = $this->app->request->getInt('comment_id', 0);
		$content  = $this->app->request->getString('content', '');
		$msg      = null;
		$table    = $this->app->table->comment;
		$comment  = $table->get($id);

		try {
			if (!$comment) {

				throw new CommentControllerException("Error Processing Request");

			}

			// set redirect
			$redirect = $this->app->route->item($comment->getItem());
			$redirect .= '#comment-'.$comment->id;

			if (!$comment->canManageComments()) {

				throw new CommentControllerException("Invalid Access Permissions");

			}

			// save content
			$comment->content = $content;
			$table->save($comment);

			// set redirect message
			$msg = JText::_('Comment Edited');

		} catch (AppException $e) {

			// raise notice on exception
			$this->app->error->raiseWarning(0, JText::_('Error Editing Comment').' ('.$e.')');

		}

		$this->setRedirect($redirect, $msg);
	}

	public function delete() {

		// init vars
		$id       = $this->app->request->getInt('comment_id', 0);
		$msg      = null;
		$table    = $this->app->table->comment;
		$comment  = $table->get($id);

		try {
			if (!$comment) {

				throw new CommentControllerException("Error Processing Request");

			}

			// set redirect
			$redirect = $this->app->route->item($comment->getItem());
			$redirect .= $comment->parent_id != 0 ? '#comment-'.$comment->parent_id : '#comments';

			if (!$comment->canManageComments()) {

				throw new CommentControllerException("Invalid Access Permissions");

			}

			// delete comment
			$table->delete($comment);


			// set redirect message
			$msg = JText::_('Comment Deleted');

		} catch (AppException $e) {

			// raise notice on exception
			$this->app->error->raiseWarning(0, JText::_('Error Deleting Comment').' ('.$e.')');

		}

		$this->setRedirect($redirect, $msg);
	}

	/*
		Function: approve
			Approve a comment

		Returns:
			Void
	*/
	public function approve() {
		$this->_editState(1);
	}

	/*
		Function: unapprove
			Unapprove a comment

		Returns:
			Void
	*/
	public function unapprove() {
		$this->_editState(0);
	}

	/*
		Function: spam
			Mark comment as spam

		Returns:
			Void
	*/
	public function spam() {
		$this->_editState(2);
	}


	protected function _editState($state) {

		// init vars
		$id       = $this->app->request->getInt('comment_id', 0);
		$msg      = null;
		$table    = $this->app->table->comment;
		$comment  = $table->get($id);

		try {
			if (!$comment) {
				throw new CommentControllerException("Error Processing Request");
			}

			// set redirect
			$redirect = $this->app->route->item($comment->getItem());
			$redirect .= $comment->parent_id != 0 ? '#comment-'.$comment->parent_id : '#comments';

			if (!$comment->canManageComments()) {
				throw new CommentControllerException("Invalid Access Permissions");
			}

			// set state and safe
			$comment->setState($state, true);

			// set redirect message
			$msg = JText::_('Comment state edited');

		} catch (CommentControllerException $e) {

				// raise notice on exception
				$this->app->error->raiseWarning(0, JText::_('Error editing Comment State').' ('.$e.')');

		}

		$this->setRedirect($redirect, $msg);
	}

	public function unsubscribe() {

		// init vars
		$item_id  = $this->app->request->getInt('item_id');
		$email	  = $this->app->request->getString('email');
		$hash	  = $this->app->request->getCmd('hash');
		$msg	  = '';
		$redirect = 'index.php';

		try {

			if ($hash != $this->app->comment->getCookieHash($email, $item_id, '')) {
				throw new CommentControllerException('Hashes did not match.');
			}

			// subscribe author to item
			if (!($item = $this->app->table->item->get($item_id))) {
				throw new CommentControllerException('Item not found.');
			}

			$this->app->table->item->save($item->unsubscribe($email));

			$redirect = $this->app->route->item($item, false);
			$msg = JText::_('SUCCESSFULLY_UNSUBSCRIBED');

		} catch (CommentControllerException $e) {

			// raise warning on exception
			$this->app->error->raiseWarning(0, (string) $e);

		} catch (AppException $e) {

			// raise warning on exception
			$this->app->error->raiseWarning(0, JText::_('ERROR_UNSUBSCRIBING'));

			// add exception details, for super administrators only
			if ($this->user->superadmin) {
				$this->app->error->raiseWarning(0, (string) $e);
			}

		}

		$this->setRedirect(JRoute::_($redirect), $msg);

	}

	protected function _validate($comment) {

		// get params
		$require_author 		 = $this->params->get('require_name_and_mail', 0);
		$registered     		 = $this->params->get('registered_users_only', 0);
		$time_between_user_posts = $this->params->get('time_between_user_posts', 120);
		$blacklist      		 = $this->params->get('blacklist', '');

		// check if related item exists
		if ($this->app->table->item->get($comment->item_id) === null) {
			throw new CommentControllerException('Related item does not exists.');
		}

		// only registered users can comment
		if ($registered && $this->author->isGuest()) {
			throw new CommentControllerException('LOGIN_TO_LEAVE_OMMENT');
		}

		// validate required name/email
		if ($this->author->isGuest() && $require_author && (empty($comment->author) || empty($comment->email))) {
			throw new CommentControllerException('Please enter the required fields author and email.');
		}

		// validate email format
		try {
			$this->app->validator->create('email')->addOption('required', false)->clean($comment->email);
		} catch (AppValidatorException $e) {
			throw new CommentControllerException('Please enter a valid email address.');
		}

		// validate url format
		try {
			$this->app->validator->create('url')->addOption('required', false)->clean($comment->url);
		} catch (AppValidatorException $e) {
			throw new CommentControllerException('Please enter a valid website link.');
		}

		// check if content is empty
		if (empty($comment->content)) {
			throw new CommentControllerException('Please enter a comment.');
		}

		// check quick multiple posts
		if ($last = $this->app->table->comment->getLastComment($comment->ip, $this->author)) {
			if ($this->app->date->create($comment->created)->toUnix() < $this->app->date->create($last->created)->toUnix() + $time_between_user_posts) {
				throw new CommentControllerException('You are posting comments too quickly. Slow down a bit.');
			}
		}

		// check against spam blacklist
		if ($this->app->comment->matchWords($comment, $blacklist) && $comment->state != Comment::STATE_SPAM) {
			$comment->state = Comment::STATE_SPAM;
		}

		// check comment for spam (akismet)
		if ($this->params->get('akismet_enable', 0) && $comment->state != Comment::STATE_SPAM) {
			try {

				$this->app->comment->akismet($comment, $this->params->get('akismet_api_key'));

			} catch (Exception $e) {

				// re-throw exception, for super administrators only
				if ($this->user->superadmin) throw new AppException($e->getMessage());

			}
		}

		// check comment for spam (mollom)
		if ($this->params->get('mollom_enable', 0) && $comment->state != Comment::STATE_SPAM) {
			try {

				$this->app->comment->mollom($comment, $this->params->get('mollom_public_key'), $this->params->get('mollom_private_key'));

			} catch (Exception $e) {

				// re-throw exception, for super administrators only
				if ($this->user->superadmin) throw new AppException($e->getMessage());

			}
		}

	}

	public function facebookConnect() {

		// init vars
		$item_id = $this->app->request->getInt('item_id', 0);
		$item    = $this->app->table->item->get($item_id);

		// get facebook client
		$connection = $this->app->facebook->client();

		if ($connection && empty($connection->access_token)) {

			$redirect = JURI::root().'index.php?option='.$this->option.'&controller='.$this->controller.'&task=facebookauthenticate&item_id='.$item_id;
			$redirect = $connection->getAuthenticateURL($redirect);

		} else {

			// already connected
			$redirect = $this->app->route->item($item);

		}

		$this->setRedirect($redirect);

	}

	public function facebookAuthenticate() {

		// init vars
		$item_id = $this->app->request->getInt('item_id', 0);
		$item    = $this->app->table->item->get($item_id);

		// get facebook client
		$connection = $this->app->facebook->client();

		if ($connection) {
			$code = $this->app->request->getString('code', '');
			$redirect = JURI::root() .'index.php?option='.$this->option.'&controller='.$this->controller.'&task=facebookauthenticate&item_id='.$item_id;
			$url  = $connection->getAccessTokenURL($code, $redirect);

			$result = $this->app->http->get($url, array('ssl_verifypeer' => false));
			$token = str_replace('access_token=', '', $result['body']);
			$_SESSION['facebook_access_token'] = $token;
		}

		$this->setRedirect($this->app->route->item($item));
	}

	public function facebookLogout() {
		$this->app->facebook->logout();
		$this->setRedirect($this->app->request->getString('HTTP_REFERER', '', 'server'));
	}

	public function twitterConnect() {

		// get twitter client
		$connection = $this->app->twitter->client();

		// redirect to the referer after authorize/login procedure
		$referer = $this->app->request->getString('HTTP_REFERER', '', 'server');

		// retrieve request token only if token is not supplied already
		if ($connection && empty($connection->token)) {

			$redirect = JURI::root() .'index.php?option='.$this->option.'&app_id='.$this->application->id.'&controller='.$this->controller.'&task=twitterauthenticate&referer='.urlencode($referer);

			// get temporary credentials
			$request_token = $connection->getRequestToken($redirect);

			// save temporary credentials to session
			$_SESSION['twitter_oauth_token'] = $token = $request_token['oauth_token'];
			$_SESSION['twitter_oauth_token_secret'] = $request_token['oauth_token_secret'];

			// if last connection failed don't display authorization link
			switch ($connection->http_code) {
			  case 200:
			    // build authorize URL and redirect user to Twitter
			    $redirect = $connection->getAuthorizeURL($token);
			    break;
			  default:
			    // show notification if something went wrong.
				$this->app->error->raiseWarning(0, JText::_('ERROR_CONNECT_TWITTER'));

				$redirect = $referer;
			}
		} else {
			// already connected
			$redirect = $referer;
		}

		$this->setRedirect($redirect);

	}

	public function twitterAuthenticate() {

		// get twitter client
		$connection = $this->app->twitter->client();

		if ($connection) {
			// retrieve access token
			$token_credentials = $connection->getAccessToken($_REQUEST['oauth_verifier']);

			// replace request token with access token in session.
			if ($token_credentials) {
				$_SESSION['twitter_oauth_token'] = $token_credentials['oauth_token'];
				$_SESSION['twitter_oauth_token_secret'] = $token_credentials['oauth_token_secret'];
			} else {
				// show notification if something went wrong.
				$this->app->error->raiseWarning(0, JText::_('ERROR_CONNECT_TWITTER'));

			}
		}

		$this->setRedirect($this->app->request->getString('referer'));
	}

	public function twitterLogout() {
		$this->app->twitter->logout();
		$this->setRedirect($this->app->request->getString('HTTP_REFERER', '', 'server'));
	}

}

/*
	Class: CommentControllerException
*/
class CommentControllerException extends AppException {

	/**
	 * Converts the exception to a human readable string
	 *
	 * @return string The error message
	 *
	 * @since 1.0.0
	 */
	public function __toString() {
		return JText::_($this->getMessage());
	}

}