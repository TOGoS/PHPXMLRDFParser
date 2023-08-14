<?php

class TOGoS_XMLRDFParser_XML_BufferedTextTransformerTest
extends TOGoS_SimplerTest_TestCase
{
	protected $output;
	public function __invoke($text) {
		$this->output .= $text;
	}

	public function setUp() {
		$this->output = '';
	}
	
	public function testBufferAndTransform() {
		$xmlEmitter = new TOGoS_XMLRDFParser_XML_XMLEmitter($this);
		$textTrimmer = new TOGoS_XMLRDFParser_XML_BufferedTextTransformer(function($text) {
			$text = trim($text);
			return strlen($text) == 0 ? '' : "[".trim($text)."]";
		}, $xmlEmitter);
		$textTrimmer->text("Funky ");
		$textTrimmer->text("fridge");
		$textTrimmer->openTag("microwave", array("buttonCount"=>42));
		$textTrimmer->text("  ");
		$textTrimmer->text("toast");
		$textTrimmer->text("  ");
		$textTrimmer->text("jelly");
		$textTrimmer->text("  ");
		$textTrimmer->openTag("sandwich", array());
		$textTrimmer->text("  ");
		$textTrimmer->closeTag("sandwich");
		$textTrimmer->text("\n");
		$textTrimmer->closeTag("microwave");
		$textTrimmer->text(" Samuel the grate");
		unset($textTrimmer);

		$this->assertEquals("[Funky fridge]<microwave buttonCount=\"42\">[toast  jelly]<sandwich /></microwave>[Samuel the grate]", $this->output);
	}
}
