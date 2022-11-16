<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\EntryPoints;

use MediaWiki\Rest\Response;
use MediaWiki\Rest\SimpleHandler;
use MediaWiki\Rest\StringStream;
use ProfessionalWiki\WikibaseExport\Application\Export\EntityMapper;
use ProfessionalWiki\WikibaseExport\Application\Export\ExportUseCase;
use ProfessionalWiki\WikibaseExport\Application\Export\StatementMapper;
use ProfessionalWiki\WikibaseExport\Application\ExportStatementFilter;
use ProfessionalWiki\WikibaseExport\Application\TimeQualifierProperties;
use ProfessionalWiki\WikibaseExport\Application\TimeQualifierStatementGrouper;
use ProfessionalWiki\WikibaseExport\Application\TimeRange;
use ProfessionalWiki\WikibaseExport\Persistence\InMemoryEntitySource;
use ProfessionalWiki\WikibaseExport\Presentation\NullPresenter;
use Wikibase\DataModel\Entity\NumericPropertyId;
use Wikimedia\ParamValidator\ParamValidator;

class ExportApi extends SimpleHandler {

	private const PARAM_SUBJECT_IDS = 'subject_ids';
	private const PARAM_STATEMENT_PROPERTY_IDS = 'statement_property_ids';
	private const PARAM_START_TIME = 'start_time';
	private const PARAM_END_TIME = 'end_time';
	private const PARAM_FORMAT = 'format';

	public function run(): Response {
		$exporter = $this->newExportUseCase();

		$exporter->export();

		$response = $this->getResponseFactory()->create();
		$response->setHeader( 'Content-Disposition', 'attachment; filename=export.csv;' );
		$response->setHeader( 'Content-Type', 'text/csv' );
		$response->setBody( new StringStream( "foo,bar\n123,456" ) );
		return $response;
	}

	private function newExportUseCase(): ExportUseCase {
		$params = $this->getValidatedParams();

		return new ExportUseCase(
			entitySource: new InMemoryEntitySource(), // TODO: use subject IDs
			entityMapper: $this->newEntityMapper( $params ),
			presenter: new NullPresenter() // TODO: use format
		);
	}

	/**
	 * @param array<string, mixed> $params
	 */
	private function newEntityMapper( array $params ): EntityMapper {
		$timeQualifierProperties = $this->newTimeQualifierProperties();

		return new EntityMapper(
			statementFilter: new ExportStatementFilter(
				propertyIds: [], // $params[self::PARAM_STATEMENT_PROPERTY_IDS], // TODO: parse
				timeRange: new TimeRange(
					start: new \DateTimeImmutable(), // $params[self::PARAM_START_TIME], // TODO: parse
					end: new \DateTimeImmutable(), // $params[self::PARAM_END_TIME], // TODO: parse
				),
				qualifierProperties: $timeQualifierProperties
			),
			statementGrouper: TimeQualifierStatementGrouper::newForYearRange(
				timeQualifierProperties: $timeQualifierProperties,
				startYear: 2000, // $params[self::PARAM_START_TIME], // TODO: parse
				endYear: 2022 // $params[self::PARAM_END_TIME], // TODO: parse
			),
			statementMapper: new StatementMapper()
		);
	}

	private function newTimeQualifierProperties(): TimeQualifierProperties {
		return new TimeQualifierProperties(
			pointInTime: new NumericPropertyId( 'P1' ), // TODO: get from config
			startTime: new NumericPropertyId( 'P1' ), // TODO: get from config
			endTime: new NumericPropertyId( 'P1' ), // TODO: get from config
		);
	}

	/**
	 * @return array<string, array<string, mixed>>
	 */
	public function getParamSettings(): array {
		return [
			self::PARAM_SUBJECT_IDS => [
				self::PARAM_SOURCE => 'query',
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_REQUIRED => true,
				ParamValidator::PARAM_ISMULTI => true
			],
			self::PARAM_STATEMENT_PROPERTY_IDS => [
				self::PARAM_SOURCE => 'query',
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_REQUIRED => true,
				ParamValidator::PARAM_ISMULTI => true
			],
			self::PARAM_START_TIME => [
				self::PARAM_SOURCE => 'query',
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_REQUIRED => false,
			],
			self::PARAM_END_TIME => [
				self::PARAM_SOURCE => 'query',
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_REQUIRED => false,
			],
			self::PARAM_FORMAT => [
				self::PARAM_SOURCE => 'query',
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_REQUIRED => false,
			]
		];
	}

	/**
	 * @inheritDoc
	 */
	public function needsWriteAccess() {
		return false;
	}

}
