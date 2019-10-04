<?php

namespace App;

class Config
{
    public $config;

    public function __construct($file)
    {
        if (!file_exists($file)) {
            throw new \Exception('File not found.');
        }

        $this->config = parse_ini_file($file, true);
    }
}