<?php

namespace Test\RemoteCollectionStream\Domain\Fixture;

use RemoteCollectionStream\Domain\StreamCollection;

/**
 * @author Lukas Lukac <services@trki.sk>
 * @since  2017-06-20
 */
class DummyCollection implements StreamCollection
{
    private $elements = [];

    /**
     * @param string $element
     */
    public function addElement(string $element)
    {
        $this->elements[] = $element;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->elements);
    }
}
