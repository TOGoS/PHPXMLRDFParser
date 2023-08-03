<?php

class TOGoS_XMLRDFParser_XML_XMLPrettifier
implements TOGoS_XMLRDFParser_XML_XMLConsumer
{
	protected $indentLevel = 0;
	protected $next;
	protected $state = 'text';
	protected $bufferedText = '';
	public function __construct(TOGoS_XMLRDFParser_XML_XMLConsumer $next) {
		$this->next = $next;
	}
	public function text( $text ) {
		$this->bufferedText .= $text;
	}
	public function openTag( $name, array $attributes ) {
		$indent = str_repeat("\t",$this->indentLevel);
		if( $this->state == 'tag' ) {
			$this->next->text("\n".$indent);
		}
		if( strlen($this->bufferedText) > 0 ) {
			$this->next->text(
				str_replace("\n","\n".$indent,$this->bufferedText).
				"\n".$indent);
		}
		$this->bufferedText = '';
		$this->next->openTag($name, $attributes);
		++$this->indentLevel;
		$this->state = 'tag';
	}
	public function closeTag( $name ) {
		if( strlen($this->bufferedText) > 0 ) {
			if( $this->state == 'tag' && strpos($this->bufferedText,"\n") === false ) {
				$this->next->text($this->bufferedText);
			} else {
				$indent = str_repeat("\t",$this->indentLevel);
				$this->next->text(
					"\n".$indent.
					str_replace("\n","\n".$indent,$this->bufferedText)
				);
				$this->bufferedText = '';
				$this->state = 'text';
			}
		}
		--$this->indentLevel;
		if( $this->state == 'text' ) {
			$this->next->text("\n".str_repeat("\t",$this->indentLevel));
		}
		$this->next->closeTag($name);
		$this->state = 'text';
	}
}
