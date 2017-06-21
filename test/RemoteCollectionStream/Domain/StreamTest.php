<?php

namespace Test\RemoteCollectionStream\Domain;

use RemoteCollectionStream\Domain\Stream;
use RemoteCollectionStream\Domain\StreamConfiguration;
use PHPUnit\Framework\TestCase;
use Test\RemoteCollectionStream\Domain\Fixture\DummyCollection;
use Test\RemoteCollectionStream\Domain\Fixture\DummyCollectionRepository;
use Test\RemoteCollectionStream\Domain\Fixture\InvalidCollection;

/**
 * @author Lukas Lukac <services@trki.sk>
 * @since  2017-06-20
 *
 * @group RemoteCollectionStream
 *
 * @coverDefaultClass
 * @covers \RemoteCollectionStream\Domain\Stream::__construct()
 */
class StreamTest extends TestCase
{
    /**
     * @var Stream
     */
    private $stream;

    /**
     * @covers \RemoteCollectionStream\Domain\Stream::__construct()
     */
    public function setUp()
    {
        parent::setUp();

        $this->stream = new Stream();
    }

    /**
     * @test
     * @covers \RemoteCollectionStream\Domain\Stream::stream()
     * @covers \RemoteCollectionStream\Domain\Stream::verifyCallable()
     *
     * @expectedException \LogicException
     */
    public function streamShouldThrowExceptionOnInvalidCallable(): void
    {
        $streamCollectionGenerator = $this->stream->stream(
            new StreamConfiguration(1),
            function ($offset) {
                return $this->fetchDummyCollection();
            }
        );

        $this->activateGenerator($streamCollectionGenerator);
    }

    /**
     * @test
     * @covers \RemoteCollectionStream\Domain\Stream::stream()
     * @covers \RemoteCollectionStream\Domain\Stream::verifyFetchedCollection()
     *
     * @expectedException \LogicException
     */
    public function streamShouldThrowExceptionOnInvalidCollection(): void
    {
        $streamCollectionGenerator = $this->stream->stream(
            new StreamConfiguration(1),
            function ($offset, $limit) {
                return $this->fetchInvalidCollection();
            }
        );

        $this->activateGenerator($streamCollectionGenerator);
    }

    /**
     * @test
     * @covers \RemoteCollectionStream\Domain\Stream::stream()
     * @covers \RemoteCollectionStream\Domain\Stream::verifyFetchedCollection()
     * @covers \RemoteCollectionStream\Domain\Stream::verifyCallable()
     * @covers \RemoteCollectionStream\Domain\Stream::isLastCollection()
     */
    public function streamShouldProperlyIncrementPointerAndStopOnLastCollection(): void
    {
        $totalElementsCount         = rand(0, 100);
        $collectionFetchLimit       = rand(0, 200);
        $expectedCollectionsCount   = ceil($totalElementsCount / $collectionFetchLimit);
        $generatedCollectionsCount  = 0;
        $collectionSourceRepository = new DummyCollectionRepository($totalElementsCount);

        $streamCollectionGenerator = $this->stream->stream(
            new StreamConfiguration($collectionFetchLimit),
            function ($offset, $limit) use ($collectionSourceRepository) {
                return $collectionSourceRepository->getAll($offset, $limit);
            }
        );

        foreach ($streamCollectionGenerator as $streamCollection) {
            $generatedCollectionsCount++;
        }

        // Stream always executes $fetchCollection() at least once
        if ($expectedCollectionsCount === 0) {
            $expectedCollectionsCount = 1;
        }

        // Stream will execute $fetchCollection() on a collection of 100 elements with Fetch limit 100 twice
        if ($totalElementsCount === $collectionFetchLimit) {
            $expectedCollectionsCount++;
        }

        $this->assertEquals($expectedCollectionsCount, $generatedCollectionsCount);
    }

    /**
     * @return DummyCollection
     */
    private function fetchDummyCollection(): DummyCollection
    {
        return new DummyCollection();
    }

    /**
     * @return InvalidCollection
     */
    private function fetchInvalidCollection(): InvalidCollection
    {
        return new InvalidCollection();
    }

    /**
     * @param \Generator $streamCollectionGenerator
     *
     * @return void
     */
    private function activateGenerator(\Generator $streamCollectionGenerator): void
    {
        foreach ($streamCollectionGenerator as $streamCollection) {
        }
    }
}
