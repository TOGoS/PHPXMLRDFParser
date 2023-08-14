<?php

class TOGoS_XMLRDFParser_XML_XMLNamespacifierTest
extends TOGoS_SimplerTest_TestCase
{
	protected $things = array();
	
	public function text( $thing ) {
		$this->things[] = $thing;
	}
	public function openTag( $name, $attrList ) {
		$this->things[] = array($name,$attrList);
	}
	public function closeTag( $name ) {
		$this->things[] = array($name,'closed');
	}
	
	function assertOpenTag( $info, $name, $attrs ) {
		$this->assertEquals( $name, $info[0] );
		$this->assertEquals( count($attrs), count($info[1]) );
		foreach( $info[1] as $k=>$v ) {
			$this->assertEquals( $attrs[$k], $v );
		}
	}
	
	function assertCloseTag( $info, $name ) {
		$this->assertEquals( array($name,'closed'), $info );
	}
	
	public function testParseThings() {
		$xml = "<nons><a xmlns=\"foo#\" xmlns:b=\"b#\" b:r=\"b\" c=\"d\"><b:q uhm=\"wat\" b:e=\"f\"/>HHAI &amp;<q /></a></nons>";
		
		$n = new TOGoS_XMLRDFParser_XML_XMLNamespacifier( $this );
		$p = new TOGoS_XMLRDFParser_XML_XMLParser( $n );
		$p->parse( $xml );
		$this->assertEquals( 9, count($this->things) );
		$this->assertOpenTag(  $this->things[0], 'nons', array() );
		$this->assertOpenTag(  $this->things[1], 'foo#a', array('b#r'=>'b','foo#c'=>'d') );
		$this->assertOpenTag(  $this->things[2], 'b#q', array('foo#uhm'=>'wat','b#e'=>'f') );
		$this->assertCloseTag( $this->things[3], 'b#q' );
		$this->assertEquals( 'HHAI &', $this->things[4] );
		$this->assertOpenTag(  $this->things[5], 'foo#q', array() );
		$this->assertCloseTag( $this->things[6], 'foo#q' );
		$this->assertCloseTag( $this->things[7], 'foo#a' );
		$this->assertCloseTag( $this->things[8], 'nons' );
	}
}
