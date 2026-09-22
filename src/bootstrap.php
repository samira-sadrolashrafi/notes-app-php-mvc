<?php

// مسیر اصلی پروژه
define('APPROOT', dirname(__FILE__) . '/app');

// آدرس سایت
define('URLROOT', 'http://localhost:8000');


// Config
require_once APPROOT . '/config/config.php';


// Libraries
require_once APPROOT . '/libraries/Core.php';
require_once APPROOT . '/libraries/Controller.php';
require_once APPROOT . '/libraries/Database.php';


// Models
require_once APPROOT . '/models/User.php';


// Controllers
require_once APPROOT . '/controllers/AuthController.php';


// Helpers
require_once APPROOT . '/helpers/validation_helper.php';