<?php

namespace view;

class HTMLPage {
  /**
   * @param String $title
   * @param String $body
   * @return String
   */
  public function echoHTML($title, $body) {
    $ret = "
      <!DOCTYPE html>
      <html>
      <head>
        <meta charset='utf-8'>
        <title>$title</title>
      </head>
      <body>
        <h1>Laborationskod jh222xk</h1>
        $body
      </body>
      </html>
    ";
    return $ret;
  }
}