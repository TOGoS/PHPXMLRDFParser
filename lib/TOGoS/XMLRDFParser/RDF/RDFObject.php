<?php

class TOGoS_XMLRDFParser_RDF_RDFObject implements ArrayAccess, TOGoS_XMLRDFParser_URIRef
{
	public static function ref($uri) {
		return new self(null,array(),$uri);
	}
	public static function dataValue($v) {
		return new self(TOGoS_XMLRDFParser_RDF_Namespaces::RDF_DATA,array(),null,$v);
	}
	public static function collection(array $items=array()) {
		$collection = new self(TOGoS_XMLRDFParser_RDF_Namespaces::RDF_COLLECTION);
		foreach( $items as $item ) $collection->addItem($item);
		return $collection;
	}
	
	protected $uris = array();
	protected $props = array();
	protected $items = array();
	protected $dataValue;
		
	public function __construct( $className=null, array $propMap=array(), $uri=null, $dataValue=null ) {
		if( $className !== null ) {
			$this->addProperty( TOGoS_XMLRDFParser_RDF_Namespaces::RDF_TYPE, self::ref($className) );
		}
		foreach( $propMap as $k=>$v ) {
			$this->addProprety( $k, $v );
		}
		if( $uri ) $this->addUri($uri);
		$this->dataValue = $dataValue;
	}
	
	public function addUri( $uri ) {
		$this->uris[$uri] = $uri;
	}
	public function getUris() {
		return $this->uris;
	}
	public function getUri() {
		foreach( $this->uris as $uri ) return $uri; return null;
	}
	
	public function addItem(TOGoS_XMLRDFParser_RDF_RDFObject $item) {
		$this->addProperty(TOGoS_XMLRDFParser_RDF_Namespaces::RDF_ITEM, $item);
	}
	public function getItems() {
		return $this->getAll(TOGoS_XMLRDFParser_RDF_Namespaces::RDF_ITEM);
	}	
	
	public function getDataValue() {
		return $this->dataValue;
	}
	
	public function getProperties() {
		return $this->props;
	}
	
	public function addProperty( $k, TOGoS_XMLRDFParser_RDF_RDFObject $v ) {
		if( !isset($this->props[$k]) ) {
			$this->props[$k] = array();
		}
		$this->props[$k][] = $v;
	}
	
	public function getAll( $k ) {
		return isset($this->props[$k]) ? $this->props[$k] : array();
	}
	
	public function getRdfTypeName() {
		$ref = $this[TOGoS_XMLRDFParser_RDF_Namespaces::RDF_TYPE];
		return $ref === null ? null : $ref->getUri();
	}
	
	//// Array access for singular properties ////
	
	public function offsetExists( $k ) {
		return isset($this->props[$k]) && count($this->props[$k]) > 0;
	}
	
	public function offsetSet( $k, $v ) {
		if( !($v instanceof TOGoS_XMLRDFParser_RDF_RDFObject) ) {
			throw new Exception("Property values must themselves be RDFObjects.");
		}
		$this->props[$k] = array($v);
	}
	
	public function offsetGet( $k ) {
		if( isset($this->props[$k]) ) {
			foreach( $this->props[$k] as $v ) return $v;
		}
		return null;
	}
	
	public function offsetUnset( $k ) {
		unset($this->props[$k]);
	}
}
