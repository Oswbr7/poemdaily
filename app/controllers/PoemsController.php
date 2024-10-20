<?php

use Phalcon\Mvc\Controller;

class PoemsController extends Controller
{
    public function indexAction()
    {
        $this->view->login_url = $this->url->get('login');
    }

    public function dashboardAction()
    {

    }
}

