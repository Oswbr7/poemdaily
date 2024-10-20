<?php

use Phalcon\Mvc\Model;

class DesolationPoem extends Model
{
    public $id;
    public $author;
    public $title;
    public $content;
    public $created_at;

    public function initialize()
    {
        $this->setSource('desolation_poems');
    }
}
