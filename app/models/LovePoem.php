<?php

use Phalcon\Mvc\Model;

class LovePoem extends Model
{
    public $id;
    public $author;
    public $title;
    public $content;
    public $created_at;

    public function initialize()
    {
        $this->setSource('love_poems');
    }
}
