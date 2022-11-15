# Wikibase Export

[![GitHub Workflow Status](https://img.shields.io/github/workflow/status/ProfessionalWiki/WikibaseExport/CI)](https://github.com/ProfessionalWiki/WikibaseExport/actions?query=workflow%3ACI)
[![Type Coverage](https://shepherd.dev/github/ProfessionalWiki/WikibaseExport/coverage.svg)](https://shepherd.dev/github/ProfessionalWiki/WikibaseExport)
[![Psalm level](https://shepherd.dev/github/ProfessionalWiki/WikibaseExport/level.svg)](psalm.xml)
[![Latest Stable Version](https://poser.pugx.org/professional-wiki/wikibase-export/version.png)](https://packagist.org/packages/professional-wiki/wikibase-export)
[![Download count](https://poser.pugx.org/professional-wiki/wikibase-export/d/total.png)](https://packagist.org/packages/professional-wiki/wikibase-export)
[![License](https://img.shields.io/packagist/l/professional-wiki/wikibase-export)](LICENSE)

[Wikibase] extension for exporting data as simple CSV.

[Professional.Wiki] created and maintains Wikibase Export. We provide [Wikibase hosting], [Wikibase development] and [Wikibase consulting].

**Table of Contents**

- [Usage](#usage)
  * [REST API](#rest-api)
- [Installation](#installation)
- [PHP Configuration](#php-configuration)
- [Development](#development)
- [Release notes](#release-notes)

## Usage

TODO

### REST API

TODO

## Installation

Platform requirements:

* [PHP] 8.0 or later (tested up to 8.1)
* [MediaWiki] 1.37 or later (tested up to 1.38)
* [Wikibase] 1.37 or later (tested up to 1.38)

The recommended way to install Wikibase Export is using [Composer] with
[MediaWiki's built-in support for Composer][Composer install].

On the commandline, go to your wikis root directory. Then run these two commands:

```shell script
COMPOSER=composer.local.json composer require --no-update professional-wiki/wikibase-export:~1.0
```
```shell script
composer update professional-wiki/wikibase-export --no-dev -o
```

Then enable the extension by adding the following to the bottom of your wikis [LocalSettings.php] file:

```php
wfLoadExtension( 'WikibaseExport' );
```

You can verify the extension was enabled successfully by opening your wikis Special:Version page in your browser.

## PHP Configuration

Configuration can be changed via [LocalSettings.php].

### Export properties

You can choose which properties to include values for in your exports. This configuration specifies which
properties can be chosen from, and which properties are included when selecting "all properties".

Variable: `$wgWikibaseExportProperties`

Default: `[]`

Example:

```php
$wgWikibaseExportProperties = [
	'P2',
	'P3',
	'P5'
];
```

## Development

To ensure the dev dependencies get installed, have this in your `composer.local.json`:

```json
{
	"require": {
		"vimeo/psalm": "^4",
		"phpstan/phpstan": "^1.8.11"
	},
	"extra": {
		"merge-plugin": {
			"include": [
				"extensions/WikibaseExport/composer.json"
			]
		}
	}
}
```

### Running tests and CI checks

You can use the `Makefile` by running make commands in the `WikibaseExport` directory.

* `make ci`: Run everything
* `make test`: Run all tests
* `make cs`: Run all style checks and static analysis

Alternatively, you can execute commands from the MediaWiki root directory:

* PHPUnit: `php tests/phpunit/phpunit.php -c extensions/WikibaseExport/`
* Style checks: `vendor/bin/phpcs -p -s --standard=extensions/WikibaseExport/phpcs.xml`
* PHPStan: `vendor/bin/phpstan analyse --configuration=extensions/WikibaseExport/phpstan.neon --memory-limit=2G`
* Psalm: `php vendor/bin/psalm --config=extensions/WikibaseExport/psalm.xml`

## Release notes

### Version 1.0.0 - TBD

TODO

* Special page with export UI
  * TODO
* Configuration that can be set via PHP and a configuration UI on `MediaWiki:WikibaseExport`
  * TODO
* API endpoint for export
* TranslateWiki integration
* Support for PHP 8.0 and 8.1

[Professional.Wiki]: https://professional.wiki
[Wikibase]: https://wikibase.consulting/what-is-wikibase/
[Wikibase hosting]: https://professional.wiki/en/hosting/wikibase
[Wikibase development]: https://www.wikibase.consulting/about-the-wikibase-team/
[Wikibase consulting]: https://wikibase.consulting/
[MediaWiki]: https://www.mediawiki.org
[PHP]: https://www.php.net
[Composer]: https://getcomposer.org
[Composer install]: https://professional.wiki/en/articles/installing-mediawiki-extensions-with-composer
[LocalSettings.php]: https://www.pro.wiki/help/mediawiki-localsettings-php-guide
[Wikibase Stakeholder Group]:https://wbstakeholder.group/
