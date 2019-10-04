<?php

namespace App;

use App\User;
use App\Config;
use App\Operation;

class CommissionCalculator
{
    protected $user;
    protected $operation;
    protected $settings;

    protected $fee;
    protected $commissionFee;
    protected $commissionFeeMinAmount;
    protected $commissionFeeMinCurrency;
    protected $commissionFeeMaxAmount;
    protected $commissionFeeMaxCurrency;

    public function __construct(User $user, Operation $operation, Config $config)
    {
        $this->user = $user;
        $this->operation = $operation;
        $this->settings = $config;
    }

    public function calculate()
    {
        if (!$this->isSupportedCurrency()) {
            throw new \Exception('Unsupported currency.');
        }

        $this->calculateCashIn();
        $this->calculateCashOut();

        $this->calculateFee();

        $this->calculateMinAmount();
        $this->calculateMaxAmount();
    }

    private function calculateCashIn()
    {
        if (!$this->operation->isCashIn()) {
            return;
        }

        $this->commissionFee = $this->settings->config['CASH_IN_COMMISSION_FEE'];
        $this->commissionFeeMaxAmount = $this->settings->config['CASH_IN_MAX_COMMISSION_AMOUNT'];
        $this->commissionFeeMaxCurrency = $this->settings->config['CASH_IN_MAX_COMMISSION_CURRENCY'];
    }

    private function calculateCashOut()
    {
        if (!$this->operation->isCashOut()) {
            return;
        }

        $this->commissionFee = $this->settings->config['CASH_OUT_COMMISSION_FEE'];

        $this->calculateCashOutNatural();
        $this->calculateCashOutLegal();
    }

    private function calculateCashOutNatural()
    {
        if (!$this->user->isNatural()) {
            return;
        }
    }

    private function calculateCashOutLegal()
    {
        if (!$this->user->isLegal()) {
            return;
        }

        $this->commissionFeeMinAmount = $this->settings->config['CASH_OUT_LEGAL_MIN_COMMISSION_AMOUNT'];
        $this->commissionFeeMinCurrency = $this->settings->config['CASH_OUT_LEGAL_MIN_COMMISSION_CURRENCY'];
    }

    private function calculateFee()
    {
        $this->fee = $this->operation->getAmount() * ($this->commissionFee / 100);
    }

    private function calculateMinAmount()
    {
        if (empty($this->commissionFeeMinAmount)) {
            return true;
        }

        if ($this->commissionFeeMinAmount < $this->fee) {
            return true;
        }

        $this->fee = $this->commissionFeeMinAmount;
    }

    private function calculateMaxAmount()
    {
        if (empty($this->commissionFeeMaxAmount)) {
            return true;
        }

        if ($this->commissionFeeMaxAmount > $this->fee) {
            return true;
        }

        $this->fee = $this->commissionFeeMaxAmount;
    }

    private function isSupportedCurrency()
    {
        if (in_array($this->operation->getCurrency(), $this->settings->config['SUPPORTED_CURRENCY'])) {
            return true;
        }

        return false;
    }

    public function getFee()
    {
        return $this->fee;
    }
}