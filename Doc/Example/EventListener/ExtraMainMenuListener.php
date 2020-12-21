<?php
declare(strict_types=1);

namespace EzPlatform\MenuExample\EventListener;

use EzPlatform\MenuBundle\Events\ConfigureMenuEvent;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

/**
 * Class ExtraMainMenuListener to demonstrate how to add menu item to an existing menu . This EventListner is dispatched in AbstractBuilder.php (see build() method)
 *
 * @package EzPlatform\MenuBundle\EventListener
 */
class ExtraMainMenuListener implements TranslationContainerInterface
{
    const MAIN_MENU_EXTRA_LINK = 'menu.main_shop';

    /**
     * @param \EzPlatform\MenuBundle\Events\ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();

        $menu->addChild(
            self::MAIN_MENU_EXTRA_LINK,
            [
                'uri' => '/',
                'label' => 'shop.translation.key',
                'extras' => [
                    'translation_domain' => 'menu',
                ],

        ]
        );
    }

    /**
     * Returns an array of messages.
     *
     * @return array<Message>
     */
    public static function getTranslationMessages()
    {
        return [
            (new Message(self::MAIN_MENU_EXTRA_LINK, 'menu'))->setDesc('Shop'),
        ];
    }
}
