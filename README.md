# Wikibase Export

[![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/ProfessionalWiki/WikibaseExport/ci.yml?branch=master)](https://github.com/ProfessionalWiki/WikibaseExport/actions?query=workflow%3ACI)
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
- [Export page text configuration](#export-page-text-configuration)
- [Development](#development)
- [Release notes](#release-notes)

## Usage

TODO

This extension provides a special page `Special:WikibaseExport` that can be used to export statement values. These
values may be either ungrouped, or grouped by year and limited to a specific date range.

Statements that may be grouped by year need to have date qualifiers that can be defined in two ways:
* Using a single "Point in time" property; or
* As a range, using two "Point in time" properties for "start date" and "end date"

When exporting, groupable statement qualifiers are compared against the export date range. For single point qualifiers
the statement value will be included if the date is contained in the export date range. For qualifier ranges the
statements will be included for the period where the qualifier range overlaps with the export range.

### REST API

This extension provides a REST API endpoint for exporting Wikibase items.

For more information, refer to the [REST API documentation].

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

### Export configuration

In JSON format, following the JSON Schema at [schema.json].
Gets combined with rules defined on page `MediaWiki:WikibaseExportConfig`.

Variable: `$wgWikibaseExport`

Default: `""`

Example: [example.json]

Caution: invalid JSON will be ignored. No error will be shown, the intended config will just not be applied.

#### JSON Variables

| Variable                     | Description                                                                                        | Example          |
|------------------------------|----------------------------------------------------------------------------------------------------|------------------|
| `startTimePropertyId`        | Property ID of the qualifier used for the start of a time range.                                   | `P100`           |
| `endTimePropertyId`          | Property ID of the qualifier used for the end of a time range.                                     | `P200`           |
| `pointInTimePropertyId`      | Property ID of the qualifier used for a specific point in time.                                    | `P300`           |
| `propertiesToGroupByYear`    | List of property IDs. Values of statements with these properties are included and grouped by year. | `[ "P1", "P2" ]` |
| `ungroupedProperties`        | List of property IDs. Values of statements with these properties are included without grouping.    | `[ "P3", "P4" ]` |
| `defaultSubjects`            | List of IDs of items that should be selected by default.                                           | `[ "Q1", "Q2" ]` |
| `defaultStartYear`           | The default start year.                                                                            | `2010`           |
| `defaultEndYear`             | The default end year.                                                                              | `2022`           |
| `subjectFilterPropertyId`    | Property ID of the statement used to filter items that may be included in the export.              | `P50`            |
| `subjectFilterPropertyValue` | Expected value of the subject filter property.                                                     | `company`        |

When specifying `propertiesToGroupByYear` the following variables are required and must be defined in either
[LocalSettings.php] or using the in-wiki configuration page:
* `startTimePropertyId`
* `endTimePropertyId`
* `pointInTimePropertyId`

If neither `grouped_statement_property_ids` nor `ungrouped_statement_property_ids` is specified then the export will
contain only item IDs and labels.

### Enable in-wiki configuration

If it should be possible to configure this extension via `MediaWiki:WikibaseExportConfig`.

Variable: `$wgWikibaseExportEnableInWikiConfig`

Default: `true`

Example: `false`

The page `MediaWiki:WikibaseExportConfig` will always be available. If this configuration is set to `false`, its contents will be ignored.

## Export page text configuration

The following text snippets shown on `Special:WikibaseExport` can be configured by editing the on-wiki system message:

| Text snippet                            | System message                                   |
|-----------------------------------------|--------------------------------------------------|
| Introductory text shown before the form | `MediaWiki:Wikibase-export-intro`                |
| Choose subjects heading                 | `MediaWiki:Wikibase-export-subjects-heading`     |
| Search subjects placeholder             | `MediaWiki:Wikibase-export-subjects-placeholder` |


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
* Configuration that can be set via PHP and a configuration UI on `MediaWiki:WikibaseExportConfig`
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
[schema.json]: https://github.com/ProfessionalWiki/WikibaseExport/blob/master/schema.json
[example.json]: https://github.com/ProfessionalWiki/WikibaseExport/blob/master/example.json
[Rest API Documentation]: docs/rest.md
