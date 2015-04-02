SearchBundle
=================

This bundle provides Search mechanism (elasticsearch) in Symfony2

Documentation
-------------

You need to set config:

    search:
        index_name: ~
        index_type: ~
        color_tag_open: <b class="yellow">
        color_tag_close: </b>
        index: [fields]
        search: 
            fields: [fields]
            parameters: 
                fuzziness: 0.6
                operator: or
                type: best_fields
                tie_breaker: 0.3
                minimum_should_match: 30%
            filter:
                fields: [fields]
                count: 10
                analyze: true
                
            

Installation
------------

    $ composer require evheniy/search-bundle "1.*"

    Or add to composer.json

    "evheniy/search-bundle": "1.*"


    AppKernel:
        public function registerBundles()
            {
                $bundles = array(
                    ...
                    new Evheniy\SearchBundle\SearchBundle(),
                );
                ...


    config.yml:
        #SearchBundle
        search:
            index_name: ~
            index_type: ~
            color_tag_open: <b class="yellow">
            color_tag_close: </b>
            index: [fields]
            search: 
                fields: [fields]
                parameters: 
                    fuzziness: 0.6
                    operator: or
                    type: best_fields
                    tie_breaker: 0.3
                    minimum_should_match: 30%
                filter:
                    fields: [fields]
                    count: 10
                    analyze: true

License
-------

This bundle is under the MIT license. See the complete license in the bundle:

    Resources/meta/LICENSE

[MakeDev.org][1]

[1]:  http://makedev.org/