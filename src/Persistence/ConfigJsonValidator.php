<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Persistence;

class ConfigJsonValidator {

	public static function newInstance(): self {
		// TODO load schema

		return new self();
	}

	private function __construct() {
	}

	public function validate( string $configJson ): bool {
		// TOOD: implement validation
		return true;
	}

	public function getError(): string {
		// TODO: return message string
		return '';
	}

}
