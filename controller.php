<?php

namespace Plugin\Elasticsearch;

class Controller extends \Controller
{
    protected $client = null;

    /**
     * GET /search
     */
    public function search(\Base $f3)
    {
        $client = Base::instance()->client();
        $result = $client->search([
            'index' => 'phproject_issues',
            'type' => 'issue',
            'body' => [
                'query' => [
                    'match' => [
                        '_all' => $f3->get('GET.q')
                    ]
                ]
            ]
        ]);

        $this->_printJson($result);
        // TODO: display results in a view
    }
}
