<?php
/**
 * Phproject Elasticsearch CLI
 */

require_once dirname(dirname(dirname(__DIR__))) . '/cron/base.php';
require_once __DIR__ . '/vendor/autoload.php';

$plugin = \Plugin\Elasticsearch\Base::instance();

switch(end($argv)) {
    case null:
    case 'help':
    case '--help':
    case '-h':
    default:
        echo <<<EOT
Phproject Elasticsearch CLI
This tool safely runs bulk operations on Elasticsearch integration

Usage: php app/plugin/elasticsearch/cli.php <action>

Supported actions:
  import: Erase and re-import all records from Phproject

EOT;
        break;

    case 'import':
        $client = $plugin->client();

        echo "Connected to elasticsearch\n";

        echo "Deleting current index...";
        $plugin->truncate();
        echo " done.\n";

        $size = 100;
        $filter = ['deleted_date IS NULL'];

        $detail = new \Model\Issue\Detail;
        $issues = $detail->paginate(0, $size, $filter);

        echo "Starting index of {$issues['total']} issues, $size at a time\n";

        $plugin->indexIssues($issues['subset']);

        while ($issues['pos'] < $issues['count'] - 1) {
            echo (($issues['pos'] + 1) * $issues['limit']), " issues indexed\n";
            $issues = $detail->paginate($issues['pos'] + 1, $size, $filter);
            $plugin->indexIssues($issues['subset']);
        }

        echo "{$issues['total']} issues indexed, indexing complete.\n";

        break;
}
