<?php

require_once("src/view/HTMLPage.php");
require_once("src/view/CurrentDateTime.php");

$view = new \view\HTMLPage();

$currentTime = new \view\CurrentDateTime();

echo $view->echoHTML("Tiden är ", $currentTime->getCurrentDateTime());
