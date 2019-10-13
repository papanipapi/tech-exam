<?php

namespace App;

use App\Entity\User;
use App\Entity\Operation;
use App\Resources\Config;
use App\Exception\CommissionException;

class Commission
{
    protected $user;
    protected $operation;
    protected $config;

    protected $fee;
    protected $feeMinAmount;
    protected $feeMinCurrency;
    protected $feeMaxAmount;
    protected $feeMaxCurrency;

    protected $amount;
    protected $formattedAmount;
    
    public function __construct(User $user, Operation $operation, Config $config)
    {
        $this->user = $user;
        $this->operation = $operation;
        $this->config = $config;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getOperation()
    {
        return $this->operation;
    }

    public function setOperation($operation)
    {
        $this->operation = $operation;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function getFee()
    {
        return $this->fee;
    }

    public function setFee($fee)
    {
        $this->fee = $fee;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function getMinAmount()
    {
        return $this->feeMinAmount;
    }

    public function getMinCurrency()
    {
        return $this->feeMinCurrency;
    }

    public function getMaxAmount()
    {
        return $this->feeMaxAmount;
    }

    public function getMaxCurrency()
    {
        return $this->feeMaxCurrency;
    }

    public function setMinFee($amount, $currency)
    {
        $this->feeMinAmount = $amount;
        $this->feeMinCurrency = $currency;
    }

    public function setMaxFee($amount, $currency)
    {
        $this->feeMaxAmount = $amount;
        $this->feeMaxCurrency = $currency;
    }

    public function getFormattedAmount()
    {
        return $this->formattedAmount;
    }

    public function setFormattedAmount()
    {
        $ceilAmount = $this->roundUp($this->getAmount(), $this->getConfig()->ROUND_UP_DECIMAL);
        $this->formattedAmount = number_format($ceilAmount, $this->getConfig()->ROUND_UP_DECIMAL);
    }

    public function calculate()
    {
        if (!$this->isSupportedCurrency()) {
            throw new CommissionException('Unsupported currency.');
        }

        $this->setCommissionFee();
        $this->calculateAmount();
        $this->setConditionalAmount();
        $this->setFormattedAmount();
    }

    public function setCommissionFee()
    {
        $this->setCashInFee();
        $this->setCashOutFee();
    }

    public function setConditionalAmount()
    {
        $this->calculateMinAmount();
        $this->calculateMaxAmount();
    }

    public function setCashInFee()
    {
        if (!$this->getOperation()->isCashIn()) {
            return;
        }

        $this->setFee($this->getConfig()->CASH_IN_COMMISSION_FEE);
        $this->setMaxFee($this->getConfig()->CASH_IN_MAX_COMMISSION_AMOUNT
            , $this->getConfig()->CASH_IN_MAX_COMMISSION_CURRENCY);
    }

    public function setCashOutFee()
    {
        if (!$this->getOperation()->isCashOut()) {
            return;
        }

        $this->setFee($this->getConfig()->CASH_OUT_COMMISSION_FEE);

        $this->setCashOutLegalFee();
    }

    public function setCashOutLegalFee()
    {
        if (!$this->getUser()->isLegal()) {
            return;
        }

        $this->setMinFee($this->getConfig()->CASH_OUT_LEGAL_MIN_COMMISSION_AMOUNT
            , $this->getConfig()->CASH_OUT_LEGAL_MIN_COMMISSION_CURRENCY);
    }

    public function calculateAmount()
    {
        $amount = $this->getOperation()->getAmount() * ($this->getFee() / 100);
        $this->setAmount($amount);
    }

    public function calculateMinAmount()
    {
        if (empty($this->getMinAmount())) {
            return true;
        }

        if ($this->getMinAmount() < $this->getAmount()) {
            return true;
        }

        $this->setAmount($this->getMinAmount());
    }

    public function calculateMaxAmount()
    {
        if (empty($this->getMaxAmount())) {
            return true;
        }

        if ($this->getMaxAmount() > $this->getAmount()) {
            return true;
        }

        $this->setAmount($this->getMaxAmount());
    }

    public function isSupportedCurrency()
    {
        if (in_array($this->getOperation()->getCurrency(), $this->getConfig()->SUPPORTED_CURRENCY)) {
            return true;
        }

        return false;
    }

    public function roundUp($value, $places)
    {
        $mult = pow(10, $places);
        return ceil($value * $mult) / $mult;
    }
}