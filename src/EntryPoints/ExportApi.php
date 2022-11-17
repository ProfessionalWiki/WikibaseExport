<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\EntryPoints;

use MediaWiki\Rest\Response;
use MediaWiki\Rest\SimpleHandler;
use MediaWiki\Rest\StringStream;
use ProfessionalWiki\WikibaseExport\Application\Export\ExportPresenter;
use ProfessionalWiki\WikibaseExport\Application\Export\ExportRequest;
use ProfessionalWiki\WikibaseExport\Application\Export\ExportUcFactory;
use ProfessionalWiki\WikibaseExport\Presentation\NullPresenter;
use Wikimedia\ParamValidator\ParamValidator;

class ExportApi extends SimpleHandler {

	private const PARAM_SUBJECT_IDS = 'subject_ids';
	private const PARAM_STATEMENT_PROPERTY_IDS = 'statement_property_ids';
	private const PARAM_START_YEAR = 'start_year';
	private const PARAM_END_YEAR = 'end_year';
	private const PARAM_FORMAT = 'format';

	public function run(): Response {
		$presenter = $this->newPresenter();

		$exporter = ( new ExportUcFactory() )->buildUseCase(
			request: $this->buildExportRequest(),
			presenter: $presenter
		);

		$exporter->export();

		$response = $this->getResponseFactory()->create();
		$response->setHeader( 'Content-Disposition', 'attachment; filename=export.csv;' );
		$response->setHeader( 'Content-Type', 'text/csv' );
		$response->setBody( new StringStream( "foo,bar\n123,456" ) );
		return $response;
	}

	private function newPresenter(): ExportPresenter {
		return new NullPresenter(); // TODO: use format
	}

	private function buildExportRequest(): ExportRequest {
		$params = $this->getValidatedParams();

		return new ExportRequest(
			subjectIds: [], // $params[self::PARAM_SUBJECT_IDS], // TODO: parse
			statementPropertyIds: [], // $params[self::PARAM_STATEMENT_PROPERTY_IDS], // TODO: parse
			startYear: (int)$params[self::PARAM_START_YEAR],
			endYear: (int)$params[self::PARAM_END_YEAR]
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
			self::PARAM_START_YEAR => [
				self::PARAM_SOURCE => 'query',
				ParamValidator::PARAM_TYPE => 'integer',
				ParamValidator::PARAM_REQUIRED => false,
			],
			self::PARAM_END_YEAR => [
				self::PARAM_SOURCE => 'query',
				ParamValidator::PARAM_TYPE => 'integer',
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
