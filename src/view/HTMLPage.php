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
        $body
      </body>
      </html>
    ";
    return $ret;
  }
}