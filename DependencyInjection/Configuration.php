<?php

namespace Evheniy\SearchBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Class Configuration
 * @package Evheniy\SearchBundle\DependencyInjection
 */
class Configuration
{
    /**
     * @var array
     */
    protected $config = array();

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function validate()
    {
        foreach (array('search_url', 'index_name', 'index_type', 'color_tag_open', 'color_tag_close', 'query_parameter') as $parameter) {
            if (empty($this->config[$parameter])) {
                throw new Exception('Empty "' . $parameter . '" parameter!');
            }
        }
        if (empty($this->config['search']['query'])) {
            throw new Exception('Empty [\'search\'][\'query\'] parameter!');
        }
        if (empty($this->config['search']['highlight']['fragment_size'])) {
            $configs['search']['highlight']['fragment_size'] = 1500;
        }
        if (empty($this->config['search']['highlight']['number_of_fragments'])) {
            $configs['search']['highlight']['number_of_fragments'] = 3;
        }
        if (!empty($this->config['search']['filter']) && !is_array($this->config['search']['filter']['fields'])) {
            throw new Exception("['search']['filter']['fields'] should be array");
        } else {
            if (!empty(array_diff(array_keys($this->config['search']['filter']['fields']), $this->config['index']['fields']))) {
                throw new Exception("['search']['filter']['fields'] should be from ['search']['fields']");
            }
        }

        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function filter()
    {
        if (!empty($this->config['search']['highlight']) && !is_array($this->config['search']['highlight']['fields'])) {
            if (!in_array($this->config['search']['highlight']['fields'], array('all', 'none'))) {
                throw new Exception("['search']['highlight']['fields'] should be all, none or array");
            }
            if ($this->config['search']['highlight']['fields'] == 'all') {
                $this->config['search']['highlight']['fields'] = array();
                foreach ($this->config['index']['fields'] as $key) {
                    $this->config['search']['highlight']['fields'][$key] = array(
                        'fragment_size'       => $this->config['search']['highlight']['fragment_size'],
                        'number_of_fragments' => $this->config['search']['highlight']['number_of_fragments']
                    );
                }
            }
            if ($this->config['search']['highlight']['fields'] == 'none') {
                $this->config['search']['highlight']['fields'] = array();
            }
        } else {
            if (!empty(array_diff(array_keys($this->config['search']['highlight']['fields']), $this->config['index']['fields']))) {
                throw new Exception("['search']['highlight']['fields'] should be from ['index']['fields']");
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getConfig()
    {

        return $this->config;
    }
}