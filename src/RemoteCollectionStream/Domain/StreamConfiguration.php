<?php

namespace RemoteCollectionStream\Domain;

/**
 * @package RemoteCollectionStream\Domain
 * @author  Lukas Lukac <services@trki.sk>
 * @since   2017-06-20
 */
class StreamConfiguration
{
    /**
     * @var int
     */
    private $fetchLimit;

    /**
     * @param int $fetchLimit
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(int $fetchLimit)
    {
        if ($fetchLimit === 0) {
            throw new \InvalidArgumentException('Fetch limit has to be greater than 0!');
        }

        $this->fetchLimit = $fetchLimit;
    }

    /**
     * @return int
     */
    public function fetchLimit(): int
    {
        return $this->fetchLimit;
    }
}
