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
    $this->model = new \model\User(1, "Admin", "password");
    $this->view = new \view\User($this->model);
  }


  /**
   * Desides which page to be shown.
   * @return String
   */
  public function showPage() {
    // User logged in, giv'em the logout!
    if ($this->model->userIsLoggedIn($this->view->getClientIdentifier())) {
      // $this->view->loginThroughCookies();
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

    ## MOVE!!
    if ($this->view->didPost("login")) {
      // Valid user credentials?
      $this->model->hashPassword($this->model->getPassword());
      if ($this->view->userCredentialsIsValid()) {
        ## MOVE!!
        if ($this->view->didPost("remember")) {
          $this->view->setCookies();
        }
        // Login user.
        $this->model->login($this->view->getClientIdentifier(), $this->model->getPassword(),
          $this->model->getHashedPassword());
      }
    }

    // var_dump($_SESSION);

    // Render a view.
    return $this->view->showLogin();
    
  }

  public function doLogout() {
    // Pressed logout?

    ## MOVE!!
    if ($this->view->didGet("logout")) {
      // Logout the user.
      if ($this->view->cookieExist()) {
        $this->view->killCookies();
      }
      $this->model->logout();
    }

    // var_dump($_SESSION);

    // Render a view.
    return $this->view->showLogout();
  }


}