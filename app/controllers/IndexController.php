<?php

use Phalcon\Mvc\Controller;

class IndexController extends Controller
{
    public function indexAction()
    {
        $this->view->register_url = $this->url->get('register');
        $this->view->login_url = $this->url->get('login');
    }
}
