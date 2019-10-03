<?php

use App\File;
use App\Calculate;
use App\Exceptions\FileException;

require __DIR__ . '/vendor/autoload.php';

try {
    $file = new File($argv);
    $data = $file->readByLine();
    $explodeData = $file->explodeData($data, ",");
    usort($explodeData, ["App\File", "sortFirstColumn"]);

    foreach ($explodeData as $line) {
        $calculate = new Calculate($line);
        printf("%s \r\n", $calculate->commission());
    }
    
} catch (CalculateException $e) {
    echo $e->getMessage();
} catch (FileException $e) {
    echo $e->getMessage();
} catch (Exception $e) {
    echo $e->getMessage();
}
