<?php

namespace App;

class User
{
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function isNatural()
    {
        if ($this->getType() === 'natural') {
            return true;
        }

        return false;
    }

    public function isLegal()
    {
        if ($this->getType() === 'legal') {
            return true;
        }
        
        return false;
    }
}