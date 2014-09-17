<?php

namespace controller;

require_once("src/model/User.php");
require_once("src/view/User.php");

class User {

  /**
   * @var Userview
   */
  private $view;

  /**
   * @var Usermodel
   */
  private $model;
  
  function __construct() {
    $this->model = new \model\User(1, "Admin", "Password");
    $this->view = new \view\User($this->model);
    $this->model->hashPassword($this->model->getPassword());
  }


  /**
   * Desides which page to be shown.
   * @return String
   */
  public function showPage() {
    // User logged in, giv'em the logout!
    if ($this->model->userIsLoggedIn($this->view->getClientIdentifier())) {
      return $this->doLogout();
    }
    else {
      return $this->doLogin();
    }
  }

  /**
   * 
   */ 
  public function doLogin() {
    // Submitted the form?
    if ($this->view->userWantsToLogin()) {
      // Valid user credentials?
      if ($this->view->userCredentialsIsValid()) {
        // If the user want to be remembered set some cookies for that.
        if ($this->view->userWantsToBeRemembered()) {
          $this->view->setCookies();
        }
        // Login user.
        $this->model->login($this->view->getClientIdentifier(), $this->model->getPassword(),
          $this->model->getHashedPassword());
      }
    }

    // Render a view.
    return $this->view->showLogin();
  }

  public function doLogout() {
    // Pressed logout?
    if ($this->view->userWantsToLogout()) {
      // If the user has some cookies set, kill'em.
      if ($this->view->cookieExist()) {
        $this->view->killCookies();
      }
      // Logout the user.
      $this->model->logout();
    }

    // Render a view.
    return $this->view->showLogout();
  }

}