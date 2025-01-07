<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use DI\Container;

class TestCase extends BaseTestCase
{
    protected $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->container = new Container();
    }
} 