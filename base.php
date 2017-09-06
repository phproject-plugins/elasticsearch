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
        $f3 = \Base::instance();

        if (!is_file(__DIR__ . '/vendor/autoload.php')) {
            $f3->set('error', 'Run `composer install` from app/plugin/elasticsearch/ to complete the Elasticsearch installation.');
            return;
        }

        // Load composer libraries
        require_once __DIR__ . '/vendor/autoload.php';

        // Override search route
        $f3->route("GET /search", "Plugin\Elasticsearch\Controller->search");
    }

    /**
     * Generate page for admin panel
     */
    public function _admin()
    {
        $f3->set('elastic_client', $this->client());
        echo \Helper\View::instance()->render("elasticsearch/view/admin.html");
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
