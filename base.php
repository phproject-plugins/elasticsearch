<?php

/**
 * @package  Elasticsearch
 * @author   Alan Hardman <alan@phpizza.com>
 * @version  0.1.0
 */

namespace Plugin\Elasticsearch;

use Elasticsearch\ClientBuilder;
use Elasticsearch\Common\Exceptions\ElasticsearchException;
use Elasticsearch\Common\Exceptions\Missing404Exception;

class Base extends \Plugin
{
    protected $client;

    public const INDEX_NAME = 'phproject_issues';

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

        // Hook into issue events
        $this->_hook('model/issue.after_save', $this->issueSaveHook(...));

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
        $detail = new \Model\Issue\Detail();
        $issues = $detail->find(['deleted_date IS NULL']);
        foreach ($issues as $issue) {
            $this->indexIssue($issue);
        }

        return count($issues);
    }

    /**
     * Index an issue
     */
    public function indexIssue(\Model\Issue\Detail $issue): void
    {
        $this->client()->index([
            'index' => self::INDEX_NAME,
            'type' => 'issue',
            'id' => $issue->id,
            'body' => [
                'name' => $issue->name,
                'description' => $issue->description,
                'author_name' => $issue->author_name,
                'owner_name' => $issue->owner_name,
            ]
        ]);
    }

    /**
     * Delete an issue
     */
    public function deleteIssue(\Model\Issue $issue): void
    {
        $this->client()->delete([
            'index' => self::INDEX_NAME,
            'type' => 'issue',
            'id' => $issue->id
        ]);
    }

    /**
     * Handle issue saving
     * @param  $issue \Model\Issue
     */
    public function issueSaveHook(\Model\Issue $issue): void
    {
        if ($issue->deleted_date) {
            try {
                $this->deleteIssue($issue);
            } catch (Missing404Exception) {
                // Silently ignore 404s
            } catch (ElasticsearchException) {
                \Base::instance()->set('error', 'Failed to delete issue from Elasticsearch index.');
            }
        } else {
            $detail = new \Model\Issue\Detail();
            $detail->load(['id = ?', $issue->id]);
            if ($detail->id) {
                $this->indexIssue($detail);
            }
        }
    }
}
