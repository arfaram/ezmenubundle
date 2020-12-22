# FAQ


#### Can I use the Menu bundle in classical twig frontend engine
YES, this is the standard behavior of this bundle

#### Can I use more advanced search criterion and sort clauses
YES, you have to add a Post Event Listener or Subscriber. For more information please check the [extend](extend.md) documentation

#### Can I transfer options to the search query
YES, you can add them either in the template or in the builder. This is the typical use case to fetch the subtree and searching for content with specific field, priority or date criteria

Template:
```
'show_in_hamburger_menu': false
```

Builder:
```
$options['show_in_footer'] =  true;
```

#### Can I transfer options to the template
No, this is not supported yet. Any additional option will be available in the search query (see above question) and it is not transferred to the template. (WIP)



```
'show_in_footer': true
```

#### Can I create menu on the fly 
YES, Check - [Custom usage - PHP integration](extend.md#custom-usage---php-integration)
 
#### Can I use the Menu bundle in Headless CMS
Not out-of-the-box. You have to use the PHP integration and convert the Knp MenuItem object to xml/json. The Menu object contains always the `activeLink`  information to highlight the current navigation item.

Children item example:
```
#displayChildren: true
      #children: array:1 [▼
        11818 => Knp\Menu\MenuItem {#3937 ▼
          #name: "11818"
          #label: "cologne"
          #linkAttributes: []
          #childrenAttributes: []
          #labelAttributes: []
          #uri: "/news/cologne"
          #attributes: array:1 [▼
            "class" => "nav-location-11818"
          ]
          #extras: array:4 [▼
            "translation_domain" => "menu"
            "itemlocationId" => 11818
            "thisLocationId" => 60
            "activeLink" => false
          ]
```

if (`itemlocationId` == `thisLocationId`)  then `activeLink` = true

#### Is there any REST API or GraphQL Integration.
Not for the time being. Any contribution is welcome. contact me first I have some Ideas :) 

#### I got "Unsupported value (NULL)" as error
The error means that you haven't configured the contenttype list parameter.

#### The template is not responsive and has different color 
The default template delivered with this bundle is just an example and will only work with bootstrap4 and Jquery. In general, you will override it most of the time. Take it as example :)