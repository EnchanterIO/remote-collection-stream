<?php

namespace Test\RemoteCollectionStream\Domain\Fixture;

/**
 * @author Lukas Lukac <services@trki.sk>
 * @since  2017-06-20
 */
class DummyCollectionRepository
{
    private $allElements = [];

    /**
     * @param int $elementsCount
     */
    public function __construct(int $elementsCount)
    {
        for ($i = 1; $i <= $elementsCount; $i++) {
            $this->allElements[] = $i;
        }
    }

    /**
     * @param int $offset
     * @param int $limit
     *
     * @return DummyCollection
     */
    public function getAll(int $offset, int $limit): DummyCollection
    {
        $collection      = new DummyCollection();
        $collectionChunk = array_slice($this->allElements, $offset, $limit);

        foreach ($collectionChunk as $element) {
            $collection->addElement($element);
        }

        return $collection;
    }
}
