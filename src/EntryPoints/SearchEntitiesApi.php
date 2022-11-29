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
		// API: wbsearchentities
		$searchData = $this->getSearchData();

		// API: wbgetentities
		$entityData = $this->getEntityData(
			array_map(
				fn( $entity ) => $entity['id'],
				$searchData['search']
			)
		);

		// Filter matches
		$validIds = $this->filterIds( $entityData['entities'] );
		$searchData['search'] = array_values(
			array_filter(
				$searchData['search'],
				fn( $entity ) => in_array( $entity['id'], $validIds )
			)
		);

		return $this->getResponseFactory()->createJson( $searchData );
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

	/**
	 * @return array<string, mixed>
	 */
	private function getSearchData(): array {
		$lang = MediaWikiServices::getInstance()->getMainConfig()->get( 'LanguageCode' );

		$api = new ApiMain(
			new DerivativeRequest(
				RequestContext::getMain()->getRequest(),
				array(
					'action' => 'wbsearchentities',
					'type' => 'item',
					'language' => $lang,
					'uselang' => $lang,
					'search' => $this->getValidatedParams()[self::PARAM_SEARCH]
				)
			)
		);
		$api->execute();

		return $api->getResult()->getResultData(
			transforms: [ 'Strip' => 'all' ]
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	private function getEntityData( array $ids ): array {
		$api = new ApiMain(
			new DerivativeRequest(
				RequestContext::getMain()->getRequest(),
				array(
					'action' => 'wbgetentities',
					'ids' => implode( '|', $ids ),
					'props' => 'claims'
				)
			)
		);
		$api->execute();

		return $api->getResult()->getResultData(
			transforms: [ 'Strip' => 'all' ]
		);
	}

	/**
	 * @param array<string, mixed> $entityData
	 * @return string[]
	 */
	private function filterIds( array $entityData ): array {
		$PID = 'P14';
		$VALUE = 'company';

		$ids = [];
		foreach( $entityData as $id => $data ) {
			if ( array_key_exists( 'P14', $data['claims'] ) ) {
				$claims = $data['claims'][$PID];
				foreach( $claims as $claim ) {
					if ( $claim['mainsnak']['datavalue']['value'] === $VALUE ) {
						$ids[] = $id;
					}
				}
			}
		}
		return $ids;
	}

}
