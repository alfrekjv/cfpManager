<?php
namespace App\Controller;
class User extends Application {

	function preDispatch() {
		$this->addCSS('user/signup');
		$this->addJS('libs/jquery-validationEngine-en', 'libs/jquery-validationEngine', 'app/user/general');
	}
	
	function index() {
		
	}
	
	/**
	 * This is the registration process
	 * 
	 * @return void
	 */
	function signup() {

		$errors = array();
		if(!$this->is('post')) {
			return $this->render('user/signup', compact('errors'));
		}
		
		$post = $this->post();
		$requiredKeys = array('userName', 'email', 'firstName', 'lastName', 'password');
		
		foreach($requiredKeys as $field) {
			if(!isset($post[$field]) || empty($post[$field])) {
				$errors[$field] = 'Field is required';
			}
		}
		
		if(empty($errors)) {
		
			$user = array(
				'username'  => $post['userName'],
				'email'     => $post['email'],
				'firstName' => $post['firstName'],
				'lastName'  => $post['lastName'],
				'password'  => $post['password'],
				'salt'      => base64_encode(openssl_random_pseudo_bytes(16))
			);
			
			$userStorage = $this->getUserStorage();
			$newUserID = $userStorage->create($user, $this->getConfig()->auth->salt);
			$this->redirect('user/login');
		}
		
		$this->render('user/signup', compact('errors'));
	}
	
	function login() {
		
		// Check if we are already logged in
		if($this->isLoggedIn()) {
			$this->redirect('myaccount');
		}
		
		$errors = array();
		if(!$this->is('post')) {
			return $this->render('user/login', compact('errors'));
		}
		
		$post = $this->post();
		
		$userStorage = $this->getUserStorage();
		if($userStorage->checkAuth($post['email'], $post['password'], $this->getConfig()->auth->salt)) {
			$this->setAuthData(new \App\Entity\AuthUser($userStorage->findByEmail($post['email'])));
			$this->redirect('account');
		} else {
			$errors['message'] = 'Login failed. Please try again.';
		}
		$this->render('user/login', compact('errors'));
	}
	
	function logout() {
		$this->getSession()->clearAuthData();
		$this->redirect('');
	}

	function activate() {
		
	}
	
	function forgotpw() {
		$this->render('user/forgotpw');
	}
	
	protected function getUserStorage() {
		return new \App\Data\User();
	}
	
	function showaccount() {
		
		$this->loginCheck();
		
		$userAccount = new \App\Entity\User($this->getUserStorage()->findByEmail($this->getUser()->getEmail()));
		$subPage = 'showaccount';
		$this->render('user/account', compact('userAccount', 'subPage'));
	}
	
	function editaccount() {
		
		$this->loginCheck();
		
		if($this->is('post')) {
			
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
					
				), array('id' => $this->getUser()->getID()));
				
				$this->setFlash('Account Updated');
				$this->redirect('account');
			}
		}
		
		$userAccount = new \App\Entity\User($this->getUserStorage()->findByEmail($this->getUser()->getEmail()));
		$subPage = 'editaccount';
		$this->render('user/account', compact('userAccount', 'subPage', 'errors'));
	}
	
	function editpassword() {
		
		$this->loginCheck();
		
		$errors = array();
		$post = $this->post();
		if($this->is('post') && isset($post['currentPassword'], $post['password'])) {
			
			$userStorage = $this->getUserStorage();
			$email       = $this->getUser()->getEmail();
			$configSalt  = $this->getConfig()->auth->salt;
			
			// If the existing password is correct.
			if($userStorage->checkAuth($email, $post['currentPassword'], $configSalt)) {
//				var_dump($this->getUser()->getSalt(), $configSalt, $userStorage->saltPass($this->getUser()->getSalt(), $configSalt, $post['password'])); exit;
				$userStorage->update(array(
					'password' => $userStorage->saltPass($this->getUser()->getSalt(), $configSalt, $post['password'])
				), array('id' => $this->getUser()->getID()));
				
				$this->setFlash('Password Updated');
				$this->redirect('account');
			} else {
				$errors['currentPassword'] = 'Your current password is incorrect';
			}
		}
		$userAccount = new \App\Entity\User($this->getUserStorage()->findByEmail($this->getUser()->getEmail()));
		$subPage = 'editpassword';
		$this->render('user/account', compact('userAccount', 'subPage', 'errors'));
	}
	
}
