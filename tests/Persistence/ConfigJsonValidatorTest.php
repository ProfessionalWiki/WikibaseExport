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

	public function testEmptyJsonPassesValidation(): void {
		$this->assertTrue(
			ConfigJsonValidator::newInstance()->validate( '{}' )
		);
	}

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

	public function testInvalidPropertyIdFailsValidation(): void {
		$this->assertFalse(
			ConfigJsonValidator::newInstance()->validate( '{ "pointInTimePropertyId": "Q123" }' )
		);

		$this->assertFalse(
			ConfigJsonValidator::newInstance()->validate( '{ "pointInTimePropertyId": "p123" }' )
		);
	}

	public function testValidPropertyIdPassesValidation(): void {
		$this->assertTrue(
			ConfigJsonValidator::newInstance()->validate( '{ "pointInTimePropertyId": "P123" }' )
		);
	}

	public function testInvalidJsonErrorsAreAvailable(): void {
		$validator = ConfigJsonValidator::newInstance();

		$validator->validate( '{ "defaultStartYear": "2022" }' );

		$this->assertSame(
			[ '/defaultStartYear' => 'The data (string) must match the type: integer, null' ],
			$validator->getErrors()
		);
	}

	public function testEndDateGreaterThanStartDatePassesValidation(): void {
		$validator = ConfigJsonValidator::newInstance();

		$result = $validator->validate( '{ "defaultStartYear": 2021, "defaultEndYear": 2022 }' );

		$this->assertTrue( $result );
	}

	public function testEndDateSameAsStartDatePassesValidation(): void {
		$validator = ConfigJsonValidator::newInstance();

		$result = $validator->validate( '{ "defaultStartYear": 2022, "defaultEndYear": 2022 }' );

		$this->assertTrue( $result );
	}

	public function testStartDateGreaterThanEndDateFailsValidation(): void {
		$validator = ConfigJsonValidator::newInstance();

		$result = $validator->validate( '{ "defaultStartYear": 2022, "defaultEndYear": 2021 }' );

		$this->assertFalse( $result );

		$this->assertSame(
			[ '/defaultEndYear' => 'Number must be greater than or equal to 2022' ],
			$validator->getErrors()
		);
	}

	public function testMissingStartDatePassesValidation(): void {
		$validator = ConfigJsonValidator::newInstance();

		$result = $validator->validate( '{ "defaultEndYear": 2022 }' );

		$this->assertTrue( $result );
	}

	public function testMissingEndDatePassesValidation(): void {
		$validator = ConfigJsonValidator::newInstance();

		$result = $validator->validate( '{ "defaultStartYear": 2022 }' );

		$this->assertTrue( $result );
	}

}
