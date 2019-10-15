<?php

declare(strict_types=1);

namespace EzPlatform\Menu;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzPlatform\MenuBundle\Events\ConfigureMenuEvent;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class MainMenuBuilder
 *
 * @package EzPlatform\Menu
 */
class MainMenuBuilder extends AbstractBuilder
{
    /* Main Menu / Content */
    const ITEM_CONTENT = 'main__content';

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
        return ConfigureMenuEvent::$menuName = 'site.main_menu';
    }

    /**
     * @param array $options
     * @return \Knp\Menu\ItemInterface
     */
    public function createStructure(array $options): ItemInterface
    {

        /** @var ItemInterface $menu */
        $menu = $this->factory->createItem(
            self::ITEM_CONTENT,
            [
                'uri' => '/',
                'extras' => [
                    'locationId' => $this->configResolver->getParameter('content.tree_root.location_id')
                ]
            ]
        );

        // you can choose your custom level name and define it in the template when calling knp_menu_get()
        $level = isset($options['level']) && $options['level'] ? $options['level'] : 'main';

        $this->menuItems->setLevel($level);

        $menu = $this->menuItems->createMenu(
            $menu,
            $this->configResolver->getParameter('content.tree_root.location_id'),
            $options
        );


        $menu->setChildrenAttribute('class', 'nav');

        return $menu;
    }
}
