<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\EntryPoints;

use MediaWiki\Rest\Response;
use MediaWiki\Rest\SimpleHandler;
use Wikimedia\ParamValidator\ParamValidator;

class ExportApi extends SimpleHandler {

	private const PARAM_SUBJECT_IDS = 'subjectIds';
	private const PARAM_STATEMENT_PROPERTY_IDS = 'statementPropertyIds';
	private const PARAM_START_TIME = 'startTime';
	private const PARAM_END_TIME = 'endTime';
	private const PARAM_FORMAT = 'format';

	public function run(): Response {
		// TOOD: get use case
		$response = new Response( "foo,bar\n123,456" );
		$response->setHeader( 'Content-Disposition', 'attachment; filename=export.csv;' );
		return $response;
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

}
