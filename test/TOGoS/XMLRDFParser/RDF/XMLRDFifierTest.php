<?php

class TOGoS_XMLRDFParser_RDF_XMLRDFifierTest extends PHPUnit_Framework_TestCase
{
	function testParseText() {
		$xml = 'Hello, world!';
		$rdfos = new TOGoS_XMLRDFParser_RDF_RDFObjectConstructor();
		$rf = new TOGoS_XMLRDFParser_RDF_XMLRDFifier( $rdfos );
		$rf->parse($xml);
		$this->assertEquals( 'Hello, world!', $rf->getValue() );
	}
	
	function testParseSimpleObject() {
		$xml = '<foaf:Person xmlns:foaf="http://xmlns.com/foaf/0.1/"/>';
		$rdfos = new TOGoS_XMLRDFParser_RDF_RDFObjectConstructor();
		$rf = new TOGoS_XMLRDFParser_RDF_XMLRDFifier( $rdfos );
		$rf->parse($xml);
		$v = $rf->getValue();
		$this->assertEquals( 'http://xmlns.com/foaf/0.1/Person', $v['http://www.w3.org/1999/02/22-rdf-syntax-ns#type']->getUri() );
	}
	
	protected function assertRefUriEquals( $uri, $ref ) {
		$this->assertTrue( $ref instanceof TOGoS_XMLRDFParser_URIRef );
		$this->assertEquals( $uri, $ref->getUri() );
	}
	
	function testObjectWithProps() {
		$xml =
			'<foaf:Person rdf:about="#danbri" xmlns:foaf="http://xmlns.com/foaf/0.1/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">'."\n".
			'  <foaf:name>Dan Brickley</foaf:name>'."\n".
			'  <foaf:homepage rdf:resource="http://danbri.org/" />'."\n".
			'  <foaf:openid rdf:resource="http://danbri.org/" />'."\n".
			'  <foaf:img rdf:resource="/images/me.jpg" />'."\n".
			'</foaf:Person>';
		$rdfos = new TOGoS_XMLRDFParser_RDF_RDFObjectConstructor();
		$rf = new TOGoS_XMLRDFParser_RDF_XMLRDFifier( $rdfos );
		$rf->parse($xml);
		
		$v = $rf->getValue();
		$this->assertRefUriEquals( 'http://xmlns.com/foaf/0.1/Person', $v['http://www.w3.org/1999/02/22-rdf-syntax-ns#type'] );
		$this->assertEquals( 'Dan Brickley', $v['http://xmlns.com/foaf/0.1/name'] );
		$this->assertRefUriEquals( 'http://danbri.org/', $v['http://xmlns.com/foaf/0.1/homepage'] );
		$this->assertRefUriEquals( 'http://danbri.org/', $v['http://xmlns.com/foaf/0.1/openid'] );
		$this->assertRefUriEquals( '/images/me.jpg', $v['http://xmlns.com/foaf/0.1/img'] );
	}
	
	function testObjectWithEmbeddedProps() {
		$xml =
			'<foaf:Person xmlns:foaf="http://xmlns.com/foaf/0.1/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">'."\n".
			'  <foaf:homepage rdf:resource="http://www.nuke24.net/"/>'."\n".
			'  <foaf:knows>'."\n".
			'    <foaf:Person>'."\n".
			'      <foaf:name>Jared Chapiewsky</foaf:name>'."\n".
			'    </foaf:Person>'."\n".
			'  </foaf:knows>'."\n".
			'  <foaf:name>Dan Stevens</foaf:name>'."\n".
			'</foaf:Person>';
		$rdfos = new TOGoS_XMLRDFParser_RDF_RDFObjectConstructor();
		$rf = new TOGoS_XMLRDFParser_RDF_XMLRDFifier( $rdfos );
		$rf->parse($xml);
		
		$dan = $rf->getValue();
		$this->assertRefUriEquals( 'http://xmlns.com/foaf/0.1/Person', $dan['http://www.w3.org/1999/02/22-rdf-syntax-ns#type'] );
		$this->assertEquals( 'Dan Stevens', $dan['http://xmlns.com/foaf/0.1/name'] );
		
		$jared = $dan['http://xmlns.com/foaf/0.1/knows'];
		$this->assertEquals( 'Jared Chapiewsky', $jared['http://xmlns.com/foaf/0.1/name'] );
	}
}
