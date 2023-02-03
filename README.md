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

## Usage

You can export Wikibase data to CSV via the `Special:WikibaseExport` page. You can navigate directly to this page
or find it in the "Wikibase" section of the "Special pages" list on `Special:SpecialPages`.

As a wiki administrator, you can add a link to the export page to the sidebar by modifying `MediaWiki:Sidebar`.

### Choosing the export language

Wiki administrators can configure there to be an option to select the export language at the top of the export page.

TODO pic

The language selection dropdown only shows if there are multiple languages available. If the administrators do not
configure any language, the wiki's main language is used.

Example configuration on `MediaWiki:WikibaseExportConfig`:

```json
{
    "exportLanguages": [
        "en",
        "nl",
        "de"
    ]
}
```

### Selecting entities to export

At the top of the export page, select the entities you wish to export. You can either select a single entity or
multiple entities. You can search for entities to include in the export by entering their label or ID in the search box.

TODO pic

As a wiki administrator, you can configure the default entities to be selected via `MediaWiki:WikibaseExportConfig`. Example:

```json
{
    "defaultSubjects": [
        "Q1",
        "Q2"
    ]
}
```

You can also limit which entities show up in the search results via `subjectFilterPropertyId` and `subjectFilterPropertyValue`. Example:

```json
{
    "subjectFilterPropertyId": "P1",
    "subjectFilterPropertyValue": "Q2"
}
```

### Exporting ungrouped values

Wikibase Export supports filtering and grouping by year. This is however an optional feature. If you do not want
all or any of your properties to be grouped by year, use the "Ungrouped values" section. This section only shows if the wiki
administrators have configured ungrouped properties.

TODO pic

To export values for only some ungrouped properties, untick the "Values for all properties" checkbox and select
the properties you wish to export.

TODO pic

As a wiki administrator, you can configure the list of ungrouped properties via `MediaWiki:WikibaseExportConfig`. Example:

```json
{
    "ungroupedProperties": [
        "P1",
        "P2"
    ]
}
```

### Grouping values by year

If you want to group values by year, use the "Values grouped by year" section. This section only shows if the wiki
administrators have configured grouped properties.

TODO pic

Select the date range you wish to export. You can either select a single year or a range of years. The CSV will
have one column per year in the range. Example:

TODO pic

To export values for only some grouped properties, untick the "Values for all properties" checkbox and select
the properties you wish to export.

TODO pic

The grouping feature uses qualifiers to determine the year of a statement. To be included in the export, a statement that
gets grouped by year needs to have one of these qualifiers:

* Point in time
* Start time
* End time

It is common to combine Start time and End time qualifiers to define a closed range, though leaving the range open is
also supported.

TODO pic

As a wiki administrator, you can configure the grouping by year via `MediaWiki:WikibaseExportConfig`. Example:

```json
{
    "propertiesToGroupByYear": [
        "P3",
        "P4"
    ],
    "startTimePropertyId": "P10",
    "endTimePropertyId": "P11",
    "pointInTimePropertyId": "P12",
    "defaultStartYear": 2019,
    "defaultEndYear": 2023
}
```

Without `propertiesToGroupByYear` and either `pointInTimePropertyId` or both `startTimePropertyId` and `endTimePropertyId`,
the "Values grouped by year" section will not show.

### Choosing column headers

At the bottom of the export page you can choose which column headers to use. The default is to use the property IDs,
though you can choose to use property labels instead.

## REST API

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
