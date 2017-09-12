<?php

namespace Plugin\Elasticsearch;

class Controller extends \Controller
{
    protected $client = null;

    /**
     * GET /search
     *
     * @param \Base $f3
     * @return void
     */
    public function search(\Base $f3)
    {
        $client = Base::instance()->client();
        try {
            $result = $client->search([
                'index' => Base::INDEX_NAME,
                'type' => 'issue',
                'body' => [
                    'query' => [
                        'match' => [
                            '_all' => $f3->get('GET.q')
                        ]
                    ]
                ]
            ]);
            $f3->set('result', $result);
        } catch (Exception $e) {
            $f3->set('error', 'Unable to load results from Elasticsearch.');
        }

        $this->_render('elasticsearch/view/search.html');
    }

    /**
     * POST /search/reindex
     *
     * @param \Base $f3
     * @return void
     */
    public function reindex(\Base $f3)
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
