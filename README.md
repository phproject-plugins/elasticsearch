# Elasticsearch

*Easily add Elasticsearch support to your Phproject site!*

## Requirements

- PHP 5.6.6 or higher, PHP 7.x recommended
- Elasticsearch 5.x
- Phproject 1.5.0 or higher, at least 1.6.0 recommended
- Composer

## Setup

1. Install Elasticsearch on your Phproject server
2. Clone the plugin to the `app/plugin/` directory on your Phproject server
3. Install plugin dependencies with `composer install` from the plugin directory
4. Import your data into Elasticsearch via the Administration page

From there, your site will use Elasticsearch for all searches, and will keep the data in sync with changes made on your Phproject site.
