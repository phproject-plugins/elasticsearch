# Elasticsearch

*Easily add Elasticsearch support to your Phproject site!*

**This plugin is under initial development and is not usable in a production environment yet!**

It is currently being built as a properly integrated implementation based on [a quick test script](https://gist.github.com/Alanaktion/0730bd63bdeeeeb77693d354e1e47ce4).

## Setup

1. Install Elasticsearch on your Phproject server
2. Clone the plugin to the `app/plugin/` directory on your Phproject server
3. Install plugin dependencies with `composer install` from the plugin directory
4. Import your data into Elasticsearch via the Administration page

From there, your site will use Elasticsearch for all searches, and will keep the data in sync with changes made on your Phproject site.
