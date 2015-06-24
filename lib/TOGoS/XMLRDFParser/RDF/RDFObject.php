<?php

class TOGoS_XMLRDFParser_RDF_RDFObject implements ArrayAccess, TOGoS_XMLRDFParser_URIRef
{
	protected $uris = array();
	protected $props = array();
	
	const RDF_TYPE_PROPERTY = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type';
	
	public function __construct( $className=null, $propMap=array(), $uri ) {
		if( $className !== null ) {
			$this->addProperty( self::RDF_TYPE_PROPERTY, new TOGoS_XMLRDFParser_RDF_URIRef($className) );
		}
		foreach( $propMap as $k=>$v ) {
			$this->addProprety( $k, $v );
		}
		if( $uri ) $this->addUri($uri);
	}
	
	public function getUris() { return $this->uris; }
	public function addUri( $uri ) { $this->uris[$uri] = $uri; }
	public function getUri() { foreach($this->uris as $uri) return $uri; return null; }
	
	public function addProperty( $k, $v ) {
		if( !isset($this->props[$k]) ) {
			$this->props[$k] = array();
		}
		$this->props[$k][] = $v;
	}
	
	public function getAll( $k ) {
		return isset($this->props[$k]) ? $this->props[$k] : array();
	}
	
	public function getRdfTypeName() {
		$ref = $this[self::RDF_TYPE_PROPERTY];
		return $ref === null ? null : $ref->getUri();
	}
	
	//// Array access for singular properties ////
	
	public function offsetExists( $k ) {
		return isset($this->props[$k]) && count($this->props[$k]) > 0;
	}
	
	public function offsetSet( $k, $v ) {
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
