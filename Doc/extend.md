## Extending Queries using custom Criteria and Sort clauses

The default query is built using: 
* `ContentTypeIdentifier`
* `Subtree`
* `Depth`
* `LanguageCode`

Only the`ContentTypeIdentifier` array and `Depth` value can be set respectively from the yaml and the template. More information in the [Default usage](usage.md#default-usage)

It is possible to add a custom post query EventListener using the `PostQueryEvent`. The`$event->getquery()` contains the **default query** and **parameters options** before execution. Now you can amend the original query or add custom criteria, sortClauses, limit, offset, performCount etc. Check Ibexa Documentation for query properties [Query.php](https://github.com/ezsystems/ezpublish-kernel/blob/master/eZ/Publish/API/Repository/Values/Content/Query.php)

Two similar examples are provided in this bundle: 

**EventSubscriber**:

`EzPlatform/Doc/Example/EventSubscriber/PostQuerySubscriber.php`

```
use eZ\Publish\API\Repository\Values\Content;
use EzPlatform\MenuBundle\Events\PostQueryEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PostQuerySubscriber implements EventSubscriberInterface
{
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
```
The EventSubscriber should have the `autoconfigure: true` option in the service definition. 

**EventListener**:

`EzPlatform/Doc/Example/EventListener/PostQueryListener.php`


```
class PostQueryListener
{
    public function afterQueryBuildConfigure(PostQueryEvent $event)
    {
        $query = $event->getquery();
        
        $options = $event->getOptions();

        $query->filter->criteria[] = new Content\Query\Criterion\Field('bool_field', Content\Query\Criterion\Operator::EQ, 1);

        $query->sortClauses = [new Content\Query\SortClause\Location\Priority(Content\Query::SORT_ASC)];

    }
}
```

Next, Tag your post query listener and assign it the right level(event) name:

```
services:
    EzPlatform\MenuBundle\Doc\Example\EventListener\PostQueryListener:
        tags:
            - { name: 'kernel.event_listener', event: 'main', method: 'afterQueryBuildConfigure' }
```