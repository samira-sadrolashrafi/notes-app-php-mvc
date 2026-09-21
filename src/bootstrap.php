<?php

// مسیر اصلی پروژه
define('APPROOT', dirname(__FILE__) . '/app');

// آدرس سایت
define('URLROOT', 'http://localhost:8000');


require_once APPROOT . '/config/config.php';

require_once APPROOT . '/libraries/Core.php';
require_once APPROOT . '/libraries/Controller.php';
require_once APPROOT . '/libraries/Database.php';