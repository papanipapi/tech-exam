<?php

namespace App\Resources;

use App\Exception\CommissionException;

class Config
{
    protected $config;

    public function __construct($file)
    {
        if (!file_exists($file)) {
            throw new CommissionException('File not found.');
        }

        $this->config = parse_ini_file($file, true);
    }

    public function __get($property)
    {
        return array_key_exists($property, $this->config) ? $this->config[$property] : '';
    }
}