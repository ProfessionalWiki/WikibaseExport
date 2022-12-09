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
use ProfessionalWiki\WikibaseExport\Application\Export\YearGroupingStatementsMapper;
use ProfessionalWiki\WikibaseExport\Application\PropertyIdList;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\FakeHeaderBuilder;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\FakeValueSetCreator;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\TimeHelper;
use Wikibase\DataModel\Entity\NumericPropertyId;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\DataModel\Statement\StatementList;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Application\Export\YearGroupingStatementsMapper
 */
class YearGroupingStatementsMapperTest extends TestCase {

	public function testYearlyGroupedHeaders(): void {
		$mapper = new YearGroupingStatementsMapper(
			valueSetCreator: new FakeValueSetCreator(),
			yearGroupedProperties: new PropertyIdList( [
				new NumericPropertyId( 'P1' ),
				new NumericPropertyId( 'P2' ),
			] ),
			timeQualifierProperties: TimeHelper::newTimeQualifierProperties(),
			startYear: 2000,
			endYear: 2002,
			headerBuilder: new FakeHeaderBuilder()
		);

		$this->assertEquals(
			new ColumnHeaders( [
				new ColumnHeader( 'P1 2002' ),
				new ColumnHeader( 'P1 2001' ),
				new ColumnHeader( 'P1 2000' ),
				new ColumnHeader( 'P2 2002' ),
				new ColumnHeader( 'P2 2001' ),
				new ColumnHeader( 'P2 2000' ),
			] ),
			$mapper->createColumnHeaders()
		);
	}

	public function testGroupsPointInTimeStatements(): void {
		$mapper = new YearGroupingStatementsMapper(
			valueSetCreator: new FakeValueSetCreator(),
			yearGroupedProperties: new PropertyIdList( [
				new NumericPropertyId( 'P11' ),
				new NumericPropertyId( 'P22' ),
			] ),
			timeQualifierProperties: TimeHelper::newTimeQualifierProperties(),
			startYear: 2148,
			endYear: 2151,
			headerBuilder: new FakeHeaderBuilder()
		);

		$statements = new StatementList(
			TimeHelper::newPointInTimeStatement( day: '2147-12-31', pId: 'P22', value: 'Too early' ),
			TimeHelper::newPointInTimeStatement( day: '2148-01-01', pId: 'P22', value: 'Included lower bound' ),
			TimeHelper::newPointInTimeStatement( day: '2151-12-31', pId: 'P22', value: 'Included upper bound' ),
			TimeHelper::newPointInTimeStatement( day: '2152-01-01', pId: 'P22', value: 'Too late' ),

			TimeHelper::newPointInTimeStatement( day: '2022-12-09', pId: 'P11', value: 'Today' ),
			TimeHelper::newPointInTimeStatement( day: '2150-12-07', pId: 'P11', value: 'Last day' ),
			TimeHelper::newPointInTimeStatement( day: '2149-01-01', pId: 'P11', value: '2049' ),
			TimeHelper::newPointInTimeStatement( day: '2150-12-08', pId: 'P11', value: '???' ),
			TimeHelper::newPointInTimeStatement( day: '2258-01-01', pId: 'P11', value: 'Last best hope' ),
		);

		$this->assertEquals(
			new ValueSetList( [
				new ValueSet( [] ),
				new ValueSet( [ 'Last day', '???' ] ),
				new ValueSet( [ '2049' ] ),
				new ValueSet( [] ),
				new ValueSet( [ 'Included upper bound' ] ),
				new ValueSet( [] ),
				new ValueSet( [] ),
				new ValueSet( [ 'Included lower bound' ] ),
			] ),
			$mapper->buildValueSetList( $statements )
		);
	}

	public function testGroupsTimeRangeStatements(): void {
		$mapper = new YearGroupingStatementsMapper(
			valueSetCreator: new FakeValueSetCreator(),
			yearGroupedProperties: new PropertyIdList( [
				new NumericPropertyId( 'P11' ),
				new NumericPropertyId( 'P22' ),
			] ),
			timeQualifierProperties: TimeHelper::newTimeQualifierProperties(),
			startYear: 2022,
			endYear: 2023,
			headerBuilder: new FakeHeaderBuilder()
		);

		$statements = new StatementList(
			TimeHelper::newDayRangeStatement( '2023-01-01', null, 'P11', new StringValue( 'Open ended' ) ),
			TimeHelper::newDayRangeStatement( null, '2023-01-01', 'P11', new StringValue( 'Open start' ) ),
			TimeHelper::newDayRangeStatement( '2000-01-01', '2100-01-01', 'P11', new StringValue( 'Spanning all' ) ),

			TimeHelper::newDayRangeStatement( '2021-12-31', '2021-12-31', 'P22', new StringValue( 'Before lower bound' ) ),
			TimeHelper::newDayRangeStatement( '2021-12-31', '2022-01-01', 'P22', new StringValue( 'Included lower bound' ) ),
			TimeHelper::newDayRangeStatement( '2023-12-31', '2024-12-31', 'P22', new StringValue( 'Included upper bound' ) ),
			TimeHelper::newDayRangeStatement( '2024-01-01', '2024-12-31', 'P22', new StringValue( 'After upper bound' ) ),
		);

		$this->assertEquals(
			new ValueSetList( [
				new ValueSet( [ 'Open ended', 'Open start', 'Spanning all' ] ),
				new ValueSet( [ 'Open start', 'Spanning all' ] ),
				new ValueSet( [ 'Included upper bound' ] ),
				new ValueSet( [ 'Included lower bound' ] ),
			] ),
			$mapper->buildValueSetList( $statements )
		);
	}

	public function testIncludesOnlyBestValuesPerStatement(): void {
		$mapper = new YearGroupingStatementsMapper(
			valueSetCreator: new FakeValueSetCreator(),
			yearGroupedProperties: new PropertyIdList( [
				new NumericPropertyId( 'P11' ),
				new NumericPropertyId( 'P22' ),
			] ),
			timeQualifierProperties: TimeHelper::newTimeQualifierProperties(),
			startYear: 2022,
			endYear: 2022,
			headerBuilder: new FakeHeaderBuilder()
		);

		$normalRankP1 = TimeHelper::newPointInTimeStatement( day: '2022-12-09', pId: 'P11', value: 'Normal' );
		$deprecatedP1 = TimeHelper::newPointInTimeStatement( day: '2022-12-09', pId: 'P11', value: 'Deprecated' );
		$deprecatedP1->setRank( Statement::RANK_DEPRECATED );
		$anotherNormalP1 = TimeHelper::newPointInTimeStatement( day: '2022-12-09', pId: 'P11', value: 'Another normal' );

		$normalRankP2 = TimeHelper::newPointInTimeStatement( day: '2022-12-09', pId: 'P22', value: 'Normal' );
		$preferredP2 = TimeHelper::newPointInTimeStatement( day: '2022-12-09', pId: 'P22', value: 'Preferred' );
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
