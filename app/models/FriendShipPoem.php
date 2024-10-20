<?php

use Phalcon\Mvc\Model;

class FriendShipPoem extends Model
{
    public $id;
    public $author;
    public $title;
    public $content;
    public $created_at;

    public function initialize()
    {
        $this->setSource('friend_ship_poems');
    }
}
