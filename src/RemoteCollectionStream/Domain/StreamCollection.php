<?php

namespace RemoteCollectionStream\Domain;

/**
 * @package RemoteCollectionStream\Domain
 * @author  Lukas Lukac <services@trki.sk>
 * @since   2017-06-20
 */
interface StreamCollection
{
    /**
     * @return int
     */
    public function count(): int;
}
