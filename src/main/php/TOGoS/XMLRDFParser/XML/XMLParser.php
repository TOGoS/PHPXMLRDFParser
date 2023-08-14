<?php

/*
 * Simple, low-level XML parser.
 * Breaks souce into a list of text and tags with no structure.
 * Does NOT validate in any way (behavior when parsing malformed XML
 * is undefined), structure nested tags, or translate prefixes to namespaces.
 * DOES decode entities in text.
 */
class TOGoS_XMLRDFParser_XML_XMLParser
{
	protected $xmlConsumer;
	
	protected function decodeText( $text ) {
		return html_entity_decode( $text, ENT_QUOTES, 'UTF-8' );
	}
	
	public function __construct( TOGoS_XMLRDFParser_XML_XMLConsumer $xmlConsumer ) {
		$this->xmlConsumer = $xmlConsumer;
	}
	
	/**
	 * @param string $xml XML source to parse
	 */
	public function parse( $xml ) {
		$c = $this->xmlConsumer;
		
		$tokens = preg_match_all('#<!--.*?-->|<[^>]+>|[^<]+#s',$xml,$matches,PREG_SET_ORDER);
		foreach( $matches as $m ) {
			if( preg_match('#^<!--#',$m[0]) ) {
				// Comment; ignore!
			} else if( preg_match('#^<(/?)([^\s/>]+)\s?(.*?)(/?)>$#s',$m[0],$bif) ) {
				$name = $bif[2];
				$isOpening = $bif[1] == '';
				$isClosing = ($bif[1] == '/' || $bif[4] == '/');
				$attributes = array();
				preg_match_all('#([^\s="]+)="([^"]*)"#',$bif[3],$attrMatches,PREG_SET_ORDER);
				foreach( $attrMatches as $am ) {
					$attributes[$am[1]] = $this->decodeText($am[2]);
				}
				if( $isOpening ) {
					$c->openTag( $name, $attributes );
				}
				if( $isClosing ) {
					$c->closeTag( $name );
				}
			} else {
				$rawText = $m[0];
				if( $rawText != '' ) {
					$c->text( $this->decodeText($rawText) );
				}
			}
		}
	}
}
