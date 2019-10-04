<?php

require __DIR__ . '/vendor/autoload.php';

use App\User;
use App\Config;
use App\Format;
use App\Operation;
use App\ArrayUtil;
use App\CommissionCalculator;

try {
    
    if (!isset($argv[1])) {
        throw new \Exception('Invalid file.');
    }

    $filename = $argv[1];

    if (!file_exists($filename)) {
        throw new \Exception('File not found.');
    }

    $csv = array_map('str_getcsv', file($filename));

    usort($csv, ["App\ArrayUtil", "sortByFirstColumn"]);

    $config = new Config('./config.ini');

    foreach ($csv as $row) {
        list($operationDate, $userId, $userType, $operationType,
            $operationAmount, $operationCurrency) = array_pad($row, 6, null);

        $operation = new Operation();
        $operation->setDate($operationDate);
        $operation->setType($operationType);
        $operation->setAmount($operationAmount);
        $operation->setCurrency($operationCurrency);

        $user = new User();
        $user->setId($userId);
        $user->setType($userType);

        $calculator = new CommissionCalculator($user, $operation, $config);
        $calculator->calculate();

        fwrite(STDOUT, Format::currencyFormat($calculator->getFee()) . PHP_EOL);
    }

} catch (\Exception $e) {
    echo $e->getMessage();
}