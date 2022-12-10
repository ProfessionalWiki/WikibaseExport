<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application;

use Wikibase\DataModel\Entity\PropertyId;

class TimeQualifierProperties {

	public function __construct(
		public /* readonly */ ?PropertyId $pointInTime,
		public /* readonly */ ?PropertyId $startTime,
		public /* readonly */ ?PropertyId $endTime,
	) {
	}

}
