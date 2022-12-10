<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application\Export;

use DataValues\StringValue;
use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\Export\ColumnHeader;
use ProfessionalWiki\WikibaseExport\Application\Export\ColumnHeaders;
use ProfessionalWiki\WikibaseExport\Application\Export\SimpleStatementsMapper;
use ProfessionalWiki\WikibaseExport\Application\Export\ValueSet;
use ProfessionalWiki\WikibaseExport\Application\Export\ValueSetList;
use ProfessionalWiki\WikibaseExport\Application\PropertyIdList;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\FakeHeaderBuilder;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\FakeValueSetCreator;
use Wikibase\DataModel\Entity\NumericPropertyId;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\DataModel\Statement\StatementList;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Application\Export\SimpleStatementsMapper
 */
class SimpleStatementsMapperTest extends TestCase {

	public function testHeaders(): void {
		$mapper = new SimpleStatementsMapper(
			valueSetCreator: new FakeValueSetCreator(),
			propertyIds: new PropertyIdList( [
				new NumericPropertyId( 'P1' ),
				new NumericPropertyId( 'P3' ),
				new NumericPropertyId( 'P2' ),
			] ),
			headerBuilder: new FakeHeaderBuilder()
		);

		$this->assertEquals(
			new ColumnHeaders( [
				new ColumnHeader( 'P1' ),
				new ColumnHeader( 'P3' ),
				new ColumnHeader( 'P2' ),
			] ),
			$mapper->createColumnHeaders()
		);
	}

	public function testNoPropertyIdsResultsInEmptyValueSetList(): void {
		$mapper = new SimpleStatementsMapper(
			valueSetCreator: new FakeValueSetCreator(),
			propertyIds: new PropertyIdList(),
			headerBuilder: new FakeHeaderBuilder()
		);

		$statements = new StatementList(
			new Statement( new PropertyValueSnak( new NumericPropertyId( 'P4' ), new StringValue( 'foo' ) ) )
		);

		$this->assertEquals(
			new ValueSetList( [] ),
			$mapper->buildValueSetList( $statements )
		);
	}

	public function testPropertiesWithoutValuesResultInEmptyValueSets(): void {
		$mapper = new SimpleStatementsMapper(
			valueSetCreator: new FakeValueSetCreator(),
			propertyIds: new PropertyIdList( [
				new NumericPropertyId( 'P1' ),
				new NumericPropertyId( 'P2' ),
			] ),
			headerBuilder: new FakeHeaderBuilder()
		);

		$statements = new StatementList(
			new Statement( new PropertyValueSnak( new NumericPropertyId( 'P4' ), new StringValue( 'ID mismatch' ) ) )
		);

		$this->assertEquals(
			new ValueSetList( [
				new ValueSet( [] ),
				new ValueSet( [] ),
			] ),
			$mapper->buildValueSetList( $statements )
		);
	}

	public function testMatchingValuesAreIncludedInTheValueSets(): void {
		$mapper = new SimpleStatementsMapper(
			valueSetCreator: new FakeValueSetCreator(),
			propertyIds: new PropertyIdList( [
				new NumericPropertyId( 'P1' ),
				new NumericPropertyId( 'P2' ),
			] ),
			headerBuilder: new FakeHeaderBuilder()
		);

		$statements = new StatementList(
			new Statement( new PropertyValueSnak( new NumericPropertyId( 'P4' ), new StringValue( 'ID mismatch' ) ) ),
			new Statement( new PropertyValueSnak( new NumericPropertyId( 'P2' ), new StringValue( '2 first' ) ) ),
			new Statement( new PropertyValueSnak( new NumericPropertyId( 'P1' ), new StringValue( '1' ) ) ),
			new Statement( new PropertyValueSnak( new NumericPropertyId( 'P2' ), new StringValue( '2 second' ) ) ),
			new Statement( new PropertyValueSnak( new NumericPropertyId( 'P2' ), new StringValue( '2 third' ) ) ),
			new Statement( new PropertyValueSnak( new NumericPropertyId( 'P4' ), new StringValue( 'ID mismatch' ) ) ),
		);

		$this->assertEquals(
			new ValueSetList( [
				new ValueSet( [ '1' ] ),
				new ValueSet( [ '2 first', '2 second', '2 third' ] ),
			] ),
			$mapper->buildValueSetList( $statements )
		);
	}

	public function testIncludesOnlyBestValuesPerStatement(): void {
		$mapper = new SimpleStatementsMapper(
			valueSetCreator: new FakeValueSetCreator(),
			propertyIds: new PropertyIdList( [
				new NumericPropertyId( 'P1' ),
				new NumericPropertyId( 'P2' ),
			] ),
			headerBuilder: new FakeHeaderBuilder()
		);

		$normalRankP1 = new Statement( new PropertyValueSnak( new NumericPropertyId( 'P1' ), new StringValue( 'Normal' ) ) );
		$deprecatedP1 = new Statement( new PropertyValueSnak( new NumericPropertyId( 'P1' ), new StringValue( 'Deprecated' ) ) );
		$deprecatedP1->setRank( Statement::RANK_DEPRECATED );
		$anotherNormalP1 = new Statement( new PropertyValueSnak( new NumericPropertyId( 'P1' ), new StringValue( 'Another normal' ) ) );

		$normalRankP2 = new Statement( new PropertyValueSnak( new NumericPropertyId( 'P2' ), new StringValue( 'Normal' ) ) );
		$preferredP2 = new Statement( new PropertyValueSnak( new NumericPropertyId( 'P2' ), new StringValue( 'Preferred' ) ) );
		$preferredP2->setRank( Statement::RANK_PREFERRED );

		$statements = new StatementList( $normalRankP1, $deprecatedP1, $anotherNormalP1, $normalRankP2, $preferredP2 );

		$this->assertEquals(
			new ValueSetList( [
				new ValueSet( [ 'Normal', 'Another normal' ] ),
				new ValueSet( [ 'Preferred' ] ),
			] ),
			$mapper->buildValueSetList( $statements )
		);
	}

}
