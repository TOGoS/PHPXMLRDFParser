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
		if( $this->indentLevel == 0 ) {
			$this->next->text($text);
		} else {
			$this->bufferedText .= $text;
		}
	}
	protected function fixText($text) {
		// TODO: Strip leading and trailing blank lines,
		// remove as much leading indentation as can be
		// removed from all non-blank lines
		return preg_replace('/\n[ \t]+/', "\n", trim($text));
	}
	public function openTag( $name, array $attributes ) {
		$indent = str_repeat("\t",$this->indentLevel);
		if( $this->indentLevel > 0 ) {
			$this->next->text("\n".$indent);
		}
		$this->bufferedText = $this->fixText($this->bufferedText);
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
		$this->bufferedText = $this->fixText($this->bufferedText);
		if( strlen($this->bufferedText) > 0 ) {
			if( $this->state == 'tag' && strpos($this->bufferedText,"\n") === false ) {
				$this->next->text($this->bufferedText);
			} else {
				$indent = str_repeat("\t",$this->indentLevel);
				$this->next->text(
					"\n".$indent.
					str_replace("\n","\n".$indent,$this->bufferedText)
				);
				$this->state = 'text';
			}
			$this->bufferedText = '';
		}
		--$this->indentLevel;
		if( $this->state == 'text' ) {
			$this->next->text("\n".str_repeat("\t",$this->indentLevel));
		}
		$this->next->closeTag($name);
		$this->state = 'text';
	}
}
