<?php
/**
 * @package  Elasticsearch
 * @author   Alan Hardman <alan@phpizza.com>
 * @version  0.1.0
 */

namespace Plugin\Elasticsearch;

use Elasticsearch\ClientBuilder;

class Base extends \Plugin
{
    protected $client = null;

    /**
     * Initialize the plugin
     */
    public function _load()
    {
        // Load composer libraries
        require_once 'vendor/autoload.php';

        // Override search route
        $f3 = \Base::instance();
        $f3->route("GET /search", "Plugin\Elasticsearch\Controller->search");
    }

    /**
     * Get an Elasticsearch client instance
     * @return \Elasticsearch\Client
     */
    public function client()
    {
        if ($this->client === null) {
            $this->client = ClientBuilder::create()->build();
        }
        return $this->client;
    }
}
