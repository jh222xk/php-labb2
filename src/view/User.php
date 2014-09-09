<?php

namespace view;

class User {
  /**
   * @var Usermodel
   */
  private $model;
  
  function __construct(\model\User $model) {
    $this->model = $model;
  }

  /**
   * Desides which page to be shown.
   * @return String
   */
  public function showPage() {
    // User logged in, giv'em the logout!
    if ($this->model->userIsLoggedIn()) {
      return $this->showLogout();
    }
    else {
      return $this->showLogin();
    }
  }

  /**
   * Did the user submit the login form?
   * @return Boolean
   */
  public function didSubmit() {
    if (isset($_POST['login'])) {
      return true;
    }
    return false;
  }

  /**
   * Did the user press the logout button?
   * @return Boolean
   */
  public function didPressLogout() {
    if (isset($_GET['logout'])) {
      return true;
    }
    return false;
  }

  /**
   * Just a check to see if the username and password is equal
   * to the models data.
   * @return Boolean
   */
  public function userCredentialsIsValid() {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($this->model->getUsername() === $username && $this->model->getPassword() === $password) {
      return true;
    }
    return false;
  }

  /**
   * Woho, a show login page for the users… I guess.
   * @return String
   */
  public function showLogin() {
    $ret = "
      <h2>Ej inloggad</h2>
      <form action='' method='post'>
        <fieldset>
          <legend>Login - Skriv in användarnamn och lösenord</legend>
          <label>Användarnamn: </label>
          <input type='text' size='20' name='username' value='' />
          <label>Lösenord: </label>
          <input type='password' size='20' name='password' value='' />
          <label>Håll mig inloggad: </label>
          <input type='checkbox' name='' />
          <input type='submit' value='Logga in' name='login' />
        </fieldset>
      </form>
    ";
    return $ret;
  }

  /**
   * Yep, it's true. It will return a logout page.
   * @return String
   */
  public function showLogout() {
    $user = $this->model->getUsername();
    $ret = "
      <h2>$user är inloggad</h2>

      <p><a href='?logout'>Logga ut</a></p>
    ";
    return $ret;
  }
}