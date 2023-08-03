<?php

class TOGoS_XMLRDFParser_XML_XMLEmitter
implements TOGoS_XMLRDFParser_XML_XMLConsumer
{
	protected $outputFunction;
	protected $state = 'text';

	protected static $escapeSequences = array(
		'&' => '&amp;',
		'<' => '&lt;',
		'>' => '&gt;',
		'"' => '&quot;',
	);
	protected static $textSpecialChars = array('&','<','>');
	protected static $quotedSpecialChars = array('&','<','>','"');

	protected static function mkEscapeFunction($specialChars, $escapeMap) {
		$search = array();
		$replacements = array();
		foreach( $specialChars as $c ) {
			$search[] = $c;
			$replacements[] = $escapeMap[$c];
		}
		return function($str) use ($search,$replacements) {
			return str_replace($search, $replacements, $str);
		};
	}

	protected $textEscapeFunction;
	protected $quotedEscapeFunction;
	
	public function __construct($outputFunction) {
		$this->outputFunction = $outputFunction;
		$this->textEscapeFunction = self::mkEscapeFunction(self::$textSpecialChars, self::$escapeSequences);
		$this->quotedEscapeFunction = self::mkEscapeFunction(self::$quotedSpecialChars, self::$escapeSequences);
	}

	protected function scat() {
		if( $this->state == 'tag' ) {
			call_user_func($this->outputFunction, '>');
			$this->state = 'text';
		}
	}
	
	public function text( $text ) {
		$this->scat();
		call_user_func($this->outputFunction, call_user_func($this->textEscapeFunction, $text));
	}
	public function openTag( $name, array $attributes ) {
		$this->scat();
		call_user_func($this->outputFunction, "<".$name);
		foreach( $attributes as $k=>$v ) {
			call_user_func($this->outputFunction, " {$k}=\"".call_user_func($this->quotedEscapeFunction, $v)."\"");
		}
		$this->state = 'tag';
	}
	public function closeTag( $name ) {
		if( $this->state == 'tag' ) {
			call_user_func($this->outputFunction, " />");
			$this->state = 'text';
		} else {
			call_user_func($this->outputFunction, "</$name>");
		}
	}
}
