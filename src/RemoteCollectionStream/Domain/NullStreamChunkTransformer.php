<?php

namespace RemoteCollectionStream\Domain;

/**
 * @package RemoteCollectionStream\Domain
 * @author  Lukas Lukac <services@trki.sk>
 * @since   2017-06-20
 */
class NullStreamChunkTransformer implements StreamChunkTransformer
{
    /**
     * @param StreamCollection $collection
     *
     * @return StreamCollection
     */
    public function transform(StreamCollection $collection): StreamCollection
    {
        return $collection;
    }
}
