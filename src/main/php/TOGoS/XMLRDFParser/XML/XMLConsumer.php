<?php

interface TOGoS_XMLRDFParser_XML_XMLConsumer
{
	public function text( $text );
	public function openTag( $name, array $attributes );
	public function closeTag( $name );
	/** Destruct the object when there will be no more input;
	 * __destruct is a chance to flush anything buffered */
	//public function __destruct();
}
