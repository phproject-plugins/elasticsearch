<?php
/**
 * Phproject Elasticsearch CLI
 */

require_once dirname(dirname(dirname(__DIR__))) . '/cron/base.php';

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
    	$client = \Plugin\Elasticsearch\Base::instance()->client();

        $detail = new \Model\Issue\Detail;
        $issues = $detail->find(['deleted_date IS NULL']);
        foreach ($issues as $issue) {
            $this->indexIssue($issue);
        }
        return count($issues);

    	// TODO: import dat crap
	    break;
}
