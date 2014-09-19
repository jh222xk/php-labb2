<?php

namespace view;

class CookieJar {
  private $cookieName = "CookieJar";

  /**
   * Sets a cookie
   * @param String $string
   */
  public function save($string) {
    setcookie($this->cookieName, $string, 0);
  }

  /**
   * Loads a cookie
   * @return
   */ 
  public function load() {
    $ret = isset($_COOKIE[$this->cookieName]) ? $_COOKIE[$this->cookieName] : "";

    setcookie($this->cookieName, "", time() -1);

    return $ret;
  }
}