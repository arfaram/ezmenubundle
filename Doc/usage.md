
- [Default usage- Template integration](#default-usage---template-integration)
- [Custom usage - Template integration](#custom-usage---template-integration)
- [Custom usage - PHP integration](#custom-usage---php-integration)
- [Add additional items to the repository menu](#add-additional-items-to-the-repository-menu)
- [Menu with custom start locationId](#menu-with-custom-start-locationId)
- [Static menu using knpMenuBundle (Symfony Routes)](#static-menu-using-knpMenuBundle-symfony-routes)

**Note:** Check the [extend](extend.md) documentation for additional or custom search criterion and sort results.

## Default usage - Template integration

Define first the ContentType(s) that has to be displayed in the navigation:

_Example:_
```yaml
parameters:
    main.default.contenttypes_identifier.menu: ['folder', 'products', 'article', 'product_item']
```

Add following code to the base layout

```yaml
    {% if location is defined %}
        {% set menu = knp_menu_get(
            'site.main_menu',
            [],
            {
                'location':location,
                'displayChildrenOnClick': true,
                'depth': 1,
                'level': 'main'
            }
        )%}

        {{ knp_menu_render(menu, {
            'template': '@EzPlatformMenu/parts/menu/top_menu.html.twig',
            'currentAsLink': true,
            'currentClass': 'active'
        })
        }}
    {% endif %}
    
```

### Parameters:

#### knp_menu_get options

- `location`: the current location
- `displayChildrenOnClick`:
    - true: show subitems only when item is clicked
    - false: show all items in all levels. Performance issue might occur here. You have to use the `detph` option to limit the subtree depth
- `depth`: subtree depth
- `level`: the navigation name to use. The value should be unique per menu. In this example `main`.


#### knp_menu_render options

default template: `top_menu.html.twig` delivered with this bundle.

- `template`: the template to use
- `currentAsLink` If true: display current item as a link otherwise inside a < span >. For **static** menu this option should be set to _false_.
- `currentClass` : the custom css class for active < li > items


## Custom usage - Template integration
One of the option is to define a custom menu `site.my_menu`using template options as described above **or** pass the different options using a custom builder class.

The template definition will be limited to:
```
{% set menu = knp_menu_get('site.my_menu')%}
```

The options in the builder class can be defined like below example:

```php
    private static $options = [
        'level' => 'custom',
        'depth' => 1,
        'displayChildrenOnClick' => true
    ];
```

A custom builder example is provided in this bundle under `EzPlatform/Doc/Example/EventListener/FooterMenuBuilder.php` 

The menu name should be also defined in 

```php
protected function getConfigureEventName(): string
{
    return ConfigureMenuEvent::$menuName = 'site.my_menu';
}
```

**Note:** below classes can be injected in the builder and they are very useful:
* `RequestStack`: to have access to the location `$request->attributes->get('location')` 
* `ConfigResolverInterface`: it allows you to define above options as siteaccess aware parameters.

## Custom usage - PHP integration

The Knp `MenuProviderInterface` allows creation of Menu from Services, Controller etc. Below example gives an integration example:

```php
    /**
     * @var \Knp\Menu\Provider\MenuProviderInterface
     */
    private  $menuServiceProvider;


    public function __construct(
        MenuProviderInterface $menuServiceProvider
    ) {
        $this->menuServiceProvider = $menuServiceProvider;
    }
```

```php
/** @var \Knp\Menu\MenuItem $menu */
$menu = $this->menuServiceProvider->get('site.my_menu');
```
The options must be then defined in the builder class (see above section) or passed to the builder like below example

```php
    $menu = $this->menuServiceProvider->get(
    'site.my_menu',
     [
      'level' => 'main', 
      'depth' => 2,
      ]);
```

A custom builder example is provided in this bundle under `EzPlatform/Doc/Example/EventListener/FooterMenuBuilder.php`

## Add additional items to the repository menu

An example is provided in this bundle under `EzPlatform/Doc/Example/EventListener/ExtraMainMenuListener.php`

Below service definition append a new menu item(s) to the existing `site.main_menu`

```yaml
services:
    _defaults:
        autowire: true
        autoconfigure: true
        public: false
        
        EzPlatform\Menu\MainMenuBuilder:
        tags:
            - { name: 'knp_menu.menu_builder', method: 'build', alias: 'site.main_menu' }
            
        #Append additional menu item(s) to site.main_menu
        EzPlatform\MenuExample\EventListener\ExtraMainMenuListener:
        tags:
            - { name: 'kernel.event_listener', event: 'site.main_menu', method: 'onMenuConfigure' }
```

**Note:** It is possible to use similar Listener to remove or sort menu items.

## Menu with custom start locationId

**Use cases:** Footer Menu, category menu, LandingPage Block menu

An example is provided in this bundle under `EzPlatform/Doc/Example/EventListener/FooterMenuBuilder.php`

Template:
```yaml
    {% if location is defined %}
        {% set menu = knp_menu_get(
            'site.footer_menu',
            [],
            {
                'displayChildrenOnClick': true,
                'level': 'footer'
            }
        )%}

        {{ knp_menu_render(menu, {
            'template': '@EzPlatformMenu/parts/menu/top_menu.html.twig',
            'currentAsLink': true,
            'currentClass': 'active'
        })
        }}
    {% endif %}

```

The service definition is available in `Doc/Example/menu.yaml`:`#Menu with custom start locationId`

- Add the `footer` level, `contenttypes_identifier` and the start locationId `thisLocationId` as parameters

```yaml
parameters:
    footer.default.contenttypes_identifier.menu:
        - 'folder'
    footer.default.thisLocationId.menu: XX #see below note

```

Note: or add `'thisLocationId':XX` in the template menu options.

## Static menu using knpMenuBundle (Symfony Routes)

An example is provided in this bundle under `EzPlatform/Doc/Example/EventListener/StaticMenuBuilder.php`

Template:

```yaml
    {{ knp_menu_render('site.static_menu', {
        'currentAsLink': false,
        'currentClass': 'active'
    }) }}
```

The service definition is available in `Doc/Example/menu.yaml`:`#Static menu using knpMenuBundle (Symfony Routes)`

Note: `site.static_menu`: You can instead use your own menu name 

![EZ Platform static menu](Images/static_menu.png?raw=true "EZ Platform menu navigation")