<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\EntryPoints;

use MediaWiki\Rest\Response;
use MediaWiki\Rest\SimpleHandler;
use ProfessionalWiki\WikibaseExport\Presentation\HttpSearchEntitiesPresenter;
use ProfessionalWiki\WikibaseExport\WikibaseExportExtension;
use Wikimedia\ParamValidator\ParamValidator;

class SearchEntitiesApi extends SimpleHandler {

	private const PARAM_SEARCH = 'search';
	private const PARAM_LANGUAGE = 'language';

	public function run(): Response {
		$params = $this->getValidatedParams();

		$presenter = $this->newHttpPresenter();
		$searcher = WikibaseExportExtension::getInstance()->newSearchEntitiesUseCase( $presenter );
		$searcher->search( $params[self::PARAM_SEARCH], $params[self::PARAM_LANGUAGE] );

		return $presenter->getResponse();
	}

	/**
	 * @return array<string, array<string, mixed>>
	 */
	public function getParamSettings(): array {
		return [
			self::PARAM_SEARCH => [
				self::PARAM_SOURCE => 'query',
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_REQUIRED => true
			],
			self::PARAM_LANGUAGE => [
				self::PARAM_SOURCE => 'query',
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_REQUIRED => true
			]
		];
	}

	/**
	 * @inheritDoc
	 */
	public function needsWriteAccess() {
		return false;
	}

	private function newHttpPresenter(): HttpSearchEntitiesPresenter {
		return new HttpSearchEntitiesPresenter(
			responseFactory: $this->getResponseFactory()
		);
	}

}
