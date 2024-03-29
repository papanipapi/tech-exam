<?php

namespace App\Tests;

use App\Commission;
use App\Entity\User;
use App\Entity\Operation;
use App\Resources\Config;
use App\Exception\CommissionException;
use PHPUnit\Framework\TestCase;

class CommissionTest extends TestCase
{
    protected $config;

    public function setUp(): void
    {
        $this->config = new Config('./config.ini');
    }

    public function testValid()
    {
        $content = '2016-02-15,1,natural,cash_out,300.00,EUR';
        list($operationDate, $userId, $userType, $operationType,
            $operationAmount, $operationCurrency) = explode(',', $content);

        $operation = new Operation();
        $operation->setDate($operationDate);
        $operation->setType($operationType);
        $operation->setAmount($operationAmount);
        $operation->setCurrency($operationCurrency);

        $user = new User();
        $user->setId($userId);
        $user->setType($userType);

        $calculator = new Commission($user, $operation, $this->config);
        $calculator->calculate();

        $this->assertObjectHasAttribute('date', $operation);
        $this->assertObjectHasAttribute('type', $operation);
        $this->assertObjectHasAttribute('amount', $operation);
        $this->assertObjectHasAttribute('currency', $operation);

        $this->assertObjectHasAttribute('id', $user);
        $this->assertObjectHasAttribute('type', $user);

        $this->assertTrue($operation->isCashOut());
        $this->assertTrue($user->isNatural());
        $this->assertEquals($calculator->getFormattedAmount($calculator->getFormattedAmount()), '0.90');
    }

    public function testUnsupportedCurrency()
    {
        $this->expectException(CommissionException::class);
        $this->expectExceptionMessage('Unsupported currency.');

        $content = '2016-02-15,1,natural,cash_out,300.00,PHP';
        list($operationDate, $userId, $userType, $operationType,
            $operationAmount, $operationCurrency) = array_pad(explode(',', $content), 6, null);

        $operation = new Operation();
        $operation->setDate($operationDate);
        $operation->setType($operationType);
        $operation->setAmount($operationAmount);
        $operation->setCurrency($operationCurrency);

        $user = new User();
        $user->setId($userId);
        $user->setType($userType);

        $calculator = new Commission($user, $operation, $this->config);
        $calculator->calculate();
    }

    public function testRounding()
    {
        $content = '2016-02-15,1,natural,cash_out,91.00,EUR';
        list($operationDate, $userId, $userType, $operationType,
            $operationAmount, $operationCurrency) = explode(',', $content);

        $operation = new Operation();
        $operation->setDate($operationDate);
        $operation->setType($operationType);
        $operation->setAmount($operationAmount);
        $operation->setCurrency($operationCurrency);

        $user = new User();
        $user->setId($userId);
        $user->setType($userType);

        $calculator = new Commission($user, $operation, $this->config);
        $calculator->calculate();

        $this->assertEquals($calculator->getFormattedAmount($calculator->getFormattedAmount()), '0.28');
    }
}