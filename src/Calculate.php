<?php

namespace App;

use App\Exceptions\CalculateException;

class Calculate
{
    const SUPPORTED_CURRENCY = [
        'EUR'
    ];

    const CASH_IN_COMMISSION_FEE = 0.03;
    const CASH_IN_MAX_COMMISSION_FEE = 5;
    const CASH_OUT_COMMISSION_FEE = 0.3;
    const CASH_OUT_LEGAL_MIN_COMMISSION_FEE = 0.5;

    protected $date;
    protected $userId;
    protected $userType;
    protected $type;
    protected $amount;
    protected $currency;
    protected $commissionFee;

    public function __construct($data = [])
    {
        if (count($data) < 6) {
            return false;
            // throw new CalculateException('Invalid parameter.');
        }

        $this->date = $data[0];
        $this->userId = $data[1];
        $this->userType = $data[2];
        $this->type = $data[3];
        $this->amount = $data[4];
        $this->currency = $data[5];

        return $this;
    }

    public function isSupportedCurrency()
    {
        if (in_array($this->currency, self::SUPPORTED_CURRENCY)) {
            return true;
        }

        return false;
    }

    public function isMaxCashIn($fee)
    {
        if ($fee > self::CASH_IN_MAX_COMMISSION_FEE) {
            return true;
        }

        return false;
    }

    public function isMinCashOut($fee)
    {
        if ($fee < self::CASH_OUT_LEGAL_MIN_COMMISSION_FEE) {
            return true;
        }

        return false;
    }

    public function computeFee($commision)
    {
        return $this->amount * ($commision / 100);
    }

    public function cashIn()
    {
        $fee = $this->computeFee(self::CASH_IN_COMMISSION_FEE);
        if ($this->isMaxCashIn($fee)) {
            return self::CASH_IN_MAX_COMMISSION_FEE;
        }

        return $fee;
    }

    public function cashOut()
    {
        $fee = $this->computeFee(self::CASH_OUT_COMMISSION_FEE);

        switch ($this->userType) {
            case 'natural':
                break;
            case 'legal':
                if ($this->isMinCashOut($fee)) {
                    return self::CASH_OUT_LEGAL_MIN_COMMISSION_FEE;
                }
                break;
            default:
                return false;
                // throw new CalculateException('Invalid user type.');
        }

        return $fee;
    }

    public function roundUp($value, $places=2) {
        if ($places < 0) { $places = 0; }
        $mult = pow(10, $places);
        return ceil($value * $mult) / $mult;
    }

    public function commission()
    {
        if (!$this->isSupportedCurrency()) {
            return false;
            // throw new CalculateException('Unsupported currency.');
        }

        switch ($this->type) {
            case 'cash_out':
                $fee = $this->cashOut();
                break;
            case 'cash_in':
                $fee = $this->cashIn();
                break;
            default:
                return false;
                // throw new CalculateException('Invalid operation type.');
        }

        $ceilFee = $this->roundUp($fee);
        return number_format($ceilFee, 2);
    }
}
