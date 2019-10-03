<?php

namespace App;

use App\Exceptions\FileException;

class File
{
    protected $filename;

    public function __construct($argv)
    {
        if (!isset($argv[1])) {
            throw new FileException('File is required.');
        }
        $this->filename = $argv[1];
    }

    public function readByLine()
    {
        $fh = @fopen($this->filename, 'r');
        if (!$fh) {
            throw new FileException('File not found.');
        }
        
        $line = [];
        while (($buffer = fgets($fh, 4096)) != false) {
            $line[] = trim($buffer);
        }
        fclose ($fh);
        return $line;
    }

    public function explodeData($data, $delimiter)
    {
        $line = [];
        foreach ($data as $lineData) {
            $line[] = explode($delimiter, $lineData);
        }

        return $line;
    }

    public function sortFirstColumn($a, $b)
    {
        return $a[0] > $b[0];
    }
}
