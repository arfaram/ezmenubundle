## Extending Queries using custom Criteria and Sort clauses

The default query is built using: 
* `ContentTypeIdentifier`
* `ParentLocationId`
* `LanguageCode`

Only the`ContentTypeIdentifier` array can be set from the configuration. More information in the [Default usage](usage.md#default-usage)

It is possible to add a custom post query EventSubscriber or EventListener using the `PostQueryEvent`. The`$event->getquery()` provides the **default query** and the `$event->getOptions()` contains the **options** before execution. Now you can amend the original query or add custom criteria, sortClauses, limit, offset, performCount etc. Check Ibexa Documentation for query properties [Query.php](https://github.com/ezsystems/ezpublish-kernel/blob/master/eZ/Publish/API/Repository/Values/Content/Query.php)

**Note:** The **post query** listener:
* will be dispatched in each subtree level. 
* The default options(`depth`, `location` and `displayChildrenOnClick`) can not be changed here. 
* This listener is the best place for additional criteria and custom options related to the searchService. Template options can be added one step before either in the builder class or from the template.

**Tip: You can inject these options from the template, builder class or services/controllers(see PHP Integration usage)**

Two similar examples are provided in this bundle: 

**EventSubscriber**:

`EzPlatform/Doc/Example/EventSubscriber/PostQuerySubscriber.php`

```php
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
The EventSubscriber should be defined using `autoconfigure: true` option in the service definition.

**EventListener**:

`EzPlatform/Doc/Example/EventListener/PostQueryListener.php`


```php
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

Next, Tag the post query listener and assign it to the right level(event) name:

```yaml
services:
    EzPlatform\MenuBundle\Doc\Example\EventListener\PostQueryListener:
        tags:
            - { name: 'kernel.event_listener', event: 'main', method: 'afterQueryBuildConfigure' }
```