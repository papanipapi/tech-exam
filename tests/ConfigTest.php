<?php

namespace App\Tests;

use App\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    protected $config;

    public function setUp(): void
    {
        $this->config = new Config('./config.ini');
    }

    public function testFailure()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('File not found.');

        $this->config = new Config('./config.dev.ini');
    }

    public function testAttribute()
    {
        $this->assertObjectHasAttribute('config', $this->config);
    }
}