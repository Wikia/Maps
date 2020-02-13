<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit\DataAccess;

use Maps\DataAccess\GeoJsonStore\SubObject;
use PHPUnit\Framework\TestCase;
use SMW\DIProperty;
use SMW\DIWikiPage;

/**
 * @covers \Maps\DataAccess\GeoJsonStore\SubObject
 */
class SubObjectTest extends TestCase {

	private const PAGE_NS = NS_GEO_JSON;
	private const PAGE_TITLE = 'TestGeoJson';

	public function testEmpty() {
		$subObject = new SubObject( 'MyName' );

		$container = $subObject->toContainerSemanticData( $this->newTitleValue() );

		$this->assertEquals(
			new DIWikiPage( self::PAGE_TITLE, self::PAGE_NS, '', 'MyName' ),
			$container->getSubject()
		);

		$this->assertSame( [], $container->getErrors() );
		$this->assertSame( [], $container->getProperties() );
	}

	private function newTitleValue(): \TitleValue {
		return new \TitleValue( self::PAGE_NS, self::PAGE_TITLE );
	}

	public function testPropertyValuesPairsAreTransformed() {
		$subObject = new SubObject( 'MyName' );
		$subObject->addPropertyValuePair( 'SuchNumber', new \SMWDINumber( 42 ) );
		$subObject->addPropertyValuePair( 'SuchNumber', new \SMWDINumber( 23 ) );
		$subObject->addPropertyValuePair( 'SuchBoolean', new \SMWDIBoolean( true ) );

		$container = $subObject->toContainerSemanticData( $this->newTitleValue() );

		$this->assertEquals(
			[
				'SuchNumber' => new DIProperty( 'SuchNumber' ),
				'SuchBoolean' => new DIProperty( 'SuchBoolean' ),
			],
			$container->getProperties()
		);

		$this->assertEquals(
			[
				new \SMWDINumber( 42 ) ,
				new \SMWDINumber( 23 ) ,
			],
			$container->getPropertyValues( new DIProperty( 'SuchNumber' ) )
		);

		$this->assertSame( [], $container->getErrors() );
	}

}
