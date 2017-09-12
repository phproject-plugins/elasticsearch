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

    const INDEX_NAME = 'phproject_issues';

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

        // Add/override routes
        $f3->route('GET /search', 'Plugin\Elasticsearch\Controller->search');
        $f3->route('POST /search/reindex', 'Plugin\Elasticsearch\Controller->reindex');
    }

    /**
     * Generate page for admin panel
     */
    public function _admin()
    {
        $f3 = \Base::instance();
        $f3->set('info', $this->client()->info());
        echo \Helper\View::instance()->render('elasticsearch/view/admin.html');
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

    /**
     * Delete all indexed records
     *
     * @return array
     */
    public function truncate()
    {
        $indices = $this->client()->indices();
        $params = [
            'index' => self::INDEX_NAME
        ];
        if ($indices->exists($params)) {
            return $this->client()->indices()->delete($params);
        }
        return null;
    }

    /**
     * Index all issues
     *
     * @return array
     */
    public function indexAll()
    {
        $detail = new \Model\Issue\Detail;
        $issues = $detail->find(['deleted_date IS NULL']);
        foreach ($issues as $issue) {
            $params = [
                'index' => self::INDEX_NAME,
                'type' => 'issue',
                'id' => $issue->id,
                'body' => [
                    'name' => $issue->name,
                    'description' => $issue->description,
                    'author_name' => $issue->author_name,
                    'owner_name' => $issue->owner_name,
                ]
            ];
            $this->client()->index($params);
        }
        return count($issues);
    }
}
