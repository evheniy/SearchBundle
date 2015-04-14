SearchBundle
=================
[![Latest Stable Version](https://poser.pugx.org/evheniy/search-bundle/v/stable.svg)](https://packagist.org/packages/evheniy/search-bundle) [![Total Downloads](https://poser.pugx.org/evheniy/search-bundle/downloads.svg)](https://packagist.org/packages/evheniy/search-bundle) [![Latest Unstable Version](https://poser.pugx.org/evheniy/search-bundle/v/unstable.svg)](https://packagist.org/packages/evheniy/search-bundle) [![License](https://poser.pugx.org/evheniy/search-bundle/license.svg)](https://packagist.org/packages/evheniy/search-bundle)

This bundle provides Search mechanism (elasticsearch) in Symfony2

Documentation
-------------

You need to set config:

    search:
        index_name: ~
        index_type: ~
        color_tag_open: <b class="yellow">
        color_tag_close: </b>
        search: 
            fields: [fields]
            parameters: 
                fuzziness: 0.6
                operator: or
                type: best_fields
                tie_breaker: 0.3
                minimum_should_match: 30%
                priorities:
                     field: 10
            filter:
                fields: [fields]
                count: 10
                analyze: true
                
            

Installation
------------

Elasticsearch:

    wget https://download.elasticsearch.org/elasticsearch/elasticsearch/elasticsearch-1.4.4.deb
    sudo dpkg -i elasticsearch-1.4.4.deb
    rm elasticsearch-1.4.4.deb
    
    sudo service elasticsearch start
    
    
    cd /usr/share/elasticsearch
    sudo bin/plugin -install mobz/elasticsearch-head
    
    http://localhost:9200/_plugin/head/
    
SearchBundle:

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
            search: 
                fields: [fields]
                parameters: 
                    fuzziness: 0.6
                    operator: or
                    type: best_fields
                    tie_breaker: 0.3
                    minimum_should_match: 30%
                    priorities:
                        field: 10
                filter:
                    fields: [fields]
                    count: 10
                    analyze: true
                    
Indexing

        $indexer = $this->getContainer()->get('search_index');
        $indexer->deleteIndex();
        $indexer->createIndex();
        foreach ($pages as $page) {
            $indexer->indexDocument(
                DocumentEntity::createFromArray(
                    $indexer->getIndexFieldNames(),
                    array(
                        'url'     => $page->getUrl(),
                        'title'   => $page->getPageTitle(),
                        'article' => $page->getPageArticle()
                    )
                )
            );
        }

indexAction()

        $request    = $this->container->get('request');
        $searcher   = $this->container->get('search');
        $searchText = strip_tags(trim($request->get('q')));
        $page       = $request->get('page', 1) < 1 ? 1: $request->get('page', 1);
        $size       = $request->get('size', 10) < 1 ? 10: $request->get('size', 10);
        
        $filterTitle       = $request->get('filterTitle', array());
        $filterDescription = $request->get('filterDescription', array());

        $results = $searcher->search(
            $searchText,
            $size,
            $page,
            array(
                'filterTitle'       => $filterTitle,
                'filterDescription' => $filterDescription
            )
        );
        
        return $this->render(
            'AppBundle:Default:index.html.twig',
            array(
                'searchText'  => $searchText,
                'filters'     => $searcher->getFilters(),
                'results' => $results,
                'pagination'  => $searcher->getPaginator()
            )
        );

index.html.twig

    <div class="searchResults">
            <div class="searchResultsControls">
                {% include "AppBundle:Default:searchFilters.html.twig" %}
            </div>
            <div>
                {% for result in results %}
                    <h1><a href="{{ result.url }}">{{ result.title }}</a></h1>
                    <article>{{ result.article }}</article>
                {% endfor %}
                <div class="navigation">
                    {% include "SearchBundle::pagination.html.twig" %}
                </div>
            </div>
        </div>

searchFilters.html.twig
    
    {% for key,filter in filters %}
        <div>
            <div>
                <h3 class="label">
                    {{- ('index.filters.'~key)|trans({}, 'AppBundle') -}}
                </h3>
                <div class="options">
                    <ul>
                        {% for field in filter %}
                            <li{% if field.isActive %} class="selected"{% endif %}>
                                <a class="item" href="{{ path('homepage', field.url) }}">
                                    <span class="name">{{ field.name|upper }}</span>
                                    <span class="restaurantCount">{{ field.count }}</span>
                                </a>
                            </li>
                        {% endfor %}
                    </ul>
                </div>
            </div>
        </div>
    {% endfor %}


License
-------

This bundle is under the MIT license. See the complete license in the bundle:

    Resources/meta/LICENSE

[MakeDev.org][1]

[1]:  http://makedev.org/
