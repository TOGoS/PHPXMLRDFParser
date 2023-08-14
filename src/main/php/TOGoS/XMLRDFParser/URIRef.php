<?php

/**
 * May be implemented by any object representing a resource
 * that has a URI.
 */
interface TOGoS_XMLRDFParser_URIRef
{
	/**
	 * @return a URI that this resource corresponds to; may be null.
	 */
	public function getUri();
}
