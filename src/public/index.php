<?php

require_once '../bootstrap.php';



$page = $_GET['page'] ?? 'login';



switch ($page) {


    case 'register':

        $auth = new AuthController();

        $auth->register();

        break;



    case 'dashboard':

        $pages = new PagesController();

        $pages->dashboard();

        break;


    case 'logout':

        $auth = new AuthController();

        $auth->logout();

        break;


    case 'login':

        $auth = new AuthController();

        $auth->login();

        break;


    case 'notes':

        $notes = new NotesController();

        $notes->index();

        break;

    case 'notes-create':

        $notes = new NotesController();

        $notes->create();

        break;

    case 'notes-edit':

        $notes = new NotesController();

        $notes->edit();

        break;

    case 'notes-delete':

        $notes = new NotesController();

        $notes->delete();

        break;


    default:

        $auth = new AuthController();

        $auth->login();

        break;
}
