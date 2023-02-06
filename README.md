# Wikibase Export

[![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/ProfessionalWiki/WikibaseExport/ci.yml?branch=master)](https://github.com/ProfessionalWiki/WikibaseExport/actions?query=workflow%3ACI)
[![Type Coverage](https://shepherd.dev/github/ProfessionalWiki/WikibaseExport/coverage.svg)](https://shepherd.dev/github/ProfessionalWiki/WikibaseExport)
[![Psalm level](https://shepherd.dev/github/ProfessionalWiki/WikibaseExport/level.svg)](psalm.xml)
[![Latest Stable Version](https://poser.pugx.org/professional-wiki/wikibase-export/version.png)](https://packagist.org/packages/professional-wiki/wikibase-export)
[![Download count](https://poser.pugx.org/professional-wiki/wikibase-export/d/total.png)](https://packagist.org/packages/professional-wiki/wikibase-export)

[Wikibase] extension for exporting data as simple CSV.

[Professional.Wiki] created and maintains Wikibase Export. We provide [Wikibase hosting], [Wikibase development] and [Wikibase consulting].

**Table of Contents**

- [Demo](#demo)
- [Usage](#usage)
- [REST API](#rest-api)
- [Installation](#installation)
- [PHP Configuration](#php-configuration)
- [Export page text configuration](#export-page-text-configuration)
- [Development](#development)
- [Release notes](#release-notes)

## Demo

Quickly get an idea about what this extension does by checking out the [demo wiki] or demo video TODO.

## Usage documentation

See the [usage documentation](https://professional.wiki/en/extension/wikibase-export).

## REST API

This extension provides a REST API endpoint for exporting Wikibase items.

For more information, refer to the [REST API documentation].

## Installation

Platform requirements:

* [PHP] 8.0 or later (tested up to 8.1)
* [MediaWiki] 1.37 or later (tested up to 1.39)
* [Wikibase] 1.37 or later (tested up to 1.39)

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

You can verify the extension was enabled successfully by opening your wikis Special:Version page.

## PHP Configuration

Configuration can be changed via [LocalSettings.php].

### Export configuration

In JSON format, following the JSON Schema at [schema.json].
Gets combined with rules defined on page `MediaWiki:WikibaseExportConfig`.

Variable: `$wgWikibaseExport`

Default: `""`

Example: [example.json]

Caution: invalid JSON will be ignored. No error will be shown, the intended config will just not be applied.

### Enable in-wiki configuration

If it should be possible to configure this extension via `MediaWiki:WikibaseExportConfig`.

Variable: `$wgWikibaseExportEnableInWikiConfig`

Default: `true`

Example: `false`

The page `MediaWiki:WikibaseExportConfig` will always be available. If this configuration is set to `false`, its contents will be ignored.

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

### Version 1.0.0 - 2023-02-06

* Special page with export UI
    * Language selector
    * Subject (entity) selector
    * Grouping and filtering by year based on point in time or time range qualifiers
    * Property selection for both grouped and ungrouped values
    * Header style choice: entity label or entity ID
* Support for multiple values
* Configuration that can be set via PHP and a configuration UI on `MediaWiki:WikibaseExportConfig`
* API endpoint for export
* TranslateWiki integration
* Support for PHP from 8.0 up to 8.2
* Support for MediaWiki from 1.37 up to 1.39

[Professional.Wiki]: https://professional.wiki
[Wikibase]: https://wikibase.consulting/what-is-wikibase/
[Wikibase hosting]: https://professional.wiki/en/hosting/wikibase
[Wikibase development]: https://professional.wiki/en/wikibase-software-development
[Wikibase consulting]: https://wikibase.consulting/
[MediaWiki]: https://www.mediawiki.org
[PHP]: https://www.php.net
[Composer]: https://getcomposer.org
[Composer install]: https://professional.wiki/en/articles/installing-mediawiki-extensions-with-composer
[LocalSettings.php]: https://www.pro.wiki/help/mediawiki-localsettings-php-guide
[Wikibase Stakeholder Group]:https://wbstakeholder.group/
[schema.json]: https://github.com/ProfessionalWiki/WikibaseExport/blob/master/schema.json
[example.json]: https://github.com/ProfessionalWiki/WikibaseExport/blob/master/example.json
[Rest API Documentation]: docs/rest.md
[demo wiki]: https://export.wikibase.wiki/
