<?php

namespace Plugin\Elasticsearch;

class Controller extends \Controller
{
    protected $client;

    /**
     * GET /search
     */
    public function search(\Base $f3): void
    {
        $args = $f3->get("GET");
        if (empty($args["page"])) {
            $args["page"] = 0;
        }

        $client = Base::instance()->client();
        try {
            $result = $client->search([
                'index' => Base::INDEX_NAME,
                'type' => 'issue',
                'size' => 20,
                'from' => $args["page"],
                'body' => [
                    'query' => [
                        'match' => [
                            '_all' => $f3->get('GET.q')
                        ]
                    ]
                ],
            ]);
            $f3->set('result', $result);
        } catch (Exception) {
            $f3->set('error', 'Unable to load results from Elasticsearch.');
        }

        if (!empty($result['hits']['total'])) {
            $ids = [];
            foreach ($result['hits']['hits'] as $hit) {
                $ids[] = $hit['_id'];
            }

            $db = $f3->get('db.instance');
            $issues = $db->exec('SELECT * FROM issue_detail WHERE id IN (' . implode(',', $ids) . ') ORDER BY FIELD(id,' . implode(',', $ids) . ')');
            $f3->set('issues', $issues);
            $this->_render('elasticsearch/view/search.html');
        } else {
            $this->_render('elasticsearch/view/search-empty.html');
        }
    }

    /**
     * POST /search/reindex
     */
    public function reindex(\Base $f3): void
    {
        $this->_requireAdmin();
        $base = Base::instance();

        try {
            $base->truncate();
            $base->indexAll();
        } catch (Exception $e) {
            $this->_printJson(['error' => $e->getMessage()]);
        }

        $this->_printJson(['success' => true]);
    }
}
