<?php

declare(strict_types=1);

namespace EzPlatform\MenuExample\EventListener;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzPlatform\MenuBundle\Events\ConfigureMenuEvent;
use EzPlatform\Menu\AbstractBuilder;
use EzPlatform\Menu\MenuItemFactory;
use EzPlatform\Menu\MenuItems;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class StaticMenuBuilder extends AbstractBuilder
{
    /* Main Menu / Content */
    const ITEM_ROOT = 'Home';

    /** @var ConfigResolverInterface */
    private $configResolver;

    /** @var MenuItems */
    private $menuItems;

    /**
     * MainMenuBuilder constructor.
     *
     * @param \EzPlatform\Menu\MenuItemFactory $factory
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver
     * @param \EzPlatform\Menu\MenuItems $menuItems
     */
    public function __construct(
        MenuItemFactory $factory,
        EventDispatcherInterface $eventDispatcher,
        ConfigResolverInterface $configResolver,
        MenuItems $menuItems

    ) {
        parent::__construct($factory, $eventDispatcher);

        $this->configResolver = $configResolver;
        $this->menuItems = $menuItems;
    }

    /**
     * @return string
     */
    protected function getConfigureEventName(): string
    {
        return ConfigureMenuEvent::$menuName = 'site.static_menu';
    }

    /**
     * @param array $options
     * @return \Knp\Menu\ItemInterface
     */
    public function createStructure(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild(self::ITEM_ROOT, array('uri' => '/'));
        //or use symfony route: ['route' => 'route_example']
        $menu[self::ITEM_ROOT]->addChild('Shop', ['uri' => '/']);
        $menu[self::ITEM_ROOT]->addChild('Live', ['uri' => '/']);
        $menu[self::ITEM_ROOT]->addChild('Radio', ['uri' => '/']);
        $menu[self::ITEM_ROOT]->addChild('Google', ['uri' => 'http://google.de']);


        $menu->addChild(
            'ez_link',
            [
                'label' => 'ezlink.translation.key',
                'uri' => 'http://ibexa.co',
                'linkAttributes' => [
                    'class' => 'test_class another_class',
                    'data-property' => 'value',
                    'taget' => '_blank',
                ],
                'extras' => [
                    'translation_domain' => 'menu',
                ],
            ]
        );

        $menu->setChildrenAttribute('class', 'nav2');

        return $menu;
    }
}
