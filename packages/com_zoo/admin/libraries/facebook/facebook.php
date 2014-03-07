<?php

/*
	Class: Facebook
		Facebook class
*/
class Facebook extends AppHelper {

	public $app;

	public $app_id;

	public $app_secret;

	public $access_token;

	public function __construct($app, $config) {
		$this->app = $app;
		$this->setAppId($config['app_id']);
		$this->setAppSecret($config['app_secret']);
		$this->setAccessToken($config['access_token']);
	}

	public function accessTokenURL() { return 'https://graph.facebook.com/oauth/access_token'; }
	public function authorizeURL()   { return 'https://graph.facebook.com/oauth/authorize'; }
	public function meURL()			 { return 'https://graph.facebook.com/me'; }
	public function graphURL()		 { return 'https://graph.facebook.com'; }

	public function setAppId($app_id) {
		$this->app_id = $app_id;
		return $this;
	}

	public function getAppId() {
		return $this->app_id;
	}

	public function setAppSecret($app_secret) {
		$this->app_secret = $app_secret;
		return $this;
	}

	public function getAppSecret() {
		return $this->app_secret;
	}

	public function setAccessToken($access_token) {
		$this->access_token = $access_token;
		return $this;
	}

	public function getAccessToken() {
		return $this->access_token;
	}

	public function getAuthenticateURL($redirect) {
		return $this->authorizeURL().'?client_id='.$this->getAppId().'&redirect_uri='.urlencode($redirect);
	}
	
	public function getAccessTokenURL($code, $redirect_uri) {
		return 	$this->accessTokenURL().'?client_id='.$this->getAppId().'&client_secret='.$this->getAppSecret().'&code='.$code.'&redirect_uri='.urlencode($redirect_uri);
	}

	public function getCurrentUserProfile() {
		if (!empty($this->access_token)) {
			$url  = $this->meURL() . '?access_token='.$this->access_token;
			$result = $this->app->http->get($url, array('ssl_verifypeer' => false));
			$result = json_decode($result['body']);
			return $result;
		}
		return 0;
	}

	public function getProfile($fb_uid) {
		if (empty($fb_uid)) {
			throw new FacebookException('Facebook unique id missing.');
		}
		$url  = $this->graphURL() . '/'.$fb_uid.'?fields=picture';
		try {
			$result = $this->app->http->get($url, array('ssl_verifypeer' => false));
		} catch (AppException $e) {
			return;
		}
		$result = json_decode($result['body']);
		return $result;
	}

}

/*
	Class: FacebookException
*/
class FacebookException extends AppException {}