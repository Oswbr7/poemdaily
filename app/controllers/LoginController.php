<?php

use Apps\Admin\Models\Users;
use Phalcon\Mvc\Controller;

use Phalcon\Session\Adapter\Files as Session;
$di->setShared(
    'session',
    function () {
        $session = new Session();
        $session->start();
        return $session;
    }
);

class LoginController extends Controller
{
    public function indexAction()
    {
        $this->view->register_url = $this->url->get('register');
        $this->view->login_url = $this->url->get('login');
    }

    public function loginAction()
    {
        $this->view->disable();

        if($this->request->isPost()){
            $user = \Users::findFirstByEmail($this->request->getPost("email"));
            if ($user){
                if ($this->security->checkHash($this->request->getPost("password"), $user->password)){
                    // Redirige según el rol
                    $this->session->set('userId', $user->id);
                    if ($user->role === 'admin') {
                        $this->response->redirect('admin/dashboard');
                    } elseif ($user->role === 'cliente') {
                        $this->response->redirect('client/dashboard');
                    }
                }else {
                    $this->flash->error("No es posible iniciar sesión, favor de contactar al administrador");
                    $this->response->redirect('login');
                }
            }
        }
    }
}