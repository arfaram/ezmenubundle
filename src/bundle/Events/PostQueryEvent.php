<?php

declare(strict_types=1);

namespace EzPlatform\MenuBundle\Events;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event triggered after building navigation query. Provides extensibility point for query customization.
 */
class PostQueryEvent extends Event
{

    /** @var string */
    public static $queryName;

    /** @var \eZ\Publish\API\Repository\Values\Content\LocationQuery */
    private $query;

    /**
     * QueryEvent constructor.
     * @param \eZ\Publish\API\Repository\Values\Content\LocationQuery $query
     */
    public function __construct(LocationQuery $query)
    {
        $this->query = $query;
    }

    /** @return \eZ\Publish\API\Repository\Values\Content\LocationQuery */
    public function getQuery(): LocationQuery
    {
        return $this->query;
    }
}
