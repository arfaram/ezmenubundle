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
    /** @var LocationQuery */
    private $query;

    /** @var array $options */
    private $options;

    /**
     * PostQueryEvent constructor.
     * @param \eZ\Publish\API\Repository\Values\Content\LocationQuery $query
     * @param array $options
     */
    public function __construct(LocationQuery $query, array $options)
    {
        $this->query = $query;
        $this->options = $options;
    }

    public function getQuery(): LocationQuery
    {
        return $this->query;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getLevel(): string
    {
        return $this->options['level'];
    }
}
