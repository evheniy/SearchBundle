<?php

namespace Evheniy\SearchBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;

/**
 * Class SearchExtension
 *
 * @package Evheniy\SearchBundle\DependencyInjection
 */
class SearchExtension extends Extension
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $this->filter($configs));
        $container->setParameter('search', $config);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }

    private function filter(array $configs)
    {
        if (!is_array($configs[0]['search']['fields'])) {
            if (!in_array($configs[0]['search']['fields'], array('all'))) {
                throw new Exception("['search']['fields'] should be all or array");
            }
            if ($configs[0]['search']['fields'] == 'all') {
                $configs[0]['search']['fields'] = $configs[0]['index']['fields'];
            }
        } else {
            if (!empty(array_diff($configs[0]['search']['fields'], $configs[0]['index']['fields']))) {
                throw new Exception("['search']['fields'] should be from ['index']['fields']");
            }
        }
        if (!is_array($configs[0]['search']['highlight']['fields'])) {
            if (!in_array($configs[0]['search']['highlight']['fields'], array('all', 'none'))) {
                throw new Exception("['search']['highlight']['fields'] should be all, none or array");
            }
            if ($configs[0]['search']['highlight']['fields'] == 'all') {
                $configs[0]['search']['highlight']['fields'] = array();
                foreach ($configs[0]['search']['fields'] as $key) {
                    $configs[0]['search']['highlight']['fields'][$key] = array(
                        'fragment_size'       => $configs[0]['search']['highlight']['fragment_size'],
                        'number_of_fragments' => $configs[0]['search']['highlight']['number_of_fragments']
                    );
                }
            }
            if ($configs[0]['search']['highlight']['fields'] == 'none') {
                $configs[0]['search']['highlight']['fields'] = array();
            }
        } else {
            if (!empty(array_diff(array_keys($configs[0]['search']['highlight']['fields']), $configs[0]['search']['fields']))) {
                throw new Exception("['search']['highlight']['fields'] should be from ['search']['fields']");
            }
        }
        if (!is_array($configs[0]['search']['parameters']['priorities'])) {
            throw new Exception("['search']['parameters']['priorities'] should be array");
        } else {
            if (!empty(array_diff(array_keys($configs[0]['search']['parameters']['priorities']), $configs[0]['search']['fields']))) {
                throw new Exception("['search']['parameters']['priorities'] should be from ['search']['fields']");
            }
        }
        if (!is_array($configs[0]['search']['filter']['fields'])) {
            throw new Exception("['search']['filter']['fields'] should be array");
        } else {
            if (!empty(array_diff(array_keys($configs[0]['search']['filter']['fields']), $configs[0]['search']['fields']))) {
                throw new Exception("['search']['filter']['fields'] should be from ['search']['fields']");
            }
        }

        return $configs;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return 'search';
    }
}
