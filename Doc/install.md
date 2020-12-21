## Requirement

- eZPlatform 3.x (Open Source or EE)
- PHP 7+
- [KnpMenuBundle](http://symfony.com/doc/master/bundles/KnpMenuBundle/index.html) is installed and activated in `bundles.php`(available in default installation)



## Installation steps

### Use composer

```
composer require arfaram/ezmenubundle
```

>This bundle is also available for eZ Platform 2.x. You have to use "composer require arfaram/ezmenubundle:^0.1" and read the README file according to this version

### Activate the Bundle in `bundles.php`

```
return [
    Knp\Bundle\MenuBundle\KnpMenuBundle::class => ['all' => true],
    //...
    EzPlatform\MenuBundle\EzPlatformMenuBundle::class => [ 'all'=> true ],
```

### Update the autoloader

```
composer dumpautoload 
```

### Clear caches (dev+prod)

```
php bin/console c:c -e dev
php bin/console c:c -e prod
```


### Run webpack

```
yarn encore dev
```

**Note**

Be sure that below Entrypoints are correctly generated:

```
    Entrypoint ezplatform-menu-js = runtime.js ezplatform-menu-js.js
    Entrypoint ezplatform-menu-css = runtime.js ezplatform-menu-css.css ezplatform-menu-css.js
```