<?php

use Phalcon\Mvc\Model;

class YearningPoem extends Model
{
    public $id;
    public $author;
    public $title;
    public $content;
    public $created_at;

    public function initialize()
    {
        $this->setSource('yearning_poems');
    }
}
