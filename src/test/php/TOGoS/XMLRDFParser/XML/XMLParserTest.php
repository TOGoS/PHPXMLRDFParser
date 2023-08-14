<?php

class TOGoS_XMLRDFParser_XML_XMLParserTest
extends TOGoS_SimplerTest_TestCase
implements TOGoS_XMLRDFParser_XML_XMLConsumer
{
	protected $things = array();
	
	public function text( $thing ) {
		$this->things[] = $thing;
	}
	public function openTag( $name, array $attributes ) {
		$this->things[] = array($name,$attributes);
	}
	public function closeTag( $name ) {
		$this->things[] = array($name,'closed');
	}
	
	public function testParseThings() {
		$xml = "<!--IGNORE-ME!-->&#x6A19;&#x6E96;&#x842C;&#x570B;&#x78BC;<tag>hi<xx:brag xmlns:xx=\"bag\" something=\"&amp; &quot;\"/></mag>";
		
		$p = new TOGoS_XMLRDFParser_XML_XMLParser( $this );
		$p->parse( $xml );
		
		$this->assertEquals( 6, count($this->things) );
		
		$this->assertEquals( "\xE6\xA8\x99\xE6\xBA\x96\xE8\x90\xAC\xE5\x9C\x8B\xE7\xA2\xBC", $this->things[0] );
		
		$this->assertEquals( 'tag', $this->things[1][0] );
		$this->assertEquals( 0, count($this->things[1][1]) );
		
		$this->assertEquals( 'hi', $this->things[2] );
		
		$this->assertEquals( 'xx:brag', $this->things[3][0] );
		$this->assertEquals( 2, count($this->things[3][1]) );
		$this->assertEquals( 'bag',       $this->things[3][1]['xmlns:xx'] );
		$this->assertEquals( '& "',       $this->things[3][1]['something'] );
		
		$this->assertEquals( array('xx:brag','closed'), $this->things[4] );
		$this->assertEquals( array('mag','closed'), $this->things[5] );
	}
}
