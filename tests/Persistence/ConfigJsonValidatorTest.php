<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Persistence;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Persistence\ConfigJsonValidator;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\Valid;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Persistence\ConfigJsonValidator
 */
class ConfigJsonValidatorTest extends TestCase {

	public function testValidJsonPassesValidation(): void {
		$this->assertTrue(
			ConfigJsonValidator::newInstance()->validate( Valid::configJson() )
		);
	}

	public function testStructurallyInvalidJsonFailsValidation(): void {
		$this->assertFalse(
			ConfigJsonValidator::newInstance()->validate( '}{' )
		);
	}

	public function testInvalidJsonValueFailsValidation(): void {
		$this->assertFalse(
			ConfigJsonValidator::newInstance()->validate( '{ "pointInTimePropertyId": "Q123abc" }' )
		);
	}

	public function testInvalidJsonErrorsAreAvailable(): void {
		$validator = ConfigJsonValidator::newInstance();

		$validator->validate( '{ "defaultStartYear": "2022" }' );

		$this->assertSame(
			[ '/defaultStartYear' => 'The data (string) must match the type: integer' ],
			$validator->getErrors()
		);
	}

}
