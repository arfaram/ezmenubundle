<?php

declare(strict_types=1);

namespace EzPlatform\MenuBundle\Events;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Event triggered after building main menus. Provides extensibility point for menus' customization.
 */
class ConfigureMenuEvent extends Event
{
    /** @var string */
    public static $menuName;

    /** @var FactoryInterface */
    private $factory;

    /** @var ItemInterface */
    private $menu;

    /** @var array|null */
    private $options;

    /**
     * @param FactoryInterface $factory
     * @param ItemInterface $menu
     * @param array $options
     */
    public function __construct(FactoryInterface $factory, ItemInterface $menu, array $options = [])
    {
        $this->factory = $factory;
        $this->menu = $menu;
        $this->options = $options;
    }

    /**
     * @return FactoryInterface
     */
    public function getFactory(): FactoryInterface
    {
        return $this->factory;
    }

    /**
     * @return ItemInterface
     */
    public function getMenu(): ItemInterface
    {
        return $this->menu;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options ?? [];
    }
}
