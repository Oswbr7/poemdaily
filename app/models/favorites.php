<?php
use Phalcon\Mvc\Model;

class Favorites extends Model
{
    public $userID;
    public $poemID;
    public $genre;

    public function initialize(){
        $this->setSource('favorites');
    }
}