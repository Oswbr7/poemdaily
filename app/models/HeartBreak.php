<?php

use Phalcon\Mvc\Model;

class HeartBreak extends Model
{
    public $id;
    public $author;
    public $title;
    public $content;
    public $created_at;

    public function initialize()
    {
        $this->setSource('heart_break');
    }
}
