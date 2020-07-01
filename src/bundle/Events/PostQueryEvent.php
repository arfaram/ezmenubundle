<?php

declare(strict_types=1);

namespace EzPlatform\MenuBundle\Events;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Event triggered after building navigation query. Provides extensibility point for query customization.
 */
class PostQueryEvent extends Event
{
    /** @var string */
    public static $queryName;

    /** @var LocationQuery */
    private $query;

    /**
     * @param LocationQuery $query
     */
    public function __construct(LocationQuery $query)
    {
        $this->query = $query;
    }

    public function getQuery(): LocationQuery
    {
        return $this->query;
    }
}
