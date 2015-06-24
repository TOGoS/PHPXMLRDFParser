<?php

interface TOGoS_XMLRDFParser_RDF_ObjectConstructor
{
	public function createObject( $className );
	public function addProperty( $subject, $propName, $value );
	public function closeObject( $obj );
	public function resolveResource( $uri );
}
