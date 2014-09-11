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
    // var_dump($_COOKIE);
    // die();
  }

  /**
   * Loads a cookie
   * @return
   */ 
  public function load() {
    $ret = isset($_COOKIE[$this->cookieName]) ? $_COOKIE[$this->cookieName] : "";

    // if (isset($_COOKIE[$this->cookieName]))
    //   $ret = $_COOKIE[$this->cookieName];
    // else
    //   $ret = "";

    // var_dump("ASD" . $ret);

    setcookie($this->cookieName, "", time() -1);

    return $ret;
  }
}