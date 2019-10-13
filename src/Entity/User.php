<?php

namespace App\Entity;

class User
{
    protected $id;
    protected $type;

    public function getId() : int
    {
        return $this->id;
    }

    public function setId(int $id) : void
    {
        $this->id = $id;
    }

    public function getType() : string
    {
        return $this->type;
    }

    public function setType(string $type) : void
    {
        $this->type = $type;
    }

    public function isNatural() : bool
    {
        if ($this->getType() === 'natural') {
            return true;
        }

        return false;
    }

    public function isLegal() : bool
    {
        if ($this->getType() === 'legal') {
            return true;
        }

        return false;
    }
}