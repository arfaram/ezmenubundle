<?php

declare(strict_types=1);

namespace EzPlatform\Menu;

use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\Helper\TranslationHelper;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\SearchService;
use EzPlatform\MenuBundle\Events\PostQueryEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\RouterInterface;
use Knp\Menu\ItemInterface;

class MenuItems
{
    /** @var int  */
    protected static $depth = 0;

    /** @var int  */
    protected static $limit = 0;

    /** @var  */
    protected static $currentLocationId;

    /** @var bool  */
    protected static $currentLocationPathString = false;

    /** @var string  */
    protected static $level;

    /** @var ConfigResolverInterface  */
    private $configResolver;

    /** @var PermissionResolver  */
    private $permissionResolver;

    /** @var LocationService  */
    private $locationService;

    /** @var SearchService  */
    private $searchService;

    /** @var TranslationHelper  */
    private $translationHelper;

    /** @var RouterInterface  */
    private $router;

    /** @var MenuItemFactory  */
    protected $factory;

    /** @var EventDispatcherInterface  */
    private $eventDispatcher;

    /**
     * MenuItems constructor.
     * @param \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     * @param \eZ\Publish\API\Repository\SearchService $searchService
     * @param \eZ\Publish\Core\Helper\TranslationHelper $translationHelper
     * @param \Symfony\Component\Routing\RouterInterface $router
     * @param \EzPlatform\Menu\MenuItemFactory $factory
     * @param \Symfony\Component\EventDispatcher\EventDispatcher $eventDispatcher
     */
    public function __construct(
        ConfigResolverInterface $configResolver,
        PermissionResolver $permissionResolver,
        LocationService $locationService,
        SearchService $searchService,
        TranslationHelper $translationHelper,
        RouterInterface $router,
        MenuItemFactory $factory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->configResolver = $configResolver;
        $this->permissionResolver = $permissionResolver;
        $this->locationService = $locationService;
        $this->searchService = $searchService;
        $this->translationHelper = $translationHelper;
        $this->router = $router;
        $this->factory = $factory;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param $level
     * @return string
     */
    public function setLevel($level): string
    {
        return self::$level = $level;
    }

    /**
     * @return string
     */
    public function getLevel(): string
    {
        return self::$level;
    }

    /**
     * @param \Knp\Menu\ItemInterface $menu
     * @param $locationId
     * @param $level
     * @param array $options
     * @return array
     */
    public function createMenu(ItemInterface $menu, $locationId, array $options): ItemInterface
    {
        self::$limit = $options['depth'] ?? self::$depth;

        $items = $this->addLocationsToMenu(
            $menu,
            $this->getMenuItems(
                $locationId,
                $options
            ),
            $options
        );

        return $items;
    }

    /**
     * @param $rootLocationId
     * @param $options
     * @return array
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function getMenuItems($rootLocationId, $options): array
    {
        $rootLocation = $this->locationService->loadLocation($rootLocationId);

        $query = new LocationQuery();

        $query->filter = new Criterion\LogicalAnd([
            new Criterion\ContentTypeIdentifier($this->getLevelContentTypeIdentifierList()),
            new Criterion\Location\Depth(Criterion\Operator::EQ, $rootLocation->depth + 1),
            new Criterion\Subtree($rootLocation->pathString),
            new Criterion\LanguageCode($this->contentLanguage()),
        ]);

        $query->performCount = false;

        $this->eventDispatcher->dispatch($this->createPostQueryEvent($query, $options), $options['level']);

        return $this->searchService->findLocations($query)->searchHits;
    }

    protected function createPostQueryEvent(LocationQuery $query, array $options)
    {
        return new PostQueryEvent($query, $options);
    }

    /**
     * @param \Knp\Menu\ItemInterface $menu
     * @param array $searchHits
     * @param array $options
     * @return \Knp\Menu\ItemInterface|null
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    private function addLocationsToMenu(ItemInterface $menu, array $searchHits, array $options)
    {
        if(isset($options['location'])){
            $currentLocation = $options['location'];
            self::$currentLocationId = $currentLocation->id;
            self::$currentLocationPathString = $currentLocation->pathString;
        }

        //BC
        if(isset($options['thisLocationId'])){
            $currentLocation = $this->locationService->loadLocation($options['thisLocationId']);
            self::$currentLocationId = $options['thisLocationId'];
            self::$currentLocationPathString = $currentLocation->pathString;
        }
        //BC
        if(isset($options['displayChildrenWhenItemClicked'])){
            $options['displayChildrenOnClick'] = $options['displayChildrenWhenItemClicked'];
        }

        foreach ($searchHits as $searchHit) {

            /** @var \eZ\Publish\API\Repository\Values\Content\Location $location */
            $location = $searchHit->valueObject;

            $menu->addChild($this->factory->createItem(
                (string) $location->id,
                [
                    'label' => $this->translationHelper->getTranslatedContentNameByContentInfo($location->contentInfo),
                    'uri' => $this->router->generate('ez_urlalias', ['locationId' => $location->id]),
                    'attributes' => [
                        'class' => 'nav-location-' . $location->id
                    ],
                    'extras' => [
                        'itemlocationId' =>$location->id,
                        'thisLocationId' => self::$currentLocationId,
                        'activeLink' => self::$currentLocationPathString ? \in_array($location->id, $this->getPathString(self::$currentLocationPathString)) : null

                    ],
                    'displayChildren'=> isset($options['displayChildrenOnClick']) && $options['displayChildrenOnClick'] ? \in_array($location->id, $this->getPathString(self::$currentLocationPathString)) : true,

                ]
            ));

            $searchItems = $this->getMenuItems($location->id, $options);

            if (count($searchItems) > 0 ) {
                self::$depth++;
                if (self::$depth < self::$limit ) {
                    $this->addLocationsToMenu(
                        $menu->getChild((string) $location->id),
                        $searchItems,
                        $options
                    );
                }
            }

        }

        return $menu;
    }

    /**
     * @return mixed
     */
    private function getLevelContentTypeIdentifierList()
    {
        return $this->configResolver->getParameter('contenttypes_identifier.menu', $this->getLevel());
    }

    /**
     * @return mixed
     */
    private function contentLanguage()
    {
        return $this->configResolver->getParameter('languages');
    }

    /**
     * @param $pathString
     * @return array
     */
    public function getPathString($pathString): array
    {
        return explode('/', substr($pathString, 1, -1)) ;
    }
}
