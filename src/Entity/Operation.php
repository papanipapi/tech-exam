<?php

namespace App\Entity;

class Operation
{
    protected $date;
    protected $type;
    protected $amount;
    protected $currency;

    public function getDate() : string
    {
        return $this->date;
    }

    public function setDate(string $date) : void
    {
        $this->date = $date;
    }

    public function getType() : string
    {
        return $this->type;
    }

    public function setType(string $type) : void
    {
        $this->type = $type;
    }

    public function getAmount() : int
    {
        return $this->amount;
    }

    public function setAmount(int $amount) : void
    {
        $this->amount = $amount;
    }

    public function getCurrency() : string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency) : void
    {
        $this->currency = $currency;
    }

    public function isCashIn() : bool
    {
        if ($this->getType() === 'cash_in') {
            return true;
        }

        return false;
    }

    public function isCashOut() : bool
    {
        if ($this->getType() === 'cash_out') {
            return true;
        }

        return false;
    }
}