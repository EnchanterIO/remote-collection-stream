# Description

Continuously streams remote collection in user defined chunks and yields them using Generators.

# Usage

```php
$stream = new Stream();

$campaignsCollectionGenerator = $stream->stream(
    new StreamConfiguration(self::BATCH_FETCH_SIZE_CAMPAIGNS),
    function ($offset, $limit) use ($job) {
        return $this->campaigns->getAll($job, $offset, $limit);
    }
);
```

# Use case, implementation example

Let's say we want to fully stream a bigger MySQL table (few million rows) into a messanging queue in chunks without crushing due to memory limits.

```php
/** @var Chunk $campaignChunk */
foreach ($this->chunksProvider->streamCampaigns($job) as $campaignChunk) {
    try {
        echo $campaignChunk->key() . "\n";

        if ($this->chunksStorage->storeChunk($campaignChunk)) {
            $this->campaignQueue->insert($campaignChunk->serializedValue());
        }
    } catch (StoreChunkException $exception) {
        // Retry the process later
    }
}
```
    
ChunksProvider looks like:

```php
/**
 * Continuously yields Campaign Chunk objects.
 *
 * @param Job $job
 *
 * @return \Generator
 */
public function streamCampaigns(Job $job): \Generator
{
    $stream = new Stream();

    $campaignsCollectionGenerator = $stream->stream(
        new StreamConfiguration(self::BATCH_FETCH_SIZE_CAMPAIGNS),
        function ($offset, $limit) use ($job) {
            return $this->campaigns->getAll($job, $offset, $limit);
        }
    );

    foreach ($campaignsCollectionGenerator as $collection) {
        yield new Chunk($collection);
    }
}
```
And $this->campaigns is a simple Repository fetching data from MySQL using $offset, $limit. Chunk object is just a custom DTO.

```php
/**
 * @param Job $job
 * @param int $offset
 * @param int $limit
 *
 * @return LegacyCampaignsCollection
 */
public function getAll(Job $job, int $offset, int $limit) : LegacyCampaignsCollection;
```

Or it could be a dummy in memory collection

```php
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
```

# Few last words about the implementation

The Stream objects fetches new collections using **callable** which is a bit loose as I can't enforce the callable arguments with an interface
and even though it makes me feel as a JS developer, it gives the implementation side a nice advantage + there is a validation in place verifying
the number of arguments passed.

The ability to combine the call with own parameters:

```php
function ($offset, $limit) use ($job) {
    return $this->campaigns->getAll($job, $offset, $limit);
}
```
If Stream object would require some kind of "CollectionRepository" in the constructor then you would have to use setters, nullable attributes and other evil things in the repository
in order to use additional arguments like filters in the query etc.

**Tests included.**
