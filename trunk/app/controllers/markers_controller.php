<?php
/**
 * Mark-a-Spot Marker Controller
 *
 * Everything about controlling markers
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
 * @version    1.6.0
 */

class MarkersController extends AppController {

	var $name = 'Markers';
	var $helpers = array(
			'Session', 'Rss', 'Html', 'Javascript', 'Time',
			'Text', 'Xml', 'Datum', 'JsValidate.Validation', 'Htmlcleaner',
			'Media.Media' => array(
					'versions' => array(
							's', 'xl'
					)
			),
			'Csv'
	);
	var $paginate = array(
			'limit' => 10,
			'contain' => array(
					'Category', 'Status', 'User'),
			'order' => array(
					'Marker.modified' => 'desc')
	);
	var $components = array('RequestHandler', 'Geocoder', 'Cookie', 'Notification', 'Transaction');


	/**
	 * Splash page
	 *
	 */

	function index() {
		// check for mobile devices
		if (!$this->mobileLayout) {
			$this->layout = 'default_splash';
		}
		$this->set('title_for_layout', __("Welcome to Geo'n'rate",true));


		$this->set("CSS", "styles");
		$this->set('markers', Sanitize::clean($this->Marker->publish($this->Marker->find('all', array(
				'contain' => array('Category', 'Status', 'User', 'Transaction'),
				'fields' => array('Marker.id', 'Marker.subject', 'Marker.status_id', 'Marker.description', 'Marker.lat', 'Marker.lon', 'Marker.status_id', 'Marker.modified', 'Category.name',
						'Status.id', 'Status.name', 'Category.Hex', 'Status.hex', 'User.nickname'),
				'conditions' => array(
						'Marker.status_id >=' => $this->statusCond),
				//'limit' => '3',
				'order' => 'Marker.modified DESC')))
		)
		);


		// get ids of all published markers
		$this->set('markersPublished', Sanitize::clean($this->Marker->publish($this->Marker->find('all', array(
				'fields' => array('Marker.id', 'Marker.status_id'),
				'conditions' => array(
						'Marker.status_id >=' => $this->statusCond),
				'order' => 'Marker.modified DESC')))
		)
		);

		// get three attachments


		$attachments = $this->Marker->Attachment->find('all',array(
				'fields' => array (
						'id', 'dirname', 'foreign_key','basename'),
				//'limit' => '3',
				'order' => 'created ASC',)
		);


		$this->set('attachments', $attachments);
	}
	function admin_index() {
		$this->redirect(array('action' => 'liste', 'admin' => null));
	}

	function index_markers() {
		$this->redirect('http://'.Configure::read('Site.domain'));
	}

	/**
	 * Main application
	 * Markers are called separately by JSON, we only need category and status
	 *
	 */


	function app() {
		if (!$this->mobileLayout) {
			$this->layout = 'default_marker';
		}

		$this->set('title_for_layout', __('Map and List View',true));


		$categoriesTree = $this->Marker->Category->generatetreelist($conditions=null, $keyPath=null, $valuePath = null , $spacer= '_ ', $recursive=-1);
			
		foreach ($categoriesTree as $id => $value) {
			$this->Marker->Category->id = $id;
			$value .= ";".$this->Marker->Category->field('Hex');
			$categories[]= array($id => $value);
		}

		$this->set('categories', $categories);

		$this->Marker->Status->recursive = -1;
		$statuses = $this->Marker->Status->find('all',array(
				'conditions' => array('id >=' => $this->statusCond)));
		$this->set('statuses',$statuses);

		$this->District->recursive = -1;
		$this->set('districts', $this->District->find('all'));
		$this->set('markers', $this->paginate());
	}

	/**
	 *
	 * Create feed
	 *
	 */
	
	
	function hotspots() {
		// disable layout
		$this->layout = 'ajax';
		// set correct mimetype
		$this->RequestHandler->respondAs('application/javascript');
		// prepare query parameters
		$params = array('fields' => array('Marker.id', 'Marker.category_id', 'Marker.lat', 'Marker.lon', 'Marker.city', 'Marker.street'));
		// retrieve markers from DB according to the parameters
		$markers = $this->Marker->find('all', $params);
		// prepare query parameters
		// count hotspot of the current user
		$params = array('conditions' => array('Marker.user_id' => $this->Session->read('username_caspur')));
		// do count query
		$count = $this->Marker->find('count', $params);
		// pass info to view
		$this->set('markers', $markers);
		$this->set('count', $count);
	}


	/**
	 * Personal List View
	 *
	 *
	 */
	function myliste() {
		$this->layout = 'default_page';
		$this->set('title_for_layout', __('Markers in a list',true));

		// Create Header h2

		if ($this->params['named']['status']) {
			$condition = array('Marker.status_id' => $this->params['named']['status']);
			$this->Marker->Status->recursive = -1;
			$this->set('h2Status', $this->Marker->Status->read('Name', $this->params['named']['status']));

		} elseif ($this->params['named']['category']) {
			$condition = array('Marker.category_id' => $this->params['named']['category']);
			$this->Marker->Category->recursive = -1;
			$this->set('h2Cat', $this->Marker->Category->read('Name', $this->params['named']['category']));
		}

			
		$this->Marker->Category->recursive = -1;
		$categories = $this->Marker->Category->find('all');
		$this->set('categories',$categories);

		$this->Marker->Status->recursive = -1;
		$statuses = $this->Marker->Status->find('all');
		$this->set('statuses',$statuses);

		$this->Marker->recursive = 0;
		$this->set('markers', $this->Marker->publish($this->paginate(null, $condition)));
	}


	/**
	 * Accessible List 
	 *
	 *
	 */

	function liste() {
		if (isset($this->params['named']['mine'])) {
			$condition = array('Marker.user_id'=>$this->Session->read('username_caspur'));//$this->Auth->user('id'));
		} else {
			$condition = array('Marker.status_id >=' => $this->statusCond);

		}

		// check for mobile devices
		if (!$this->mobileLayout) {
			$this->layout = 'default_page';
		}

		$this->set('title_for_layout', __('Markers in a list',true));

		$categories = $this->Marker->Category->generatetreelist($conditions=null, $keyPath=null, $valuePath=null, $spacer= '_ ', $recursive=null);
		$this->set('categories', $categories);
			
		$this->Marker->Status->recursive = -1;
		$statuses = $this->Marker->Status->find('all', array(
				'conditions' => array('id >=' => $this->statusCond)
		)
		);
		$this->set('statuses',$statuses);

		$this->Marker->recursive = 0;
		$this->set('markers',
				$this->Marker->publish($this->paginate(
						null, $condition,array('order' => array('Marker.created' => 'desc'))

				)
				)
		);

	}

	/**
	 * Paginated list view
	 *
	 *
	 */
	function ajaxList() {
		if ($this->RequestHandler->isAjax()) {
			// Reading Status names for heading
			if (isset($this->params['named']['status'])) {
				$condition = array('Marker.status_id' => $this->params['named']['status']);

				$this->Marker->Status->recursive = -1;
				$this->set('status', $this->Marker->Status->read('Name', $this->params['named']['status']));
					
			} elseif (isset($this->params['named']['category'])) {
				$condition = array('OR' => array(
						'Marker.category_id' => $this->params['named']['category'],
						'Category.parent_id' => $this->params['named']['category'])
				);

				$this->Marker->Category->recursive = -1;
				$this->set('category', $this->Marker->Category->read('Name', $this->params['named']['category']));
			}
				
			if (isset($this->params['named']['category'])){
				$this->set('getIdCategory', $this->params['named']['category']);
			}
				
			if (isset($this->params['named']['status'])) {
				$this->set('getIdStatus', $this->params['named']['status']);
			}
				
				
			$categories = $this->Marker->Category->generatetreelist($conditions=null, $keyPath=null, $valuePath=null, $spacer= '_ ', $recursive=null);
			$this->set('categories', $categories);
				
			$this->Marker->Status->recursive = -1;
			$statuses = $this->Marker->Status->find('all', array(
					'conditions' => array('id >=' => $this->statusCond)
			)
			);
				
			$this->set('statuses',$statuses);

			if (isset($condition)) {
				$this->set('markers', $this->Marker->publish($this->paginate(null, array(
						$condition))
				)
				);
			} else {
				$this->set('markers', $this->Marker->publish($this->paginate(null, array(
						'Marker.status_id >=' => $this->statusCond))
				)
				);
			}
		} else {
			die;
		}
	}

	/**
	 * Paginates list view / user
	 *
	 */
	function ajaxmylist() {
		$condition = '';

		// Reading Status names for heading
		//print_r($this->params['named']['status']);
		$condition = array('Marker.user_id'=>$this->Session->read('username_caspur'));//$this->Auth->user('id'));
		if (isset($this->params['named']['status'])) {
			$condition = array(
					'Marker.status_id' => $this->params['named']['status'],
					'Marker.user_id'=>$this->Session->read('username_caspur')//$this->Auth->user('id')
			);
			$this->Marker->Status->recursive = -1;
			$this->set('status', $this->Marker->Status->read('Name', $this->params['named']['status']));

		} elseif (isset($this->params['named']['category'])) {
			$condition = array('OR' => array(
					'Marker.category_id' => $this->params['named']['category'],
					'Category.parent_id' => $this->params['named']['category']),
					'Marker.user_id'=>$this->Session->read('username_caspur'));//$this->Auth->user('id'));
			$this->Marker->Category->recursive = -1;
			$this->set('category', $this->Marker->Category->read('Name', $this->params['named']['category']));
		}

		if (isset($this->params['named']['category'])){
			$this->set('getIdCategory', $this->params['named']['category']);
		}

		if (isset($this->params['named']['status'])) {
			$this->set('getIdStatus', $this->params['named']['status']);
		}

		$categories = $this->Marker->Category->generatetreelist($conditions=null, $keyPath=null, $valuePath=null, $spacer= '_ ', $recursive=null);
		$this->set('categories', $categories);
			

		$this->Marker->Status->recursive = -1;
		$statuses = $this->Marker->Status->find('all');
		$this->set('statuses',$statuses);
		if ($this->RequestHandler->isAjax()) {
			$this->set('markers', $this->Marker->publish($this->paginate(null, array($condition))));
			$this->render('ajaxlist');
		}
	}



	/**
	 * Add Markers the normal way (pre-registration is needed)
	 *
	 */
	function add() {
		if (!$this->mobileLayout) {
			$this->layout = 'default_page';
		}

		$this->set('title_for_layout', __('Add Marker',true));
		
		if (!empty($this->data)) {

			$address  = $this->data['Marker']['street'];
			$address .= ' '.$this->data['Marker']['zip'];
			$address .= ' '.$this->data['Marker']['city'];
				
			$latlng = $this->Geocoder->getLatLng($address);
			
			$this->data['Marker']['lat'] = $latlng['lat'];
			$this->data['Marker']['lon'] = $latlng['lng'];
				
			$this->data['Marker']['user_id'] = $this->Session->read('username_caspur');//$this->Auth->user('id');
			$this->data['Marker']['status_id'] = 1;
			$this->data['Marker']['feedback'] =  Configure::read('Publish.Feedback');

			//$this->set('markers', $this->Marker->publish($this->paginate(null, $condition,array('order' => array('Marker.created' => 'desc')))));

			if ($this->Marker->saveAll($this->data)) {
					
				$this->Session->setFlash(
						sprintf(__('The Marker ID# %s has been saved.',true),
								substr($this->Marker->id, 0, 8)),
						'default',array('class' => 'flash_success'));

				$this->Transaction->log($this->Marker->id);

				// Now read E-Mail Adress which is assigned to category (just saved in form)
				$categoryId = $this->data['Marker']['category_id'];

				$categoryUserId = $this->Marker->Category->read(array('user_id'),$categoryId);
				$catUserId = $categoryUserId['Category']['user_id'];

				if ($catUserId != ""){
					$recipient = $this->User->field('email_address',array('id =' => $catUserId));
				} else {
					$stringOfAdmins = implode(',', $this->_getAdminMail());
					$recipient = $stringOfAdmins;
				}

				//
				// call Notification Component and send mail to all Admins
				//

				$nickname = $this->Auth->user('nickname');


				$cc[] = "";
				$this->Notification->sendMessage("markerinfoadmin",$this->Marker->id, $nickname, $recipient,$cc);


				//
				// send confirmation Mail to user (if not logged in by twitter or FB)
				//
				$recipient = $this->Auth->user('email_address');
				$bcc = "";
				if (!$this->Session->read('FB') && !$this->Session->read('Twitter')) {
					$this->Notification->sendMessage("markeradd",$this->Marker->id, $nickname, $recipient,$bcc);
				}
				$id = $this->Marker->id;

				$this->redirect(array('controller' => 'markers', 'action' => 'liste', 'admin' => false, 'mine' => true));
				//$this->redirect(array('controller' => 'markers', 'action' => 'preview', $id));



			} else {
					
				$this->Session->setFlash(__('This marker could not be saved.',true),
						'default', array('class' => 'flash_error'));
			}
		}

		$this->data['Marker']['street'] = $this->Session->read('addAdress.street');
		$this->data['Marker']['zip'] = $this->Session->read('addAdress.zip');
		$this->data['Marker']['city'] = $this->Session->read('addAdress.city');

		$this->set('data',$this->data);

		//$categories = $this->Marker->Category->find('threaded', array('contain' => array('id, hex')));
		//$this->set(compact('categories'));

		$categories = $this->Marker->Category->generatetreelist($conditions=null, $keyPath=null, $valuePath=null, $spacer= '- ', $recursive=null);
		$this->set(compact('categories'));

		$districts = $this->Marker->District->find('list');
		$this->set(compact('districts'));
		$statuses = $this->Marker->Status->find('list');
		$this->set(compact('status'));
	}



	/**
	 * Add Markers directly (included-registration)
	 *
	 */

	function startup() {
		// check for login to redirect to add
			
		if ($this->Auth->user('id')) {
			$this->redirect(array('controller'  => 'markers', 'action' => 'add'));
		}
		// check for mobile devices

		if (!$this->mobileLayout) {
			$this->layout = 'default_page';
		}

		$this->set('title_for_layout', __('Add Marker',true));

		if (!empty($this->data)) {
				
			if (isset($this->data['User']['passwd'])) {
				$this->data['User']['passwdhashed'] = $this->Auth->password($this->data['User']['passwd']);
			}

			// set User non-active and Group for Users

			$this->data['User']['active'] = 0;
			$this->data['Group']['id'] = Configure::read('userGroup.users');
				
			//Save all UserData
			$this->User->create();
				
			if ($this->User->save($this->data)) {
				$address  = 	$this->data['Marker']['street'];
				$address .= ' '.$this->data['Marker']['zip'];
				$address .= ' '.$this->data['Marker']['city'];

				$latlng = $this->Geocoder->getLatLng($address);

				//all marker's stuff
				//$this->data['Marker']['city'] = Configure::read('Gov.town');
				$this->data['Marker']['lat'] = $latlng['lat'];
				$this->data['Marker']['lon'] = $latlng['lng'];

				$this->data['Marker']['user_id'] = $this->User->id;
				$this->data['Marker']['status_id'] = 0;
				$this->data['Marker']['feedback'] =  Configure::read('Publish.Feedback');

				// If the user was saved, Now we add this information to the data
				// and save the Profile.

				// The ID of the newly created user has been set
				// as $this->User->id.
				$this->data['Profile']['user_id'] = $this->User->id;

				// Because our User hasOne Profile, we can access
				// the Profile model through the User model:
				$this->User->Profile->save($this->data);



				// now unbind user-relation in order to save
				// $this-data with attachment
				$this->Marker->unbindModel(array('belongsTo' => array('User')));

				if ($this->Marker->saveAll($this->data)) {
						
					//$this->Attachement->save($this->data['Attachment']);
						
					$this->Session->setFlash(sprintf(
							__('The Marker ID# %s has been saved. Please check your e-mail.',true),
							substr($this->Marker->id, 0, 8)), 'default',array(
									'class' => 'flash_success_modal')
					);
						
					$this->Transaction->log($this->Marker->id);
						
					// send confirmation Mail with confirmation Link
					// plus preview_link
					$recipient = $this->data['User']['email_address'];
					$cc[] = "";
					$this->Notification->sendMessage("welcome",
							$this->Marker->id, $this->data['User']['nickname'], $recipient, $cc);

					//
					// call Notification Component and send mail to all Admins
					//
					$recipient = "";
						
					// Now read E-Mail Adress which is assigned to category (just saved in form)
					$categoryId = $this->data['Marker']['category_id'];
					$categoryUserId = $this->Marker->Category->read(array('user_id'),$categoryId);
					$catUserId = $categoryUserId['Category']['user_id'];
						
					if ($catUserId != ""){
						$recipient = $this->User->field('email_address',array('id =' => $catUserId));
					}
					else {
						$stringOfAdmins = implode(',', $this->_getAdminMail());
						$recipient = $stringOfAdmins;
					}
						
					//
					// call Notification Component and send mail to all Admins
					//

					$cc[] = "";
					$this->Notification->sendMessage("markerinfoadmin",
							$this->Marker->id, $nickname=null, $recipient,$cc);

					$this->redirect(array(
							'controller'  => 'markers', 'action' => 'preview', $this->Marker->id));


				} else {
					$this->Session->setFlash(__('This marker could not be saved.',true), 'default', array(
							'class' => 'flash_error'));
					$this->User->delete($this->data['Marker']['user_id']);

				}
				$this->data['User']['passwd'] =null;
				$this->data['User']['password'] =null;
			}
		}


		$categories = $this->Marker->Category->generatetreelist($conditions=null, $keyPath=null, $valuePath=null, $spacer= '- ', $recursive=null);
		$this->set(compact('categories'));

		$districts = $this->Marker->District->find('list');
		$this->set(compact('districts'));

		$statuses = $this->Marker->Status->find('list');
		$this->set(compact('status'));

		$this->data['User']['passwd'] = null;
		$this->data['User']['password'] = null;

		$this->data['Marker']['street'] = $this->Session->read('addAdress.street');
		$this->data['Marker']['zip'] = $this->Session->read('addAdress.zip');
		$this->data['Marker']['city'] = $this->Session->read('addAdress.city');

	}


	/**
	 * view details
	 *
	 */
	function view($id = null) {
		// check for mobile devices
		if (!$this->mobileLayout) {
			$this->layout = 'default_page';
		}

		$this->set('title_for_layout', 'Dettaglio Hot Spot');
		$data = $this->Marker->publishRead($this->Marker->read(array(
				'Marker.id', 'Marker.subject', 'Marker.category_id','Marker.user_id','Marker.status_id',
				'Marker.lat', 'Marker.lon', 'Marker.rating', 'Marker.feedback',
				'Marker.votes','Marker.media_url','Marker.created', 'Marker.modified',
				'Marker.zip','Marker.street', 'Marker.city', 'Marker.description', 'Category.name',
				'Status.id', 'Status.name', 'Category.hex', 'Status.hex','User.nickname', 'User.email_address'),
				$id)
		);

		$this->set('marker', $data);

		// check for community flag in Profiles
		$showMail = $this->User->Profile->field('community', array(
				'user_id' => $data['Marker']['user_id']));
		$this->set('showMail', $showMail);

		if (!$id || empty($data)) {
			$this->Session->setFlash(__('This marker does not exist.',true),
					'default', array('class' => 'flash_error'));
			$this->redirect(array('controller'  => 'markers', 'action' => 'app'));
		} else {
			$this->Transaction->log($id);
		}

		$undo_rev = $this->Marker->Previous();
		$users = $this->Marker->User->find('list');
		$this->set(compact('undo_rev', 'users'));

		// call count views from transaction
		$this->set('views', $this->Marker->Transaction->getViews($id));

		// call count comments from transaction
		$this->set('commentSum', $this->Marker->Comment->getComments($id));

		// call history without views
		$this->set('history', $this->Marker->Transaction->getHistory($id));

		$statuses = $this->Marker->Status->find('all');
		$this->set('statuses',$statuses);

		$this->set('mathCaptcha', $this->MathCaptcha->generateEquation());

		// read data from comments_controller session->write (in case of wrong calculation)
		$this->data = $this->Session->read('formdata');

	}


	/**
	 * preview marker after startup
	 *
	 */
	function preview($id = null) {
		// check for mobile devices
		if (!$this->mobileLayout) {
			$this->layout = 'default_page';
		}

		$this->set('title_for_layout', 'Preview marker');
		$data = $this->Marker->publishRead($this->Marker->read(array(
				'Marker.id', 'Marker.subject', 'Marker.category_id','Marker.user_id','Marker.status_id',
				'Marker.lat', 'Marker.lon', 'Marker.rating', 'Marker.feedback',
				'Marker.votes','Marker.media_url','Marker.created', 'Marker.modified',
				'Marker.zip','Marker.street', 'Marker.city','Marker.description','Category.name',
				'Status.id', 'Status.name', 'Category.hex', 'Status.hex','User.nickname'),
				$id)
		);

		$data = $this->Marker->publishRead($this->Marker->read(null, $id));
		$this->set('marker', $data);

		// check for community flag in Profiles
		$showMail = $this->User->Profile->field('community', array(
				'user_id' => $data['Marker']['user_id']));
		$this->set('showMail', $showMail);

		if (!$id || empty($data)) {
			$this->Session->setFlash(__('This marker does not exist.',true),
					'default',
					array('class' => 'flash_error'));
			$this->redirect(array('controller'  => 'markers', 'action' => 'app'));
		}
		//$marker = $this->Marker->read();
		$undo_rev = $this->Marker->Previous();
		$history = $this->Marker->revisions();
		$users = $this->Marker->User->find('list');
		$this->set(compact('undo_rev', 'history', 'users'));
		$this->set('mathCaptcha', $this->MathCaptcha->generateEquation());
		$this->data = $this->Session->read('formdata');
	}


	/**
	 * administrate markers as admin
	 *
	 */


	function admin_edit($id = null, $version_id = null) {
		$this->set('title_for_layout', __('Edit marker',true));
		$this->layout = 'default_page';

		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('This marker does not exist.',true), 'default', array(
					'class' => 'flash_error'));
			$this->redirect(array('action' => 'index'));
		}

		if (!empty($this->data)) {
				
			$address  = $this->data['Marker']['street'];
			$address .= ' '.$this->data['Marker']['zip'];
			$address .= ' '.Configure::read('Gov.town');
				
			$latlng = $this->Geocoder->getLatLng($address);
			$this->data['Marker']['lat'] = $latlng['lat'];
			$this->data['Marker']['lon'] = $latlng['lng'];

			$this->data['Comment'][0]['status'] = 1;
			$this->data['Comment'][0]['group_id'] = Configure::read('userGroup.admins');
			$this->data['Comment'][0]['user_id'] = $this->Auth->user('id');
			$this->data['Comment'][0]['comment'] = $this->data['Marker']['admincomment'];

			if ($this->Marker->saveAll($this->data,array('validate'=>'false'))) {
				$id = $this->Marker->id;
				$this->Session->setFlash(__('Der Hinweis wurde gespeichert!',true), 'default',array(
						'class', 'flash_success'));
					
				// Log this transaction, controller, action, ip, user_id and current status
				$this->Transaction->log($id);


				// send confirmation Mail
				// read User data first

				$client = $this->Marker->read(null, $id);
					
				// tweet Status

				if ($this->data['Marker']['twitter'] == 1) {
						
					$this->Notification->tweetStatus($this->Marker->id);
					$this->Session->setFlash(sprintf(__(
							'The Status has been tweeted.',true),$this->Marker->id),
							'default',	array('class' => 'flash_success'));
				}

				if ($this->data['Marker']['notify'] == 1) {
					// send mail to user
					$recipient = $client['User']['email_address'];
					$cc[] = "";
					$this->Notification->sendMessage("update", $this->Marker->id, $nickname=null, $recipient,$cc);

					$this->Session->setFlash(sprintf(__(
							'The Marker ID# %s has been saved. The user will be notified.',true),
							substr($this->Marker->id, 0, 8)), 'default', array(
									'class' => 'flash_success')
					);
					$this->redirect($this->referer(), null, true);

				} else {
					// just save
					$this->Session->setFlash(sprintf(__(
							'The Marker ID# %s has been saved.',true),substr($this->Marker->id, 0, 8)),
							'default',	array('class' => 'flash_success'));
					$this->redirect($this->referer(), null, true);

				}

			} else {
				$this->Session->setFlash(__(
						'This marker could not be saved.',true),
						'default', array('class', 'flash_error'));
			}
		}

		$categories = $this->Marker->Category->generatetreelist(null, null, null, ' - ');
		$this->set(compact('categories'));

		$districts = $this->Marker->District->find('list');
		$this->set(compact('districts'));

		$statuses = $this->Marker->Status->find('list');
		$this->set(compact('statuses'));

		if (empty($this->data)) {
			//$this->data = $this->Marker->read(null, $id);
			if (is_numeric($version_id)) {
				$this->data = $this->Marker->shadow('first',array('conditions' => array('version_id' => $version_id)));
			} else {
				$this->data = $this->Marker->read(null,$id);
			}
		}

		$this->set('marker', $this->Marker->read(null, $id));
		$undo_rev = $this->Marker->Previous();
		$history = $this->Marker->revisions();
		$users = $this->Marker->User->find('list');
		$this->set(compact('undo_rev', 'history', 'users'));


		// call count views from transaction
		$this->set('views', $this->Marker->Transaction->getViews($id));

		// call count comments from transaction
		$this->set('commentSum', $this->Marker->Comment->getComments($id));

		// call history without views
		$this->set('history', $this->Marker->Transaction->getHistory($id));

		$statusLogs = $this->Marker->Status->find('all');
		$this->set('statusLogs',$statusLogs);

		// Comment-Administration
		// overwrite condition status = 1

		$this->Marker->Comment->recursive = -1;
		$this->set('comments', $this->Marker->Comment->find('all', array(
				'conditions' =>	array(
						'marker_id' => $id), 'status => 0')
		)
		);

		if (empty($this->data)) {
			$this->data = $this->Marker->read(null, $id);
		}
	}


	/**
	 * edit markers as user
	 *
	 */
	function edit($id = null,$version_id = null) {
		$this->Marker->id = $id; //important for read,shadow and revisions
		$this->set('title_for_layout', 'Edit marker');
		$this->layout = 'default_page';

		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__(
					'This marker does not exist.',true), 'default',
					array('class' => 'flash_error'));
			$this->redirect(array('action' => 'index'));
		}

		if (!empty($this->data)) {
			//
			// incase of use of tinmce:
			// $this->data['Marker']['description'] =  str_replace("\n", "", $this->data['Marker']['description']);
			//
			$address  = $this->data['Marker']['street'];
			$address .= ' '.$this->data['Marker']['zip'];
			$address .= ' '.Configure::read('Gov.town');
			$latlng = $this->Geocoder->getLatLng($address);
			$this->data['Marker']['lat'] = $latlng['lat'];
			$this->data['Marker']['lon'] = $latlng['lng'];

			if ($this->Marker->saveAll($this->data)) {

				// Log this transaction, controller, action, ip, user_id and current status
				$this->Transaction->log($this->Marker->id);

				$this->Session->setFlash(__('This marker has been saved.',true), 'default', array(
						'class' => 'flash_success')
				);

				$this->redirect($this->referer(), null, true);

			} else {
				$this->Session->setFlash(__('This marker could not be saved.',true), 'default', array(
						'class' => 'flash_error')
				);
			}
		}

		$categories = $this->Marker->Category->generatetreelist(null, null, null, ' - ');
		$this->set(compact('categories'));

		$districts = $this->Marker->District->find('list');
		$this->set(compact('districts'));

		$statuses = $this->Marker->Status->find('list');
		$this->set(compact('statuses'));

		if (empty($this->data)) {

			if (is_numeric($version_id)) {
				$this->data = $this->Marker->shadow('first',array(
						'conditions' => array('version_id' => $version_id)
				)
				);
			} else {
				$this->data = $this->Marker->read(null,$id);
			}
		}

		$this->set('marker', $this->Marker->read(null, $id));
		$undo_rev = $this->Marker->Previous();
		$history = $this->Marker->revisions();

		$users = $this->Marker->User->find('list');
		$this->set(compact('undo_rev', 'history', 'users'));

		if (empty($this->data)) {
			$this->data = $this->Marker->read(null, $id);

		}
	}



	function geoSave($id = null) {
		$this->layout = 'ajax';

		if ($this->params['pass'][0] != "undefined") {
			$id = $this->params['pass'][0];
		} else {
			exit;
		}

		$this->data['Marker']['street'] = $this->params['named']['street'];
		$this->data['Marker']['zip'] = $this->params['named']['zip'];
		$this->data['Marker']['city'] = $this->params['named']['city'];

		$fields = array($this->data['Marker']['lat'],$this->data['Marker']['lon']);
		$this->Marker->id = $id;

		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('This marker does not exist.',true), 'default', array(
					'class' => 'flash_error')
			);
			$this->redirect(array('action' => 'index'));
		}

		if (!empty($this->data)) {

			if ($this->RequestHandler->isAjax()) {

				if ($this->Marker->saveField('lat', $this->params['pass'][1], $validate = false))
					$this->set(flash_success_1, 'lat ok');

				if ($this->Marker->saveField('lon', $this->params['pass'][2], $validate = false))
					$this->set(flash_success_2, 'lon ok');

				if ($this->Marker->saveField('zip', $this->data['Marker']['zip'], $validate = false))
					$this->set(flash_success_3, 'PLZ ok');

				if ($this->Marker->saveField('city', $this->data['Marker']['city'], $validate = false))
					$this->set(flash_success_4, 'Stadt ok');

				if ($this->Marker->saveField('street', $this->data['Marker']['street'], $validate = false)) {
					$this->set(flash_success_5, 'Straße ok');
						
				}

				$this->Transaction->log($id);

			}
		}
	}


	/**
	 * get id for star-rating
	 *
	 */
	function mapRate($id = null) {
		$this->layout = 'ajax';
		$this->set('id',$id=$this->params['url']['id']);
	}

	/**
	 * undo last change for users
	 *
	 */
	function undo($id = null) {
		$this->Marker->id = $id;
		$this->Marker->undo();
		$this->redirect($this->referer());

		//$this->redirect(array('action' => 'view',$id));
	}


	/**
	 * versioning for admins
	 *
	 */
	function makeCurrent($id, $version_id) {
		$this->Marker->id = $id;
		$this->Marker->revertTo($version_id);
		$this->redirect(array('action' => 'view',$id));
	}


	/**
	 * delete markers
	 *
	 */
	function delete($id) {
		$this->layout = 'ajax';

		if ($this->RequestHandler->isAjax()) {
				
			if ($this->Marker->delete($id, $cascade = true)) {
				echo 'flash_success';
			} else {
				echo 'fail';
			}
			$this->autoRender = false;
			exit();
		} else {

			if ($this->Marker->delete($id, $cascade = true)) {
				//Cache::cacheClear();
				$this->Session->setFlash(__('Marker has been deleted', true));

				$this->redirect(array('controller'  => 'markers', 'action' => 'liste'));
			}
		}
	}


	/**
	 * create JSON Object or XML [ change: layout and function xml($id = null) { ]
	 *
	 */
	function geojson($id = null) {
		$this->layout = 'ajax';
		// Um find('all') ohne Kommentare auszuführen: Unbind!
		//$this->Marker->unbindModel(array('hasMany' => array('Comment', 'Transaction')));

		if (!isset($this->params['pass'][0]) && $this->params['named']['category'] == "undefined"
				&& $this->params['named']['status'] == "undefined") {
			//$this->set('votes', $this->Rating->find('count'));
			$this->set('markers', Sanitize::clean($this->Marker->publish($this->Marker->find('all', array(
					'contain' => array('Category','Status','Attachment'),
					'fields' => array('Marker.id', 'Marker.subject', 'Marker.status_id', 'Marker.description', 'Marker.street', 'Marker.lat', 'Marker.lon',
						 'Marker.rating', 'Marker.votes', 'Marker.media_url', 'Category.Name',
							'Status.id', 'Status.Name', 'Category.Hex', 'Status.Hex'),
					'conditions' => array(
							'Marker.status_id >=' => $this->statusCond,
							'Marker.lat >' => '0.000000')
			)
			)
			)
			)
			);
		}
		// Markers by Cat
		elseif (!isset($this->params['pass'][0]) && $this->params['named']['category'] != "undefined"
		 	&& $this->params['named']['status'] == "undefined") {
			$this->set('markers', Sanitize::clean($this->Marker->publish($this->Marker->find('all', array(
					'contain' => array('Category','Status','Attachment'),
					'fields' => array('Marker.id', 'Marker.subject', 'Marker.status_id', 'Marker.description', 'Marker.lat', 'Marker.lon',
							'Marker.rating', 'Marker.votes', 'Marker.media_url', 'Category.Name',
					 	'Status.id', 'Status.Name', 'Category.Hex', 'Status.Hex'),
					'conditions' => array('OR' => array(
							'Marker.category_id' => $this->params['named']['category'],
							'Category.parent_id' => $this->params['named']['category']),
							'Marker.status_id >=' => $this->statusCond,
							'Marker.lat >' => '0.000000')
			)
			)
			))
			);

			/**
			 *
			 *  Markers by Status
			 *
			 */
		} elseif (!isset($this->params['pass'][0]) && $this->params['named']['status'] != "undefined"
				&& $this->params['named']['category'] == "undefined") {

			$this->set('markers', Sanitize::clean($this->Marker->publish($this->Marker->find('all', array(
					'contain' => array('Category','Status','Attachment'),
					'fields' => array('Marker.id', 'Marker.subject', 'Marker.status_id', 'Marker.description', 'Marker.lat', 'Marker.lon',
							'Marker.rating', 'Marker.votes', 'Marker.media_url', 'Category.Name',
					 	'Status.id', 'Status.Name', 'Category.Hex', 'Status.Hex'),
					'conditions' => array(
								
							'Marker.status_id' => $this->params['named']['status'],
							'Marker.status_id >=' => $this->statusCond,
							'Marker.lat >' => '0.000000')
			)
			)
			)
			));

		}

		/**
		 *
		 * Single Call for Single Marker if ID is set
		 *
		 */
		elseif (isset($this->params['pass'][0])) {
			$this->set('votes', $this->Rating->find('count'));

			$this->set('markers', Sanitize::clean($this->Marker->publishReadJson($this->Marker->find('all', array(
					'contain' => array('Category','Status','Attachment'),
					'fields' => array(
							'Marker.id', 'Marker.subject', 'Marker.status_id', 'Marker.description', 'Marker.lat', 'Marker.lon', 'Marker.rating', 'Marker.votes', 'Marker.media_url', 'Category.Name',
							'Status.id', 'Status.Name', 'Category.Hex', 'Status.Hex'),
					'conditions' => array(
							'Marker.id' => $this->params['pass']['0'],
							'Marker.status_id >=' => $this->statusCond,
							'Marker.lat >' => '0.000000')
			)
			)
			)
			));
		}
	}


	/**
	 *
	 * AJAX Call for all votes to create charts
	 *
	 */

	function ratesum() {
		$this->layout = 'ajax';
		$this->set('votes', $this->Rating->find('count'));
	}


	/**
	 *
	 * Create feed
	 *
	 */

	function rss() {
		$this->set('markers',
				$this->Marker->find('all', array(
						'order' => array('Marker.modified DESC'),
						'limit' => 15, //int
						'conditions' => array('Marker.status_id >=' => 1)
				)
				)
		);
		$this->set('channel', array(
				'title' => Configure::read('Site.domain'),
				'description' => __('Managing Concerns of Public Space', true),
				'link' => '/rss',
				'url' => '/rss',
				'language' => 'de'
		)
		);
		$this->set(compact('markers'));
		$this->RequestHandler->respondAs('rss');
		$this->viewPath .= '/rss';
		$this->layoutPath = 'rss';
	}


	function infolast(){

		$info['markerSumAll'] = $this->Marker->find('count', array(
				'conditions' => array('status_id >=' => '1')
		)
		);

		$date = date('Y-m-d', strtotime("-30 day"));
		$info['markerSumAllCurrent'] = $this->Marker->find('count', array(
				'conditions' => array('AND'=> array(
						'status_id >=' => '1', 'Marker.modified >=' => $date)
				)
		)
		);

		$info['markerSumOpen'] = $this->Marker->find('count', array(
				'conditions' => array('status_id >=' => '1', 'status_id <=' => '2')
		)
		);
		$info['markerSumClosed'] = $this->Marker->find('count', array(
				'conditions' => array('AND'=> array(
						'status_id =' => '2', 'status_id =' => '3','status_id =' => '4')
				)
		)
		);
		$info['commentSum'] = $this->Marker->Comment->find('count');
		$info['ratingSum'] = $this->Rating->find('count');

		$info['commentLast'] = $this->Marker->Comment->find('first', array(
				'fields' => array('marker_id'),
				'conditions' => array('status >=' => '1'),
				'order' => array(
						'Comment.created DESC')
		)
		);

		return $info;

	}

}
?>
