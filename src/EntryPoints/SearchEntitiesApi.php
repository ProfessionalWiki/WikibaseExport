<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\EntryPoints;

use ApiMain;
use DerivativeRequest;
use MediaWiki\MediaWikiServices;
use MediaWiki\Rest\Response;
use MediaWiki\Rest\SimpleHandler;
use RequestContext;
use Wikimedia\ParamValidator\ParamValidator;

class SearchEntitiesApi extends SimpleHandler {

	private const PARAM_SEARCH = 'search';

	public function run(): Response {
		$lang = MediaWikiServices::getInstance()->getMainConfig()->get( 'LanguageCode' );

		$params = new DerivativeRequest(
			RequestContext::getMain()->getRequest(),
			array(
				'action' => 'wbsearchentities',
				'type' => 'item',
				'language' => $lang,
				'uselang' => $lang,
				'search' => $this->getValidatedParams()[self::PARAM_SEARCH]
			)
		);

		$api = new ApiMain( $params );
		$api->execute();

		$data = $api->getResult()->getResultData(
			transforms: [ 'Strip' => 'all' ]
		);

		return $this->getResponseFactory()->createJson( $data );
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

}
