<?php

use Phalcon\Mvc\Model;

class ChildhoodPoem extends Model
{
    public $id;
    public $genre;
    public $title;
    public $poem;

    public function initialize()
    {
        $this->setSource('poems');
    }
}
