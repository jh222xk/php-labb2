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
    $this->model = new \model\User(1, "Jesper", "asdasd");
    $this->view = new \view\User($this->model);
  }


  /**
   * Desides which page to be shown.
   * @return String
   */
  public function showPage() {
    // User logged in, giv'em the logout!
    if ($this->model->userIsLoggedIn()) {
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
    if ($this->view->didSubmit()) {
      // Valid user credentials?
      if ($this->view->userCredentialsIsValid()) {
        // Login user.
        $this->model->login();
      }
    }

    // Render a view.
    return $this->view->showLogin();
    
  }

  public function doLogout() {
    // Pressed logout?
    if ($this->view->didPressLogout()) {
      // Logout the user.
      $this->model->logout();
      // Redirect!
      header('Location: ' . $_SERVER['PHP_SELF']);
    }

    // Render a view.
    return $this->view->showLogout();
  }
}