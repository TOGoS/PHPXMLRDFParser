<?php

class TOGoS_XMLRDFParser_RDF_XMLRDFifierTest
extends TOGoS_SimplerTest_TestCase
{
	protected $rdfParserOptions = array('strict'=>true);
	
	protected function assertRefUriEquals( $uri, $ref ) {
		$this->assertTrue( $ref instanceof TOGoS_XMLRDFParser_URIRef );
		$this->assertEquals( $uri, $ref->getUri() );
	}
	
	protected function assertDataValueEquals( $uri, $ref ) {
		$this->assertNotNull($ref, "Was expecting a data value, but the object that would contain it is null");
		$this->assertTrue( $ref instanceof TOGoS_XMLRDFParser_URIRef );
		$this->assertEquals( $uri, $ref->getDataValue() );
	}
	
	function testParseText() {
		$xml = 'Hello, world!';
		$rf = new TOGoS_XMLRDFParser_RDF_XMLRDFifier( $this->rdfParserOptions );
		$v = $rf->parse($xml);
		$this->assertDataValueEquals( 'Hello, world!', $v );
	}
	
	function testParseSimpleObject() {
		$xml = '<foaf:Person xmlns:foaf="http://xmlns.com/foaf/0.1/"/>';
		$rf = new TOGoS_XMLRDFParser_RDF_XMLRDFifier( $this->rdfParserOptions );
		$rf->parse($xml);
		$v = $rf->getRootObject();
		$this->assertNotNull($v);
		$type = $v['http://www.w3.org/1999/02/22-rdf-syntax-ns#type'];
		$this->assertNotNull($type);
		$this->assertTrue(is_object($type));
		$this->assertEquals( 'http://xmlns.com/foaf/0.1/Person', $type->getUri() );
	}
	
	function testObjectWithProps() {
		$xml =
			'<foaf:Person rdf:about="#danbri" xmlns:foaf="http://xmlns.com/foaf/0.1/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">'."\n".
			'  <foaf:name>Dan Brickley</foaf:name>'."\n".
			'  <foaf:homepage rdf:resource="http://danbri.org/" />'."\n".
			'  <foaf:openid rdf:resource="http://danbri.org/" />'."\n".
			'  <foaf:img rdf:resource="/images/me.jpg" />'."\n".
			'</foaf:Person>';
		$rf = new TOGoS_XMLRDFParser_RDF_XMLRDFifier( $this->rdfParserOptions );
		$rf->parse($xml);
		
		$v = $rf->getRootObject();
		$this->assertRefUriEquals( 'http://xmlns.com/foaf/0.1/Person', $v['http://www.w3.org/1999/02/22-rdf-syntax-ns#type'] );
		$this->assertDataValueEquals( 'Dan Brickley', $v['http://xmlns.com/foaf/0.1/name'] );
		$this->assertRefUriEquals( 'http://danbri.org/', $v['http://xmlns.com/foaf/0.1/homepage'] );
		$this->assertRefUriEquals( 'http://danbri.org/', $v['http://xmlns.com/foaf/0.1/openid'] );
		$this->assertRefUriEquals( '#danbri', $v );
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
		$rf = new TOGoS_XMLRDFParser_RDF_XMLRDFifier( $this->rdfParserOptions );
		$rf->parse($xml);
		
		$dan = $rf->getRootObject();
		$this->assertRefUriEquals( 'http://xmlns.com/foaf/0.1/Person', $dan['http://www.w3.org/1999/02/22-rdf-syntax-ns#type'] );
		$this->assertDataValueEquals( 'Dan Stevens', $dan['http://xmlns.com/foaf/0.1/name'] );
		
		$jared = $dan['http://xmlns.com/foaf/0.1/knows'];
		$this->assertDataValueEquals( 'Jared Chapiewsky', $jared['http://xmlns.com/foaf/0.1/name'] );
	}
	
	function testObjectWithCollection() {
		$xml =
			'<x:Thing xmlns:x="urn:x:" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">'."\n".
			'  <x:subThings rdf:parseType="Collection">'."\n".
			'    <x:Doohickey rdf:about="urn:doohickeys:1"/>'."\n".
			'    <x:Doohickey rdf:about="urn:doohickeys:2"/>'."\n".
			'    <x:Doohickey rdf:about="urn:doohickeys:3"/>'."\n".
			'  </x:subThings>'."\n".
			'</x:Thing>';
		
		$rf = new TOGoS_XMLRDFParser_RDF_XMLRDFifier( $this->rdfParserOptions );
		$rf->parse($xml);
		
		$thing = $rf->getRootObject();
		$subThings = $thing['urn:x:subThings'];
		$this->assertNotNull($subThings, "subthings was null!");
		//$this->assertEquals(TOGoS_XMLRDFParser_RDF_
		$this->assertEquals(3, count($subThings->getItems()));
	}
	
	// rdf:RDF nodes should parse as a collection which is returned as the root object
	function testRdf() {
		$xml =
			'<rdf:RDF xmlns:x="x:" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">'."\n".
			'  <x:Thing>'."\n".
			'    <x:name>thing1</x:name>'."\n".
			'  </x:Thing>'."\n".
			'  <x:Thing>'."\n".
			'    <x:name>thing2</x:name>'."\n".
			'  </x:Thing>'."\n".
			'</rdf:RDF>';
		
		$rf = new TOGoS_XMLRDFParser_RDF_XMLRDFifier( $this->rdfParserOptions );
		$rf->parse($xml);
		$stuff = $rf->getRootObject();
		
		$things = $stuff->getItems();
		$this->assertEquals(2, count($things));
		$this->assertDataValueEquals('thing1', $things[0]['x:name']);
		$this->assertDataValueEquals('thing2', $things[1]['x:name']);
	}
}
