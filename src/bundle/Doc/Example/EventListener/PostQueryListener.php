<?php
declare(strict_types=1);

namespace EzPlatform\MenuBundle\Doc\Example\EventListener;

use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\Location\Priority;
use EzPlatform\MenuBundle\Events\PostQueryEvent;

class PostQueryListener
{
    public function afterQueryBuildConfigure(PostQueryEvent $event)
    {
        $query = $event->getquery();

        $query->filter->criteria[] = new Query\Criterion\Visibility(Query\Criterion\Visibility::VISIBLE);

        $query->sortClauses = [new Priority(Query::SORT_ASC)];
    }
}
