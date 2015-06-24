<?php

class TOGoS_XMLRDFParser_RDF_RDFObjectConstructor
	implements TOGoS_XMLRDFParser_RDF_ObjectConstructor
{
	public function createObject( $className, $uri=null ) {
		return new TOGoS_XMLRDFParser_RDF_RDFObject( $className, array(), $uri );
	}
	public function addProperty( $subject, $propName, $value ) {
		$subject->addProperty( $propName, $value );
	}
	public function closeObject( $obj ) {
		return $obj;
	}
	public function resolveResource( $uri ) {
		return new TOGoS_XMLRDFParser_RDF_URIRef($uri);
	}
}
