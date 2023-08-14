<?php

class TOGoS_XMLRDFParser_XML_NSStackEntry
{
	public $parent;
	public $namespaces;
	
	public function __construct( $parent ) {
		$this->parent = $parent;
		$this->namespaces = $parent === null ? array() : $parent->namespaces;
	}
}

class TOGoS_XMLRDFParser_XML_XMLNamespacifier implements TOGoS_XMLRDFParser_XML_XMLConsumer
{
	protected $nsStack;
	public $next;
	
	public function __construct( $next ) {
		$this->nsStack = new TOGoS_XMLRDFParser_XML_NSStackEntry( null );
		$this->next = $next;
	}
	
	public function text( $n ) {
		$this->next->text( $n );
	}
	
	protected function namespacify( $shortName ) {
		if( preg_match('#^([^:]+):(.+)$#',$shortName,$bif) ) {
			$nsAbbrev = $bif[1];
			$shortName = $bif[2];
			$ns = @$this->nsStack->namespaces[$nsAbbrev];
			if( $ns === null ) {
				throw new Exception("Could not resolve $nsAbbrev namespace (used in $nsAbbrev:$shortName)");
			}
		} else {
			$ns = @$this->nsStack->namespaces[0];
		}
		return $ns.$shortName;
	}
	
	public function openTag( $n, array $attributes ) {
		$this->nsStack = new TOGoS_XMLRDFParser_XML_NSStackEntry( $this->nsStack );
		foreach( $attributes as $k=>$v ) {
			if( $k == 'xmlns' ) {
				$this->nsStack->namespaces[0] = $v;
			} else if( preg_match( '#^xmlns:(.+)$#', $k, $bif ) ) {
				$this->nsStack->namespaces[$bif[1]] = $v;
			}
		}
		
		$nsName = $this->namespacify($n);
		$nsAttrs = array();
		foreach( $attributes as $k=>$v ) {
			if( !preg_match('#^xmlns(:|$)#',$k) ) {
				$nsAttrs[$this->namespacify($k)] = $v;
			}
		}
		
		$this->next->openTag( $nsName, $nsAttrs );
	}
	
	public function closeTag( $n ) {
		$this->next->closeTag( $this->namespacify($n) );
		$this->nsStack = $this->nsStack->parent;
	}
}
