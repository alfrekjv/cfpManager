<?php
namespace App\Controller;
class Manage extends Application {

	function preDispatch() {
		$this->addCSS('user/talk', 'user/account', 'manage/listing');
		$this->addJS('libs/jquery-validationEngine-en', 'libs/jquery-validationEngine', 'app/manage/form');
	}
	
	function index() {
		$this->redirect('manage/users');
	}
	
	function users() {
		
		$this->loginCheck();
		if(!$this->getUser()->isAdmin()) {
			$this->setFlash('Permission Denied');
			$this->redirect('');
		}
		
		$us = $this->getUserStorage();
		
		$users = $us->getAll();
		
		$subPage = 'users';
		$section = 'users';
		$this->render('manage/index', compact('users', 'subPage', 'errors', 'section'));
	}
	
	function viewuser() {
		
		$userID = $this->get(__FUNCTION__);
		if(empty($userID)) {
			$this->redirect('manage/users');
		}
		
		$this->loginCheck();

		// -- Permissions
		if(!$this->getUser()->isAdmin()) {
			$this->setFlash('Permission Denied');
			$this->redirect('');
		}

		// -- Entity Stuff --
		$user = $this->getUserStorage()->getByID($userID);

		// - Rendering --
		$subPage = 'users/view';
		$section = 'users';
		$this->render('manage/index', compact('user', 'subPage', 'errors', 'section'));
		
	}

	function createuser()
	{
		
		$errors = array();

		if($this->is('post')) {
			$post = $this->post();
			$requiredKeys = array('userName', 'email', 'firstName', 'lastName', 'password');
			
			foreach($requiredKeys as $field) {
				if(!isset($post[$field]) || empty($post[$field])) {
					$errors[$field] = 'Field is required';
				}
			}
			
			if(empty($errors)) {
			
				$user = array(
					
					'firstName'      => $post['firstName'],
					'lastName'       => $post['lastName'],
					'email'          => $post['email'],
					'username'       => $post['userName'],
					'password'  => $post['password'],
					'salt'      => base64_encode(openssl_random_pseudo_bytes(16)),

					'twitter_handle' => $post['twitterHandle'],
					'website'        => $post['website'],
					'job_title'      => $post['jobTitle'],
					'company_name'   => $post['companyName'],
					'bio'            => $post['bio'],
					'country'        => $post['country'],

				);
				
				$userStorage = $this->getUserStorage();
				$newUserID = $userStorage->create($user, $this->getConfig()->auth->salt);
				$this->redirect('manage/users');
			}
		}
		// -- Rendering --
		$subPage = 'users/create';
		$section = 'users';
		$this->render('manage/index', compact('user', 'subPage', 'errors', 'section'));
	}
	
	function edituser() {
		
		// -- Params --
		$userID = $this->get(__FUNCTION__);
		if(empty($userID)) {
			$this->redirect('manage/users');
		}
		
		$this->loginCheck();

		// -- Permissions
		if(!$this->getUser()->isAdmin()) {
			$this->setFlash('Permission Denied');
			$this->redirect('');
		}
		
		// -- Save --
		if($this->is('post')) {
			return $this->edituser_save($userID);
		}

		// -- Entity Stuff --
		$us = $this->getUserStorage();
		if(!$us->exists($userID)) {
			$this->redirect('manage/users');
		}
		$user = $us->getByID($userID);

		// -- Rendering --
		$subPage = 'users/edit';
		$section = 'users';
		$this->render('manage/index', compact('user', 'subPage', 'errors', 'section'));
	}
	
	function deleteuser() {

		$this->loginCheck();
		if(!$this->getUser()->isAdmin()) {
			$this->setFlash('Permission Denied');
			$this->redirect('');
		}
		
		$userID = $this->get(__FUNCTION__);
		$us     = $this->getUserStorage();
		$us->delete(array('id' => $userID));
		$this->setFlash('User Deleted');
		$this->redirect('manage/users');
		
	}
	
	protected function edituser_save($userID) {

		$post = $this->post();
		$requiredKeys = array('userName', 'email', 'firstName', 'lastName');
		$errors = array();
		foreach($requiredKeys as $field) {
			if(!isset($post[$field]) || empty($post[$field])) {
				$errors[$field] = 'This field is required';
			}
		}

		if(empty($errors)) {
			$this->getUserStorage()->update(array(
				'firstName'      => $post['firstName'],
				'lastName'       => $post['lastName'],
				'email'          => $post['email'],
				'username'       => $post['userName'],
				'twitter_handle' => $post['twitterHandle'],
				'website'        => $post['website'],
				'job_title'      => $post['jobTitle'],
				'bio'            => $post['bio'],
				'country'        => $post['country'],
			), array('id' => $userID));
			
			$this->setFlash('User Account Updated');
			$this->redirect('manage/users');
		}
	}

	function talks() {

		// -- Permissions --
		$this->loginCheck();
		if(!$this->getUser()->isAdmin()) {
			$this->setFlash('Permission Denied');
			$this->redirect('');
		}
		
		// -- Entity Stuff --
		$talks = $this->getTalkStorage()->getAll();
		
		$subPage = 'talks';
		$section = 'talks';
		$this->render('manage/index', compact('talks', 'subPage', 'errors', 'section'));
	}
	
	function createtalk() {
		
		$errors = array();
		if($this->is('post')) {
			
			$post = $this->post();
			$requiredKeys = array('talkTitle', 'talkSlidesUrl', 'talkDuration', 'talkLevel', 'talkAbstract');
			
			foreach($requiredKeys as $field) {
				if(!isset($post[$field]) || empty($post[$field])) {
					$errors[$field] = 'Field is required';
				}
			}

			if(empty($errors)) {
				$talkID = $this->getTalkStorage()->create(array(
					'title'      => $post['talkTitle'],
					'slides_url' => $post['talkSlidesUrl'],
					'duration'   => $post['talkDuration'],
					'level'      => $post['talkLevel'],
					'abstract'   => $post['talkAbstract'],
					'remark'     => $post['talkRemark'],
					'owner_id'   => $this->getUser()->getID()
				));
				$this->redirect('manage/talks/view/' . $talkID);
			}
		}
		
		$talks         = $this->getTalkStorage()->getByOwnerID($this->getUser()->getID());
		$subPage       = 'talks/create';
		$section       = 'talks';
		$talkDurations = $this->getConfig()->talk->duration->toArray();
		
		$this->addCSS('manage/talk');
		$this->render('manage/index', compact('talks', 'subPage', 'section', 'talkDurations'));
	}
	
	function viewtalk() {
		
		// -- Params --
		$talkID = $this->get(__FUNCTION__);
		if(empty($talkID)) {
			$this->redirect('');
		}
		
		// -- Talk --
		$talkStorage = $this->getTalkStorage();
		$talk = $talkStorage->find($talkID);
		if(empty($talk)) {
			$this->setFlash('Invalid Talk ID');
			$this->redirect('');
		}
		
		$talk = new \App\Entity\Talk($talk);
		
		// -- Talk Owner --
		$talkOwner = new \App\Entity\User($this->getUserStorage()->find($talk->getOwnerID()));
		if(empty($talkOwner)) {
			$this->setFlash('Missing Talk Owner');
			$this->redirect('');
		}
		
		// -- Rendering --
		$viewingOwnProfile = $this->isLoggedIn() && $talk->getOwnerID() == $this->getUser()->getID();
		$subPage           = 'talks/view';
		$section           = 'talks';
		$this->render('manage/index', compact('talkOwner', 'talk', 'section', 'subPage'));
		
	}
	
	function edittalk() {
		
		// -- Params --
		$talkID = $this->get(__FUNCTION__);
		if(empty($talkID)) {
			$this->setFlash('Invalid Talk ID');
			$this->redirect('');
		}
		
		// -- Need to be authed --
		$this->loginCheck();
		
		// -- Permissions --
		if(!$this->getUser()->isAdmin()) {
			$this->setFlash('Permission Denied');
			$this->redirect('');
		}
		
		$talk = $this->getTalkStorage()->getTalkFromID($talkID);
		
		// -- Save the form --
		if($this->is('post')) {

			$post = $this->post();
			$requiredKeys = array('talkTitle', 'talkSlidesUrl', 'talkDuration', 'talkLevel', 'talkAbstract');
			$errors = array();
			foreach($requiredKeys as $field) {
				if(!isset($post[$field]) || empty($post[$field])) {
					$errors[$field] = 'Field is required';
				}
			}
			if(empty($errors)) {
				$this->getTalkStorage()->update(array(
					'title'      => $post['talkTitle'],
					'slides_url' => $post['talkSlidesUrl'],
					'duration'   => $post['talkDuration'],
					'level'      => $post['talkLevel'],
					'abstract'   => $post['talkAbstract'],
					'remark'     => $post['talkRemark'],
					'owner_id'   => $this->getUser()->getID()
					
				), array('id' => $talk->getID()));

				$this->redirect('manage/talks/view/' . $talkID);
			}
		}
		
		// -- Rendering --
		$section       = 'talks';
		$subPage       = 'talks/edit';
		$talkDurations = $this->getConfig()->talk->duration->toArray();
		
		$this->addCSS('manage/talk');
		$this->render('manage/index', compact('talk', 'section', 'subPage', 'talkDurations'));
		
	}
	
	function deletetalk() {
		
		// -- Params --
		$talkID = $this->get(__FUNCTION__);

		// -- Need to be authed --
		$this->loginCheck();
		
		// -- Permissions --
		if(!$this->getUser()->isAdmin()) {
			$this->setFlash('Permission Denied');
			$this->redirect('');
		}
		
		$ts = $this->getTalkStorage();

		// -- Get the talk --
		$talk = $ts->getTalkFromID($talkID);
		
		$ts->delete(array('id' => $talk->getID()));
		
		$this->setFlash('Talk successfully deleted');
		$this->redirect('manage/talks');
	}
	
//	function 
	
}
