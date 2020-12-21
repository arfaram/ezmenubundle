
- [Default usage- Template parameters](#default-usage-template-parameters)
- [Custom usage - PHP Builder](#custom-usage-php-builder)
- [Add additional items to the repository menu](#add-additional-items-to-the repository-menu)
- [Menu with custom start locationId](#menu-with-custom-start-locationId)
- [Static menu using knpMenuBundle (Symfony Routes)](#static-menu-using-knpMenuBundle-symfony-routes)

**Note:** Check the [extend](extend.md) documentation for additional or custom search criterion and sort results.

## Default usage - Template parameters

Define first the ContentType(s) that has to be displayed in the navigation:

_Example:_
```
parameters:
    main.default.contenttypes_identifier.menu: ['folder', 'products', 'article', 'product_item']
```

Add following code to the base layout

```
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


## Custom usage - PHP Builder
One of the option is to define a custom menu `site.my_menu`using template options as described above **or** pass the different options using a custom builder class.

The template definition will be limited to:
```
{% set menu = knp_menu_get('site.my_menu')%}
```

The options in the builder class can be defined like below example:

```
    private static $options = [
        'level' => 'custom',
        'depth' => 1,
        'displayChildrenOnClick' => true
    ];
```

A custom builder example is provided in this bundle under `EzPlatform/Doc/Example/EventListener/FooterMenuBuilder.php` 

The menu name should be also defined in 

```
protected function getConfigureEventName(): string
{
    return ConfigureMenuEvent::$menuName = 'site.my_menu';
}
```

**Note:** below classes can be injected in the builder and they are very useful:
* `RequestStack`: to have access to the location `$request->attributes->get('location')` 
* `ConfigResolverInterface`: it allows you to define above options as siteaccess aware parameters.

## Add additional items to the repository menu

An example is provided in this bundle under `EzPlatform/Doc/Example/EventListener/ExtraMainMenuListener.php`

Below service definition append a new menu item(s) to the existing `site.main_menu`

```
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
```
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

```
parameters:
    footer.default.contenttypes_identifier.menu:
        - 'folder'
    footer.default.thisLocationId.menu: XX #see below note

```

Note: or add `'thisLocationId':XX` in the template menu options.

## Static menu using knpMenuBundle (Symfony Routes)

An example is provided in this bundle under `EzPlatform/Doc/Example/EventListener/StaticMenuBuilder.php`

Template:

```
    {{ knp_menu_render('site.static_menu', {
        'currentAsLink': false,
        'currentClass': 'active'
    }) }}
```

The service definition is available in `Doc/Example/menu.yaml`:`#Static menu using knpMenuBundle (Symfony Routes)`

Note: `site.static_menu`: You can instead use your own menu name 

![EZ Platform static menu](images/static_menu.png "EZ Platform menu navigation")