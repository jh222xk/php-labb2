<?php

namespace model;

class User {

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
}