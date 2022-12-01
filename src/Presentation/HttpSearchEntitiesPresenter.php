<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Presentation;

use MediaWiki\Rest\Response;
use MediaWiki\Rest\ResponseFactory;
use ProfessionalWiki\WikibaseExport\Application\SearchEntities\SearchEntitiesPresenter;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Term\LabelsProvider;
use Wikibase\DataModel\Term\TermList;

class HttpSearchEntitiesPresenter implements SearchEntitiesPresenter {

	/**
	 * @var array<array{id: string, label: string}>
	 */
	private array $entitiesArray = [];

	public function __construct(
		private ResponseFactory $responseFactory
	) {
	}

	public function presentEntity( EntityDocument $entity ): void {
		if ( $entity instanceof LabelsProvider ) {
			$id = $entity->getId();
			if ( $id === null ) {
				return;
			}

			$this->entitiesArray[] = [
				'id' => $id->getLocalPart(),
				'label' => $this->getLabel( $entity->getLabels() )
			];
		}
	}

	public function getResponse(): Response {
		return $this->responseFactory->createJson( [
			'search' => $this->entitiesArray
		] );
	}

	private function getLabel( TermList $termList ): string {
		// TODO: use WB language fallback handling
		$terms = $termList->toTextArray();
		$first = reset( $terms );

		if ( $first === false ) {
			return '';
		}

		return $first;
	}

}
