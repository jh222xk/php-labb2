<?php

namespace view;

require_once("src/view/CookieJar.php");

class User {
  /**
   * @var Usermodel
   */
  private $model;

  private $message;

  private $rememberMeUsernameCookie = "User::Username";

  private $rememberMePasswordCookie = "User::Password";
  
  function __construct(\model\User $model) {
    $this->model = $model;
    $this->message = new \view\CookieJar();
  }

  /**
   * Do the user want to be remembered?
   * @return Boolean
   */
  public function rememberUser() {
    if (isset($_POST['remember'])) {
      return true;
    }
    return false;
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
      $this->message->save("Inloggning lyckades!");
      return true;
    }
    elseif(empty($username) && empty($password)) {
      $this->message->save("Fel användarnamn!");
    }
    elseif($username && empty($password)) {
      $this->message->save("Fel lösenord!");
    }
    elseif($password && empty($username)) {
      $this->message->save("Fel användarnamn!"); 
    }
    elseif($username === $this->model->getUsername() && $password !== $this->model->getPassword()) {
      $this->message->save("Felaktigt användarnamn och/eller lösenord");
    }
    elseif($password === $this->model->getPassword() && $username !== $this->model->getUsername()) {
      $this->message->save("Felaktigt användarnamn och/eller lösenord");
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
      <form action='.' method='post'>
        <fieldset>
          <legend>Login - Skriv in användarnamn och lösenord</legend>
          <label>Användarnamn: </label>
          <input type='text' size='20' name='username' value='' />
          <label>Lösenord: </label>
          <input type='password' size='20' name='password' value='' />
          <label>Håll mig inloggad: </label>
          <input type='checkbox' name='remember' />
          <input type='submit' value='Logga in' name='login' />
        </fieldset>
      </form>
    ";

    if ($this->didSubmit()) {
      header('Location: ' . $_SERVER['PHP_SELF']);
    }
    else {
      $ret .= $this->message->load();
    }

    // var_dump($this->message->load());

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
    $ret .= $this->message->load();
    return $ret;
  }

  /**
   * Get the clients user agent and ip.
   * The user agent is'nt enough for session hijacking
   * since spoofing a user agent is easy enough.
   * @return String
   */
  public function getClientIdentifier() {
    return $_SERVER["REMOTE_ADDR"] . $_SERVER["HTTP_USER_AGENT"];
  }


  /**
   * Check if a remember me cookie exist.
   * @return Boolean
   */
  public function cookieExist() {
    if (isset($_COOKIE[$this->rememberMeUsernameCookie])) {
      return true;
    }
  }

  /**
   * Set the remember me cookies.
   */ 
  public function setCookies() {
    $time = time()+60*60*24*30; // 30 days.
    setcookie($this->rememberMeUsernameCookie, $this->model->getUsername(), $time);
    setcookie($this->rememberMePasswordCookie, $this->model->getPassword(), $time);
  }

  /**
   * Kill the remember me cookies.
   */ 
  public function killCookies() {
    setcookie($this->rememberMeUsernameCookie, "", time() -1);
    setcookie($this->rememberMePasswordCookie, "", time() -1);
  }

  /**
   * Login throught the remember me cookies.
   */
  public function loginThroughCookies() {
    // var_dump($_COOKIE[$this->rememberMePasswordCookie]);
    // die();
    if (isset($_COOKIE[$this->rememberMeUsernameCookie]) 
      && $_COOKIE[$this->rememberMeUsernameCookie] === $this->model->getUsername()
      && isset($_COOKIE[$this->rememberMePasswordCookie])
      && $_COOKIE[$this->rememberMePasswordCookie] === $this->model->getPassword()) {
      $this->model->login($this->getClientIdentifier());
    }
    else {
      $this->killCookies();
    }
  }
}