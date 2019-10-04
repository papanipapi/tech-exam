<?php

namespace App;

class Operation
{
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    public function getDate()
    {
        return $this->date;
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

    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function isCashIn()
    {
        if ($this->getType() === 'cash_in') {
            return true;
        }

        return false;
    }

    public function isCashOut()
    {
        if ($this->getType() === 'cash_out') {
            return true;
        }
        
        return false;
    }
}