<?php

class TOGoS_XMLRDFParser_RDF_Namespaces
{
	const RDF_NS         = 'http://www.w3.org/1999/02/';
	const RDF_TYPE       = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type';
	const RDF_PARSETYPE  = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#parseType';
	const RDF_ABOUT      = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#about';
	const RDF_RESOURCE   = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#resource';
	const RDF_DESCRIPTION= 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Description';
	const RDF_ID         = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#ID';
	const RDF_NODEID     = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#nodeID';
	
	//// Some fake ones!
	
	//<Object rdf:about="yaddah"> means this object hasUri "yaddah"
	//const RDF_URI        = 'http://ns.nuke24.net/RDF/hasUri';
	// Technically we're supposed to represent collections as a linked list node
	// with a 'first' and a 'rest' property.  That's cumbersome, so I'll just give them items.
	const RDF_ITEM       = 'http://ns.nuke24.net/RDF/hasItem';
	// Similarly, it doesn't indicate a class for collections, so here's one:
	const RDF_COLLECTION = 'http://ns.nuke24.net/RDF/Collection';
	// Type for nodes that just have a data value
	const RDF_DATA       = 'http://ns.nuke24.net/RDF/Data';
	
	public static function stripAnyNs( $name ) {
		if( preg_match( '<([^#/]+)$>', $name, $bif ) ) {
			return $bif[1];
		}
	}
}
