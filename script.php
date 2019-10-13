<?php

require __DIR__ . '/vendor/autoload.php';

use App\Commission;
use App\Entity\User;
use App\Entity\Operation;
use App\Helper\ArrayUtil;
use App\Resources\Config;
use App\Exception\CommissionException;

try {
    
    if (!isset($argv[1])) {
        throw new \Exception('Invalid file.');
    }

    $filename = $argv[1];

    if (!file_exists($filename)) {
        throw new \Exception('File not found.');
    }

    $csv = array_map('str_getcsv', file($filename));

    usort($csv, ["App\Helper\ArrayUtil", "sortByFirstColumn"]);

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

        $commission = new Commission($user, $operation, $config);
        $commission->calculate();

        fwrite(STDOUT, $commission->getFormattedAmount() . PHP_EOL);
    }

} catch (CommissionException $e) {
    echo $e->getMessage();
} catch (\Exception $e) {
    echo $e->getMessage();
}