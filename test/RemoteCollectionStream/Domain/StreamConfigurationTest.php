<?php

namespace Test\RemoteCollectionStream\Domain;

use RemoteCollectionStream\Domain\StreamConfiguration;
use PHPUnit\Framework\TestCase;

/**
 * @author Lukas Lukac <services@trki.sk>
 * @since  2017-06-20
 *
 * @group RemoteCollectionStream
 *
 * @coverDefaultClass
 * @covers \RemoteCollectionStream\Domain\StreamConfiguration::__construct()
 */
class StreamConfigurationTest extends TestCase
{
    /**
     * @test
     * @covers \RemoteCollectionStream\Domain\StreamConfiguration::__construct()
     *
     * @expectedException \InvalidArgumentException
     */
    public function streamConfigurationWithInvalidFetchLimitShouldThrowException(): void
    {
        new StreamConfiguration(0);
    }
}
