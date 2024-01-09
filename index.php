<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type ");
require_once __DIR__ . '/vendor/smtp/PHPMailerAutoload.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/init.php';
$app = new App;
