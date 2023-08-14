<?php

class TOGoS_XMLRDFParser_RDF_URIRef implements TOGoS_XMLRDFParser_URIRef {
	protected $uri;
	public function __construct( $uri ) {  $this->uri = $uri;  }
	public function getUri() {  return $this->uri;  }
}
