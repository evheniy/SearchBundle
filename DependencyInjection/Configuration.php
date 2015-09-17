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
     */
    public function validate()
    {
        if (empty($this->config['search_url'])) {
            throw new Exception('Empty "search_url" parameter!');
        }
        if (empty($this->config['index_name'])) {
            throw new Exception('Empty "index_name" parameter!');
        }
        if (empty($this->config['index_type'])) {
            throw new Exception('Empty "index_type" parameter!');
        }
        if (empty($this->config['color_tag_open'])) {
            throw new Exception('Empty "color_tag_open" parameter!');
        }
        if (empty($this->config['color_tag_close'])) {
            throw new Exception('Empty "color_tag_close" parameter!');
        }
        if (empty($this->config['query_parameter'])) {
            throw new Exception('Empty "query_parameter" parameter!');
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

        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function filter()
    {
        if (!empty($this->config['search']['filter']) && !is_array($this->config['search']['filter']['fields'])) {
            throw new Exception("['search']['filter']['fields'] should be array");
        } else {
            if (!empty(array_diff(array_keys($this->config['search']['filter']['fields']), $this->config['index']['fields']))) {
                throw new Exception("['search']['filter']['fields'] should be from ['search']['fields']");
            }
        }
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