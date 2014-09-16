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

  /**
   * Secret signing key, keep secret. Maybe as an env var instead.
   * @var String
   */
  private $key = '2vtH6v#tbv$JOy4PxO!ISmWdBBtL2tjBNh0GoIwJa6ePtfhu9X5OD!NIY&*0';
  
  function __construct(\model\User $model) {
    $this->model = $model;
    $this->message = new \view\CookieJar();
  }

  /**
   * Check if a given POST var is present.
   * @return Boolean
   */
  public function didPost($name) {
    return isset($_POST[$name]);
  }

  /**
   * Check if a given GET var is present.
   * @return Boolean
   */
  public function didGet($name) {
    return isset($_GET[$name]);
  }

  /**
   * Just a check to see if the username and password is equal
   * to the models data.
   * @return Boolean
   */
  public function userCredentialsIsValid() {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($this->model->getUsername() === $username
      && $this->model->getPassword() === $password
      && $this->didPost("remember") == false) {
      $this->message->save("Inloggning lyckades!");
      return true;
    }
    elseif($this->model->getUsername() === $username
      && $this->model->getPassword() === $password
      && $this->didPost("remember")) {
      $this->message->save("Inloggning lyckades och vi kommer ihåg dig!");
      return true;
    }
    elseif(empty($username) && empty($password)) {
      $this->message->save("Användarnamn saknas!");
    }
    elseif($username && empty($password)) {
      $this->message->save("Lösenord saknas!");
    }
    elseif($password && empty($username)) {
      $this->message->save("Användarnamn saknas!");
    }
    elseif($username === $this->model->getUsername()
      && $password !== $this->model->getPassword()) {
      $this->message->save("Felaktigt användarnamn och/eller lösenord");
    }
    elseif($password === $this->model->getPassword()
      && $username !== $this->model->getUsername()) {
      $this->message->save("Felaktigt användarnamn och/eller lösenord");
    }

    return false;
  }

  /**
   * Woho, a show login page for the users… I guess.
   * @return String
   */
  public function showLogin() {
    if ($this->didPost("username")) {
      setcookie("username", $_POST["username"], 0);
    }
    else {
      setcookie("username", "", time() -1);
    }

    if(isset($_COOKIE["username"])) {
      $username = $_COOKIE["username"];
    }
    else {
      $username = "";
    }

    $ret = "
      <h2>Ej inloggad</h2>
      <form action='.' method='post'>
        <fieldset>
          <legend>Login - Skriv in användarnamn och lösenord</legend>
          <label>Användarnamn: </label>
          <input type='text' size='20' name='username' value='$username' />
          <label>Lösenord: </label>
          <input type='password' size='20' name='password' value='' />
          <label>Håll mig inloggad: </label>
          <input type='checkbox' name='remember' />
          <input type='submit' value='Logga in' name='login' />
        </fieldset>
      </form>
    ";

    if ($this->didPost("login")) {
      header('Location: ' . $_SERVER['PHP_SELF']);
    }
    elseif ($this->loginThroughCookies()) {
      $this->message->save("Inloggning genom kakor!");
      header('Location: ' . $_SERVER['PHP_SELF']);
    }
    elseif($this->cookieExist() && $this->loginThroughCookies() == false) {
      $this->message->save("Felaktig information i kakan!");
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
    if ($this->didGet("logout")) {
      $this->message->save("Du har nu loggat ut!");
      header('Location: ' . $_SERVER['PHP_SELF']);
    }
    else {
      $ret .= $this->message->load();
    }

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
    return isset($_COOKIE[$this->rememberMeUsernameCookie])
      && isset($_COOKIE[$this->rememberMePasswordCookie]);
  }

  /**
   * Set the remember me cookies.
   */ 
  public function setCookies() {
    $time = time()+60; // 1 minute.

    $userData = array('username' => $this->model->getUsername(), 'time' => $time);
    $passData = array('password' => $this->model->getHashedPassword(), 'time' => $time);

    // Use a hash_hmac with encoded json and sha256 as hashing algorithm.
    $userHmac = hash_hmac('sha256', json_encode($userData), $this->key);
    $passHmac = hash_hmac('sha256', json_encode($passData), $this->key);

    $userData['hmac'] = $userHmac;
    $passData['hmac'] = $passHmac;

    setcookie($this->rememberMeUsernameCookie, base64_encode(json_encode($userData)), $time);
    setcookie($this->rememberMePasswordCookie, base64_encode(json_encode($passData)), $time);
  }

  /**
   * Kill the remember me cookies.
   */ 
  public function killCookies() {
    setcookie($this->rememberMeUsernameCookie, "", time() -1);
    setcookie($this->rememberMePasswordCookie, "", time() -1);
  }

  /**
   * Get the remember me cookies.
   * @return Array
   */
  public function getCookies() {
    $time = time()+60; // 1 minute.

    // Decode the cookie.
    $userCookie = json_decode(base64_decode($_COOKIE[$this->rememberMeUsernameCookie]));
    $passCookie = json_decode(base64_decode($_COOKIE[$this->rememberMePasswordCookie]));

    // Store the hmac for comparison.
    $userCookieHmac = $userCookie->hmac;
    $passCookieHmac = $passCookie->hmac;

    // Remove the hmac from the cookie data.
    unset($userCookie->hmac);
    unset($passCookie->hmac);

    // Calculate hmac for data, should be the same as the stored one.
    $userCalculatedHmac = hash_hmac('sha256', json_encode($userCookie), $this->key);
    $passCalculatedHmac = hash_hmac('sha256', json_encode($passCookie), $this->key);

    // Check if the hmac's is fine.
    if ($userCookieHmac === $userCalculatedHmac && $passCookieHmac === $passCalculatedHmac) {
      return array('user' => $userCookie, 'pass' => $passCookie);
    }
    else {
      return false;
    }
  }

  /**
   * Checks if the remember me cookies has expired.
   * @return Boolean
   */
  public function cookieExpired() {
    $cookies = $this->getCookies();

    if ($cookies['user']->time < time() && $cookies['pass']->time < time()) {
      return true;
    }
    else {
      return false;
    }
  }

  /**
   * Login through the remember me cookies.
   * @return Boolean
   */
  public function loginThroughCookies() {
    if (isset($_COOKIE[$this->rememberMeUsernameCookie])
      && isset($_COOKIE[$this->rememberMePasswordCookie])
      && $this->cookieExpired() == false) {

      // Get the cookies.
      $cookies = $this->getCookies();

      // Login the user.
      if ($cookies["user"]->username === $this->model->getUsername() 
        && $this->model->login($this->getClientIdentifier(), $this->model->getPassword(),
          $cookies["pass"]->password)) {
        return true;
      }
    }
    $this->killCookies();
    return false;
  }
}