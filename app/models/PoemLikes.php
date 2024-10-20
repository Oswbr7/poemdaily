<?php
use Phalcon\Mvc\Model;

class PoemLikes extends Model
{
    public $userID;
    public $poemID;
    public $genre;

    public function initialize(){
        $this->setSource('poemlikes');
    }
}