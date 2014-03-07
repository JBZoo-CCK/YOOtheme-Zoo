<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Class that represents an Author of a comment
 *
 * @package Component.Classes
 */
class CommentAuthor {

	/**
	 * A reference to the global App object
	 *
	 * @var App
	 * @since 2.0
	 */
	public $app;

	/**
	 *
	 * The referenced application
	 *
	 * @var Application
	 */
	public $application;

	/**
	 * The name of the Author
	 *
	 * @var string
	 * @since 2.0
	 */
	public $name;

	/**
	 * The email of the author
	 *
	 * @var string
	 * @since 2.0
	 */
	public $email;

	/**
	 * The url of the author
	 *
	 * @var string
	 * @since 2.0
	 */
	public $url;

	/**
	 * The id of the user that made the comment
	 *
	 * @var int
	 * @since 2.0
	 */
	public $user_id;

	/**
	 * Class Constructor
	 *
	 * @param string $name    The name of the author
	 * @param string $email   The email of the author
	 * @param string $url     The url of the author
	 * @param string $user_id The id of the user linked to the author
	 */
	public function __construct($name = '', $email = '', $url = '', $user_id = '', $application = null) {

		// set vars
		$this->name		   = $name;
		$this->email	   = $email;
		$this->url		   = $url;
		$this->user_id	   = $user_id;
		$this->application = $application;
	}

	/**
	 * Get a user avatar from Gravatar or use the default avatar
	 *
	 * @param  int $size Size in pixels of the avatar (squared). Default: 32
	 *
	 * @return string        The html tag img showing the avatar+
	 *
	 * @since 2.0
	 */
	public function getAvatar($size = 32) {
		$default = JURI::root().'media/zoo/assets/images/avatar.png';

		if ($this->email) {
			return '<img title="'.$this->name.'" src="http://www.gravatar.com/avatar/'.md5($this->app->string->strtolower(trim($this->email))).'?s='.$size.'&amp;d='.urlencode($default).'" height="'.$size.'" width="'.$size.'" alt="'.$this->name.'" />';
		} else {
			return '<img title="'.$this->name.'" src="'.$default.'" height="'.$size.'" width="'.$size.'" alt="'.$this->name.'" />';
		}
	}

	/**
	 * Check the if the author is a guest
	 *
	 * @return boolean True if the user is not logged in
	 *
	 * @since 2.0
	 */
	public function isGuest() {
		return empty($this->user_id);
	}

	/**
	 * Check if the author is a joomla admin
	 *
	 * @return boolean True if the user is an admin
	 *
	 * @since 2.0
	 */
	public function isJoomlaAdmin() {
		return false;
	}

	/**
	 * Get the user type
	 *
	 * @return string The type of the user
	 *
	 * @since 2.0
	 */
	public function getUserType() {
		return strtolower(str_replace('CommentAuthor', '', get_class($this)));
	}

}

/**
 * Class representing an auhtor which is a Joomla User
 *
 * @package Component.Classes
 */
class CommentAuthorJoomla extends CommentAuthor {

	/**
	 * Get the related JUser object
	 *
	 * @return JUser The joomla user
	 *
	 * @since 2.0
	 */
	public function getJoomlaUser() {
		return $this->app->user->get($this->user_id);
	}

	/**
	 * Check if the joomla user is an admin
	 *
	 * @return boolean If the user is an admin
	 *
	 * @since 2.0
	 */
	public function isJoomlaAdmin() {
		if ($user = $this->getJoomlaUser()) {
			return $this->app->user->isJoomlaAdmin($user);
		}
		return false;
	}

}

/**
 * Class that represents an author that has logged in through facebook
 *
 * @package Component.Classes
 */
class CommentAuthorFacebook extends CommentAuthor {

	/**
	 * Get the avatar from facebook
	 *
	 * @param  integer $size The size in pixels (squared). Default: 32
	 *
	 * @return string        The html img tag with the avatar
	 *
	 * @since 2.0
	 */
	public function getAvatar($size = 32) {

		if ($this->user_id) {

			$cache 		 = $this->app->cache->create($this->app->path->path('cache:') . '/author_cache', true, 604800);
			$cache_check = ($cache) ? $cache->check() : false;
			$url 		 = '';

			// try to get avatar url from cache
			if ($cache_check) {
				$url = $cache->get($this->user_id);
			}

			// if url is empty, try to get avatar url from twitter
			if (empty($url)) {
				$info = $this->app->facebook->fields($this->user_id, array('picture'), $this->application);
				if (isset($info['picture']->data->url)) {
					$url = $info['picture']->data->url;
				}
				if ($cache_check) {
					$cache->set($this->user_id, $url);
					$cache->save();
				}
			}

			if (!empty($url)) {
				return '<img alt="'.$this->name.'" title="'.$this->name.'" src="'.$url.'" height="'.$size.'" width="'.$size.'" />';
			}

		}
	    return parent::getAvatar($size);
	}

}

/**
 * Class that represents an author that has logged in using Twitter
 *
 * @package Component.Classes
 */
class CommentAuthorTwitter extends CommentAuthor {

	/**
	 * Get the avatar from twitter
	 *
	 * @param  integer $size The size of the avatar (squared). Default: 32
	 *
	 * @return string        The html img tag
	 *
	 * @since 2.0
	 */
	public function getAvatar($size = 32) {
		if ($this->user_id) {

			$cache 		 = $this->app->cache->create($this->app->path->path('cache:') . '/author_cache', true, 604800);
			$cache_check = ($cache) ? $cache->check() : false;
			$url 		 = '';

			// try to get avatar url from cache
			if ($cache_check) {
				$url = $cache->get($this->user_id);
			}

			// if url is empty, try to get avatar url from twitter
			if (empty($url)) {
				$info = $this->app->twitter->fields($this->user_id, array('profile_image_url'), $this->application);
				if (isset($info['profile_image_url'])) {
					$url = $info['profile_image_url'];
				}
				if ($cache_check) {
					$cache->set($this->user_id, $url)->save();
				}
			}

			if (!empty($url)) {
				return '<img alt="'.$this->name.'" title="'.$this->name.'" src="'.$url.'" height="'.$size.'" width="'.$size.'" />';
			}

		}
	    return parent::getAvatar($size);
	}

}