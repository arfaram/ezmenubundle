<?php

declare(strict_types=1);

namespace EzPlatform\MenuExample\EventListener;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzPlatform\Menu\AbstractBuilder;
use EzPlatform\MenuBundle\Events\ConfigureMenuEvent;
use EzPlatform\Menu\MenuItemFactory;
use EzPlatform\Menu\MenuItems;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FooterMenuBuilder extends AbstractBuilder
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
        return ConfigureMenuEvent::$menuName = 'site.footer_menu';
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

        if (!isset($options['level'])) {
            throw new \Exception('You should specify the level name in the template');
        }

        $this->menuItems->setLevel($options['level']);

        if (!isset($options['thisLocationId']) && !$this->configResolver->hasParameter('thisLocationId.menu', $options['level'])) {
            throw new \Exception('You should specify the option "thisLocationId" in the template or define it in parameters in '.$options['level'].'.default.thisLocationId.menu".');
        }

        $startLocation = '';

        if (isset($options['thisLocationId'])) {
            $startLocation = $options['thisLocationId'];
        }

        if ($this->configResolver->hasParameter('thisLocationId.menu', $options['level'])) {
            $startLocation = $this->configResolver->getParameter('thisLocationId.menu', $options['level']);
            $options['thisLocationId'] = $startLocation;
        }

        $menu = $this->menuItems->createMenu(
            $menu,
            $startLocation,
            $options
        );

        $menu->setChildrenAttribute('class', 'nav');

        return $menu;
    }
}
