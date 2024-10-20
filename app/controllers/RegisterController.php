<?php

use Phalcon\Mvc\Controller;

class RegisterController extends Controller
{
    public function indexAction()
    {
        $this->view->login_url = $this->url->get('login');
        $this->view->submit_url = $this->url->get('register/submit');
    }

    public function submitAction()
    {
        $this->view->disable();

        if ($this->request->isPost()) {
            if ($this->request->has('name') && $this->request->has('email') && $this->request->has('password')) {
                $user = new Users();

                $user->name = $this->request->get('name');
                $user->email = $this->request->get('email');
                $user->password = $this->security->hash($this->request->get('password'));
                $user->role = 'cliente';

                if ($user->save()) {
                    // Usuario registrado exitosamente, redirigir al dashboard del cliente
                    $this->response->redirect('client/dashboard');
                } else {
                    // Error al registrar, redirigir al formulario de registro con mensaje de error
                    $this->flash->error('Error al registrar el usuario. Por favor, inténtelo de nuevo.');
                    $this->response->redirect('register');
                }
            }
        }
    }
}

