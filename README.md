# Wikibase Export

## PHP Configuration

Configuration can be changed via [LocalSettings.php].

### Allowed properties

List of allowed property IDs.

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
