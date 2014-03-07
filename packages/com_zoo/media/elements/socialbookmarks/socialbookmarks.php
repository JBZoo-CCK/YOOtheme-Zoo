<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
   Class: ElementSocialBookmarks
       The ElementSocialBookmarks element class
*/
class ElementSocialBookmarks extends Element implements iSubmittable {

	/*
		Function: hasValue
			Checks if the element's value is set.

	   Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/
	public function hasValue($params = array()) {
		return (bool) $this->get('value', $this->config->get('default'));
	}

	/*
		Function: render
			Override. Renders the element.

	   Parameters:
            $params - render parameter

		Returns:
			String - html
	*/
	public function render($params = array()) {

		if ($this->get('value', $this->config->get('default'))) {

			// init vars
			$bookmarks_config = $this->config->get('bookmarks');
			$bookmarks 		  = array();

			// get active bookmarks
			foreach (self::getBookmarks() as $bookmark => $data) {
				if ($this->config->get($bookmark)) {
					$bookmarks[$bookmark] = $data;
				}
			}

			// render layout
			if ($layout = $this->getLayout()) {
				return $this->renderLayout($layout, compact('bookmarks'));
			}
		}

		return null;
	}

	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit() {
		return $this->app->html->_('select.booleanlist', $this->getControlName('value'), '', $this->get('value', $this->config->get('default')));
	}

	/*
		Function: renderSubmission
			Renders the element in submission.

	   Parameters:
            $params - AppData submission parameters

		Returns:
			String - html
	*/
	public function renderSubmission($params = array()) {
        return $this->edit();
	}

	/*
		Function: validateSubmission
			Validates the submitted element

	   Parameters:
            $value  - AppData value
            $params - AppData submission parameters

		Returns:
			Array - cleaned value
	*/
	public function validateSubmission($value, $params) {
		return array('value' => (bool) $value->get('value'));
	}

	/*
		Function: getBookmarks
			Get array of supported bookmarks.

		Returns:
			Array - Bookmarks
	*/
	public static function getBookmarks() {

		// Google
		$bookmarks['google']['link']  = "http://www.google.com/";
		$bookmarks['google']['click'] = "window.open('http://www.google.com/bookmarks/mark?op=add&amp;hl=en&amp;bkmk='+encodeURIComponent(location.href)+'&amp;title='+encodeURIComponent(document.title));return false;";

		// Technorati
		$bookmarks['technorati']['link']  = "http://www.technorati.com/";
		$bookmarks['technorati']['click'] = "window.open('http://technorati.com/faves?add='+encodeURIComponent(location.href));return false;";

		// Yahoo
		$bookmarks['yahoo']['link']  = "http://www.yahoo.com/";
		$bookmarks['yahoo']['click'] = "window.open('http://myweb2.search.yahoo.com/myresults/bookmarklet?t='+encodeURIComponent(document.title)+'&amp;u='+encodeURIComponent(location.href));return false;";

		// Delicious
		$bookmarks['delicious']['link']  = "http://del.icio.us/";
		$bookmarks['delicious']['click'] = "window.open('http://del.icio.us/post?v=2&amp;url='+encodeURIComponent(location.href)+'&amp;title='+encodeURIComponent(document.title));return false;";

		// StumbleUpon
		$bookmarks['stumbleupon']['link']  = "http://www.stumbleupon.com/";
		$bookmarks['stumbleupon']['click'] = "window.open('http://www.stumbleupon.com/submit?url='+encodeURIComponent(location.href)+'&amp;title='+encodeURIComponent(document.title));return false;";

		// Digg
		$bookmarks['digg']['link']  = "http://digg.com/";
		$bookmarks['digg']['click'] = "window.open('http://digg.com/submit?phase=2&amp;url='+encodeURIComponent(location.href)+'&amp;bodytext=tags=title='+encodeURIComponent(document.title));return false;";

		// Facebook
		$bookmarks['facebook']['link']  = "http://www.facebook.com/";
		$bookmarks['facebook']['click'] = "window.open('http://www.facebook.com/sharer.php?u='+encodeURIComponent(location.href)+'&amp;t='+encodeURIComponent(document.title));return false;";

		// Reddit
		$bookmarks['reddit']['link']  = "http://reddit.com/";
		$bookmarks['reddit']['click'] = "window.open('http://reddit.com/submit?url='+encodeURIComponent(location.href)+'&amp;title='+encodeURIComponent(document.title));return false;";

		// Myspace
		$bookmarks['myspace']['link']  = "http://www.myspace.com/";
		$bookmarks['myspace']['click'] = "window.open('http://www.myspace.com/index.cfm?fuseaction=postto&amp;'+'t='+encodeURIComponent(document.title)+'&amp;c=u='+encodeURIComponent(location.href)+'&amp;l=');return false;";

		// Windows live
		$bookmarks['live']['link']  = "http://www.live.com/";
		$bookmarks['live']['click'] = "window.open('https://favorites.live.com/quickadd.aspx?url='+encodeURIComponent(location.href)+'&amp;title='+encodeURIComponent(document.title));return false;";

		// Twitter
		$bookmarks['twitter']['link']  = "http://twitter.com/";
		$bookmarks['twitter']['click'] = "window.open('http://twitter.com/intent/tweet?status='+encodeURIComponent(document.title)+' '+encodeURIComponent(location.href));return false;";

		// Email
		$bookmarks['email']['link']  = "";
		$bookmarks['email']['click'] = "this.href='mailto:?subject='+document.title+'&amp;body='+encodeURIComponent(location.href);";

		return $bookmarks;
	}

}