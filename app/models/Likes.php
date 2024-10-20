<?php
use Phalcon\Mvc\Model;

class Likes extends Model
{
    public $userID;
    public $genre;
    public $likeCnt;

    public function initialize(){
        $this->setSource('likes');
    }
}
