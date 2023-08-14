<?php

class TOGoS_XMLRDFParser_XML_BufferedTextTransformer
implements TOGoS_XMLRDFParser_XML_XMLConsumer
{
	protected $transform;
	protected $next;
	protected $bufferedText = '';
	
	public function __construct($transform, TOGoS_XMLRDFParser_XML_XMLConsumer $next) {
		$this->transform = $transform;
		$this->next = $next;
	}
	public function text( $text ) {
		$this->bufferedText .= $text;
	}
	protected function flushAnyBufferedText() {
		if( strlen($this->bufferedText) == 0 ) return;
		
		$text = call_user_func($this->transform, $this->bufferedText);
		$this->bufferedText = '';
		if( strlen($text) > 0 ) {
			$this->next->text($text);
		}
	}
	public function openTag( $name, array $attributes ) {
		$this->flushAnyBufferedText();
		$this->next->openTag($name, $attributes);
	}
	public function closeTag( $name ) {
		$this->flushAnyBufferedText();
		$this->next->closeTag($name);
	}
	public function __destruct() {
		$this->flushAnyBufferedText();
	}
}
