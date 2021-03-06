<?php 
/**
 * Mark-a-Spot TwitterController
 *
 *
 *
 * Copyright (c) 2010 Holger Kreis
 * http://www.mark-a-spot.org
 *
 *
 * PHP version 5
 * CakePHP version 1.3
 *
 * @copyright  2010, 2011 Holger Kreis <holger@markaspot.org>
 * @link       http://mark-a-spot.org/
 * @version    1.4 .6
 */


App::import('ConnectionManager','Vendor', 'oauth', array(
		'file' => 'OAuth'.DS.'oauth_consumer.php')
);


class TwitterController extends AppController {
	var $name = 'Twitter';
	var $useTable = false;

	var $uses = array('Marker');

	var $helpers = array(
			'Form', 'Rss', 'Html', 'Javascript', 'Time', 'Text', 'Xml', 'Datum',
			'JsValidate.Validation', 'Session',
			'Media.Media' => array(
					'versions' => array('s', 'xl')
			)
	);

	var $components = array('RequestHandler',
			'Geocoder', 'Notification','Cookie','TwitterService', 'Transaction');

	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(array('connect','callback','index'));
	}



	function admin_reply(){

		if (empty($this->data)) {
			$this->Session->setFlash(__(
					'This marker does not exist.',true), 'default',
					array('class' => 'flash_error'));
			$this->redirect(array('action' => 'index'));
		}

		$this->TwitterService->handleTweets($this->data);
	}

	function admin_index(){

		$this->set('title_for_layout', __('Administrate incoming tweets',true));
		$this->layout = 'default_page';

		$this->Twitter = ConnectionManager::getDataSource('twitter');
		$this->Twitter->account_verify_credentials();


		// Get all Tweets with Mentions

		$response = $this->Twitter->statuses_mentions($params = array(
		 	$params = array())
		);

		// Loop through results and check if Tweet already added or in the Blacklist
		// Plus check for additional hashtag
		if ($response){
			foreach($response as $result) {

				if (stristr($result->text, Configure::read('Twitter.HashGet'))) {
					if (!$this->TwitterService->importTweetCheck($result->id_str)) {

						// Check if Tweet is on ignore-List
						if ($this->TwitterService->ignoreTweetCheck($result->id_str)) {
							$result->ignore = true;
						}

						// write Object
						$newResponse[] = $result;
					}
				}
			}
		}
		if (isset($newResponse)){
			$this->set('response', $newResponse);
		} else {
			$this->set('response', null);
		}

		// prepare Form Helpers to choose category and status for importing tweets

		$categories = $this->Marker->Category->generatetreelist(null, null, null, ' - ');
		$this->set(compact('categories'));

		$statuses = $this->Marker->Status->find('list');
		$this->set(compact('statuses'));

		/*
		 $districts = $this->Marker->District->find('list');
		$this->set(compact('districts'));
		*/

	}


	/**
	 * Connect Mark-a-Spot with Twitter
	 * Makes Use of Neil Crooks
	 * http://github.com/neilcrookes/http_socket_oauth
	 */

	public function connect() {

		// Get a request token from twitter
		App::import('Vendor', 'HttpSocketOauth');
		$sitename = Configure::read('Site.domain');
		$url = 'http://'.$sitename.'/twitter/callback';
		$Http = new HttpSocketOauth();
		$request = array(
				'uri' => array(
						'host' => 'api.twitter.com',
						'path' => '/oauth/request_token',
				),
				'method' => 'GET',	'auth' => array(
						'method' => 'OAuth',
						'oauth_callback' => $url,
						'oauth_consumer_key' => Configure::read('Twitter.consumer_key'),
						'oauth_consumer_secret' => Configure::read('Twitter.consumer_secret'),
				),
		);
		$response = $Http->request($request);
		// Redirect user to twitter to authorize  my application
		parse_str($response, $response);
		//$this->redirect('http://api.twitter.com/oauth/authorize?oauth_token=' . $response['oauth_token']);
		$this->redirect('http://api.twitter.com/oauth/authenticate?oauth_token=' . $response['oauth_token']);
	}

	/**
	 * Connect Mark-a-Spot with Twitter
	 * Makes Use of Neil Crooks
	 * http://github.com/neilcrookes/http_socket_oauth
	 * Method to receave key and secret
	 */

	public function callback() {
		App::import('Vendor', 'HttpSocketOauth');
		$Http = new HttpSocketOauth();
		$this->layout = 'default_page';

		// Issue request for access token
		$request = array(
				'uri' => array(
						'host' => 'api.twitter.com',
						'path' => '/oauth/access_token',
				),
				'method' => 'POST', 'auth' => array(
						'method' => 'OAuth',
						'oauth_consumer_key' => Configure::read('Twitter.consumer_key'),
						'oauth_consumer_secret' => Configure::read('Twitter.consumer_secret'),
						'oauth_token' => $this->params['url']['oauth_token'],
						'oauth_verifier' => $this->params['url']['oauth_verifier'],
				),
		);
		$response = $Http->request($request);
		parse_str($response, $response);

		// if we setup MaS, print oauth_token ans secret for the connecting user
		// Connecting user is logged in Twitter User
		if (Configure::read('Twitter.Setup')){
			$this->set('response',$response);
		}


		// Save data in $response to database or session as it contains
		// the access token and access token secret that you'll need later to interact with the twitter API

		$this->Session->write('Twitter', $response);
		$this->set('twitter', $this->Session->read('Twitter'));
		if (array_key_exists('user_id', $response)) {
			$this->data['User']['email_address'] = $response['user_id']."@markaspot.org";
			$this->data['User']['password'] = $this->Auth->password($response['oauth_token']);
			$this->data['User']['passwd'] = $response['oauth_token'];
			$this->data['User']['nickname'] = $response ['screen_name'];
		} else {

			$this->Session->setFlash(__('Sorry, something went wrong',true), 'default', array(
					'class' => 'flash_success'));
			$this->redirect(array('controller' => 'users','action' => 'login'));
		}

		if (isset($this->data['User']['passwd'])) {
			$this->data['User']['passwdhashed'] = $this->Auth->password($this->data['User']['passwd']);
		}

		// set User non-active and Group for Users
		// move user into their group


		$this->data['Group']['id'] = Configure::read('userGroup.users');
		$this->data['User']['active'] = 1;
		//Save all UserData



		/**
		 * User Sign Up with Twitter Credentials
		 *
		 *
		 */

		if ($this->User->checkUnique($this->data['User']['email_address'], 'email_address')) {
				
			// Sign Up User only if email is not registered yet
				
			$this->User->create($this->data);
				
			if ($this->User->save()) {
				$this->Session->delete('FB');
				$this->Session->setFlash(__(
						'Great, you signed up successfully. Please check your Mail.',true), 'default', array(
								'class' => 'flash_success'));
					
				$this->data['User']['email_address'] = $response['user_id']."@markaspot.org";
				$this->data['User']['password'] = $this->Auth->password($response['oauth_token']);

				if ($this->Auth->login($this->data)) {
					$cookie = array();
					$this->Cookie->write('Auth.User', $cookie, true, '+2 weeks');
					$this->Session->setFlash(__('Great, you signed up successfully with the help of your
							twitter-account',true), 'default', array(
									'class' => 'flash_success'));

					/*
					 if ($this->Auth->user()) {
					$this->redirect(array('controller' => 'markers','action' => 'app'));
					}
					*/
					//$this->redirect(array('controller' => 'markers','action' => 'app'));

				} else {
					echo 'Unable to log you in with the supplied credentials. ';
				}
			}

		} else {

			// howto login user without password
			// 1. Find the User who owns this email_adress (user_id@markaspot.org)
			//

			$earlyBird = $this->User->find('first',array('conditions' => array('User.email_address' => $response['user_id']."@markaspot.org"), 'contain' => false, 'fields' => array ('id')));

			// Now we have the ID and are able to update instead of insert
			$this->data['User']['id'] = $earlyBird['User']['id'];

			// Generate new password out of oauth-token
			$this->data['User']['password'] = $this->Auth->password($response['oauth_token']);
			$this->User->id = $this->data['User']['id'];

			// Saving new password and Login with this data
			if ($this->User->saveField('password', $this->data['User']['passwdhashed'])) {
				if ($this->Auth->login($this->data)) {
					$this->Session->delete('FB');
						
					$cookie = array();
						
					$this->Session->setFlash(__('Great, you logged in successfully with the help of your
							twitter-account',true), 'default', array(
									'class' => 'flash_success'));
					$this->Cookie->write('Auth.User', $cookie, true, '+2 weeks');

					//Unfortunateley redirect does not work as expected: User wont be logged in
					//
					//$this->redirect(array('controller' => 'markers','action' => 'app','id' => $this->Auth->User('id')));

				} else {
					echo 'Unable to log you in with the supplied credentials. ';
				}
				//$this->redirect(array('controller' => 'markers', 'action'=>'add'));

			}

		}

	}





}
?>