<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\DataModel\Statement\StatementFilter;
use Wikibase\DataModel\Statement\StatementList;
use Wikibase\DataModel\Statement\StatementListProvider;

class EntityMapper {

	public function __construct(
		private StatementFilter $statementFilter
	) {
	}

	public function map( EntityDocument $entity ): MappedEntity {
		return new MappedEntity(
			id: (string)$entity->getId(),
			statements: $this->mapStatements( $entity )
		);
	}

	/**
	 * @return MappedStatement[]
	 */
	private function mapStatements( EntityDocument $entity ): array {
		return array_map(
			[ $this, 'mapStatement' ],
			$this->getFilteredStatements( $entity )->toArray()
		);
	}

	private function getFilteredStatements( EntityDocument $entity ): StatementList {
		if ( $entity instanceof StatementListProvider ) {
			return $entity->getStatements()->filter( $this->statementFilter );
		}

		return new StatementList();
	}

	private function mapStatement( Statement $statement ): MappedStatement {
		return new MappedStatement(
			mainValue: $this->statementToString( $statement )
		);
	}

	private function statementToString( Statement $statement ): string {
		$snak = $statement->getMainSnak();

		if ( $snak instanceof PropertyValueSnak ) {
			/**
			 * @var string $serialization
			 */
			$serialization = $snak->getDataValue()->serialize();
			return $serialization; // TODO: maybe need to serialize in another manner
		}

		return ''; // TODO: empty string for NoValue and SomeValue?
	}

}
