<?php


class PagesController extends Controller
{


    public function dashboard()
    {
        if(!isLoggedIn()){
            header('Location: ?page=login');
            exit;
        }

        $this->view('pages/dashboard');

    }


}