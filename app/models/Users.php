<?php

use Phalcon\Mvc\Model;

class Users extends Model
{
    public $id;
    public $name;
    public $email;
    public $password;
    public $role;
    public $like_amor;
    public $ike_anoranza;
    public $like_ausencia;
    public $like_desamor;
    public $like_angustia;
    public $like_amistad;
    public $like_desolacion;
    public $refresh_count;

    public function initialize()
    {
        $this->setSource('users');
    }
}
