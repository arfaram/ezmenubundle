<?php

declare(strict_types=1);

namespace EzPlatform\MenuBundle\Doc\Example;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzPlatform\Menu\AbstractBuilder;
use EzPlatform\MenuBundle\Events\ConfigureMenuEvent;
use EzPlatform\Menu\MenuItemFactory;
use EzPlatform\Menu\MenuItems;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class FooterMenuBuilder
 *
 * @package EzPlatform\MenuBundle\Doc\Example
 */
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
            throw new \Exception('You should specify the level in the template');
        }


        //$level = isset($options['level']) && $options['level'] ? $options['level'] : 'main';

        $this->menuItems->setLevel($options['level']);

        if (!isset($options['startLocationId']) and  !$this->configResolver->hasParameter('location_id.menu', $options['level'])) {
            throw new \Exception('You should specify the option "startLocationId" in the template or define it in parameters in "level.default.location_id.menu".');
        } else {
            $startLocation = isset($options['startLocationId']) ? $options['startLocationId'] : $this->configResolver->getParameter('location_id.menu', $options['level']);
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
