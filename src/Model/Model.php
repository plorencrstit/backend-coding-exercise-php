<?php
namespace App\Model;

abstract class Model
{
    protected $id;

    public function getId()
    {
        return $this->id;
    }
}
