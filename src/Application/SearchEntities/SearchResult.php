<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\SearchEntities;

class SearchResult {

	/**
	 * @var array<array{id: string, label: string}>
	 */
	private array $searchResult = [];

	public function add( string $id, string $label ): void {
		$this->searchResult[] = [ 'id' => $id, 'label' => $label ];
	}

	/**
	 * @return array<array{id: string, label: string}>
	 */
	public function toArray(): array {
		return $this->searchResult;
	}

}
