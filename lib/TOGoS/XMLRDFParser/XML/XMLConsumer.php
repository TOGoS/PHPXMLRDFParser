<?php

interface TOGoS_XMLRDFParser_XML_XMLConsumer
{
	public function text( $text );
	public function openTag( $name, array $attributes );
	public function closeTag( $name );
}
