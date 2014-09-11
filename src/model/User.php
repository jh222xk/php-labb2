<?php

namespace model;

class User {

  /**
   * The username session var
   * @var String
   */
  private $usernameSession = "User::username";

  /**
   * The password session var
   * @var String
   */
  private $passwordSession = "User::password";

  /**
   * The uniqueID session var
   * @var String
   */
  private $uniqueIDSession = "User::uniqueID";

  /**
   * The clientIdentifier session var, possible an ip-adress
   * @var String
   */
  private $clientIdentifier = "User::clientIdentifier";

  private $token;

  /**
   * The users unique ID
   * @var Integer Example 123
   */ 
  private $userID;

  /**
   * Username of the user
   * @var String Example Edith
   */
  private $username;

  /**
   * Password for the user
   * @var String Example 3ew0gX8V7hlS
   */
  private $password;

  /**
   * @param Integer $userID
   * @param String $username
   * @param String $password
   */
  public function __construct($userID, $username, $password) {
    $this->userID = $userID;
    $this->username = $username;
    $this->password = $password;
  }

  /**
   * Gets the users id
   * @return Integer
   */
  public function getUserID() {
    return $this->userID;
  }

  /**
   * Gets the users name
   * @return String
   */
  public function getUsername() {
    return $this->username;
  }

  /**
   * Gets the users password
   * @return String
   */
  public function getPassword() {
    return $this->password;
  }

  /**
   * Hashes the password, todo: Fix it.
   */ 
  public function hashPassword() {
    # FIX IT!
    return crypt($this->password);
  }

  /**
   * Checks if the user is logged in according to the session.
   * @return Boolean
   */ 
  public function userIsLoggedIn($clientIdentifier) {
    // var_dump($this->getToken());

    $this->generateToken($clientIdentifier);
    // die();
    if ($_SESSION[$this->uniqueIDSession] === $this->userID
        && $_SESSION[$this->usernameSession] === $this->username
        && $_SESSION[$this->passwordSession] === $this->password
        && $_SESSION[$this->clientIdentifier] === $clientIdentifier) {
        // var_dump($_SESSION[$this->clientIdentifier]);
      return true;
    }
    return false;
  }

  /**
   * Signs the user in, i.e. sets the session vars.
   */
  public function login($clientIdentifier) {
    $this->generateToken($clientIdentifier);
    $_SESSION[$this->uniqueIDSession] = $this->userID;
    $_SESSION[$this->usernameSession] = $this->username;
    $_SESSION[$this->passwordSession] = $this->password;
    $_SESSION[$this->clientIdentifier] = $clientIdentifier;
    // var_dump($this->getToken());
    // die();
    // $_SESSION["USER_AGENT"] = $_SERVER['HTTP_USER_AGENT'];
  }

  /**
   * Signs out the user, i.e. kills the session.
   */
  public function logout() {
    unset($_SESSION[$this->uniqueIDSession]);
    unset($_SESSION[$this->usernameSession]);
    unset($_SESSION[$this->passwordSession]);
  }

  public function getToken() {
    return $this->token;
  }

  public function generateToken($input) {
    $this->token = md5($input);
  }
}