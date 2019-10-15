<?php

declare(strict_types=1);

namespace EzPlatform\Menu;

use EzPlatform\MenuBundle\Events\ConfigureMenuEvent;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\Event;

abstract class AbstractBuilder
{
    /** @var MenuItemFactory */
    protected $factory;

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /**
     * @param MenuItemFactory $factory
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(MenuItemFactory $factory, EventDispatcherInterface $eventDispatcher)
    {
        $this->factory = $factory;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param string $name
     * @param Event $event
     */
    protected function dispatchMenuEvent(string $name, Event $event): void
    {
        $this->eventDispatcher->dispatch($name, $event);
    }

    /**
     * @param ItemInterface $menu
     *
     * @return ConfigureMenuEvent
     */
    protected function createConfigureMenuEvent(ItemInterface $menu, array $options = []): ConfigureMenuEvent
    {
        return new ConfigureMenuEvent($this->factory, $menu, $options);
    }

    /**
     * @param array $options
     *
     * @return ItemInterface
     */
    public function build(array $options): ItemInterface
    {
        $menu = $this->createStructure($options);

        $this->dispatchMenuEvent($this->getConfigureEventName(), $this->createConfigureMenuEvent($menu, $options));

        return $menu;
    }

    abstract protected function getConfigureEventName(): string;

    abstract protected function createStructure(array $options): ItemInterface;
}
