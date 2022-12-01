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

	public function run(): Response {
		$presenter = $this->newHttpPresenter();
		$uc = WikibaseExportExtension::getInstance()->newSearchEntitiesUseCase( $presenter );
		$uc->search( $this->getValidatedParams()[self::PARAM_SEARCH] );

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
