<?php

declare(strict_types=1);

namespace EzPlatform\MenuExample\EventSubscriber;

use eZ\Publish\API\Repository\Values\Content;
use EzPlatform\MenuBundle\Events\PostQueryEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PostQuerySubscriber implements EventSubscriberInterface
{
    /**
     * @return array|string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            'main' => 'afterQueryBuildConfigure',
        ];
    }

    public function afterQueryBuildConfigure(PostQueryEvent $event)
    {
        $query = $event->getquery();

        $options = $event->getOptions();

        $query->filter->criteria[] = new Content\Query\Criterion\Field('bool_field', Content\Query\Criterion\Operator::EQ, 1);

        $query->sortClauses = [new Content\Query\SortClause\Location\Priority(Content\Query::SORT_ASC)];

    }
}
