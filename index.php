<?php

require_once("src/view/HTMLPage.php");
require_once("src/controller/User.php");
require_once("src/view/CurrentDateTime.php");

session_start();

$controller = new \controller\User();
$htmlBody = $controller->showPage();

// $controller->doLogout();

$view = new \view\HTMLPage();
$currentTime = new \view\CurrentDateTime();

echo $view->echoHTML("Logga in", $htmlBody);

// echo $view->echoHTML("Tiden är ", $currentTime->getCurrentDateTime());
