<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\TimeRange;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Application\TimeRange
 */
class TimeRangeTest extends TestCase {

	public function testContains(): void {
		$this->assertTrue(
			( new TimeRange(
				start: new DateTimeImmutable( '2000-01-01' ),
				end: new DateTimeImmutable( '2005-12-31' ),
			) )->contains( new DateTimeImmutable( '2005-12-31' ) )
		);
	}

	public function testDoesNotContainTimeBeforeLowerBound(): void {
		$this->assertFalse(
			( new TimeRange(
				start: new DateTimeImmutable( '2000-01-01' ),
				end: new DateTimeImmutable( '2005-12-31' ),
			) )->contains( new DateTimeImmutable( '1999-12-01' ) )
		);
	}

	public function testDoesNotContainTimeAfterUpperBound(): void {
		$this->assertFalse(
			( new TimeRange(
				start: new DateTimeImmutable( '2000-01-01' ),
				end: new DateTimeImmutable( '2005-12-31' ),
			) )->contains( new DateTimeImmutable( '2006-01-01' ) )
		);
	}

	public function testNewFromStartAndEndYear(): void {
		$range = TimeRange::newFromStartAndEndYear( 2012, 2022 );
		$this->assertSame( '2012-01-01T00:00:00+00:00', $range->start->format( 'c' ) );
		$this->assertSame( '2022-12-31T23:59:59+00:00', $range->end->format( 'c' ) );
	}

}
