<?php

// مسیر اصلی پروژه
define('APPROOT', dirname(__FILE__) . '/app');

// آدرس سایت
define('URLROOT', 'http://localhost:8000');

require_once __DIR__ . '/vendor/autoload.php';

// Config
require_once APPROOT . '/config/config.php';
require_once APPROOT . '/config/session.php';


// Libraries
require_once APPROOT . '/libraries/Core.php';
require_once APPROOT . '/libraries/Controller.php';
require_once APPROOT . '/libraries/Database.php';


// Models
require_once APPROOT . '/models/User.php';


// Seeders
require_once APPROOT . '/database/seeders/NoteSeeder.php';


// Controllers
require_once APPROOT . '/controllers/AuthController.php';
require_once APPROOT . '/controllers/PagesController.php';
require_once APPROOT . '/controllers/NotesController.php';
require_once APPROOT . '/controllers/ProfileController.php';


// Helpers
require_once APPROOT . '/helpers/validation_helper.php';
require_once APPROOT . '/helpers/session_helper.php';
require_once APPROOT . '/helpers/url_helper.php';
require_once APPROOT . '/helpers/date_helper.php';
