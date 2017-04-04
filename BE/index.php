<?php
/**
 * Created by PhpStorm.
 * User: dreadw1nd
 * Date: 01.04.2017
 * Time: 21:33
 */

ini_set('display_errors', 1);
require_once '/vendor/autoload.php';

$app = require '/src/App.php';
require '/src/Controllers.php';
$app->run();