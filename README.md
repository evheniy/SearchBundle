SearchBundle
=================

This bundle provides Search mechanism (elasticsearch) in Symfony2

Documentation
-------------

You need to set config:

    search:
        index: [fields]
        search: 
            fields: [fields]
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
            index: [fields]
            search: 
                fields: [fields]
                analyze: true

License
-------

This bundle is under the MIT license. See the complete license in the bundle:

    Resources/meta/LICENSE

[MakeDev.org][1]

[1]:  http://makedev.org/