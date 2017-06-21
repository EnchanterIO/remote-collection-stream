<?php

namespace RemoteCollectionStream\Domain;

/**
 * Continuously streams remote collection in user defined chunks and yields them using Generators.
 *
 * @package RemoteCollectionStream\Domain
 * @author  Lukas Lukac <services@trki.sk>
 * @since   2017-06-20
 */
class Stream
{
    /**
     * @var StreamChunkTransformer
     */
    private $streamChunkTransformer;

    /**
     * @param StreamChunkTransformer $chunkTransformer
     */
    public function __construct(StreamChunkTransformer $chunkTransformer = null)
    {
        if (is_null($chunkTransformer)) {
            $chunkTransformer = new NullStreamChunkTransformer();
        }

        $this->streamChunkTransformer = $chunkTransformer;
    }

    /**
     * @param StreamConfiguration $config
     * @param callable            $fetchCollection
     *
     * @throws \LogicException On invalid given Collection or Callable
     *
     * @return \Generator
     */
    public function stream(StreamConfiguration $config, callable $fetchCollection): \Generator
    {
        $this->verifyCallable($fetchCollection);

        $collectionPointer = 0;

        do {
            /** @var StreamCollection $collection */
            $collection = $fetchCollection($collectionPointer, $config->fetchLimit());

            $this->verifyFetchedCollection($collection);

            $collectionPointer += $collection->count();

            yield $this->streamChunkTransformer->transform($collection);
        } while (!$this->isLastCollection($collection, $config));
    }

    /**
     * @param StreamCollection    $collection
     * @param StreamConfiguration $config
     *
     * @return bool
     */
    private function isLastCollection(StreamCollection $collection, StreamConfiguration $config): bool
    {
        return $collection->count() !== $config->fetchLimit();
    }

    /**
     * @param callable $fetchCollection
     *
     * @throws \LogicException
     *
     * @return void
     */
    private function verifyCallable(callable $fetchCollection): void
    {
        if ((new \ReflectionFunction($fetchCollection))->getNumberOfParameters() !== 2) {
            throw new \LogicException(
                sprintf('Stream Callable requires 2 arguments. "$offset" and "$limit".')
            );
        }
    }

    /**
     * @param $collection
     *
     * @throws \LogicException
     */
    private function verifyFetchedCollection($collection)
    {
        if (!$collection instanceof StreamCollection) {
            throw new \LogicException(
                sprintf(
                    '%s must implement %s to be able to stream resources.',
                    get_class($collection),
                    StreamCollection::class
                )
            );
        }
    }
}
