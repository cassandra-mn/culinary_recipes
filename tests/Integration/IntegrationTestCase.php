<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use Tests\Support\Agent;
use Tests\Support\TestDatabase;
use Tests\Support\TestServer;

abstract class IntegrationTestCase extends TestCase
{
    protected Agent $agent;

    public static function setUpBeforeClass(): void
    {
        TestServer::boot();
    }

    protected function setUp(): void
    {
        TestDatabase::truncate();

        $this->agent = new Agent(TestServer::url());
    }

    public static function tearDownAfterClass(): void
    {
        TestDatabase::disconnect();
    }
}
