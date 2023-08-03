<?php

class TOGoS_XMLRDFParser_XML_XMLPrettifierTest
extends TOGoS_SimplerTest_TestCase
{
	protected $output = '';
	protected $p;
	public function appendToOutput($text) {
		$this->output .= $text;
	}
	public function setUp() {
		$this->output = '';
		$m = new TOGoS_XMLRDFParser_XML_XMLEmitter(array($this, 'appendToOutput'));
		$this->p = new TOGoS_XMLRDFParser_XML_XMLPrettifier($m);
	}
	public function testEmitNestedTag() {
		$this->p->openTag('outer', array());
		$this->p->openTag('inner', array());
		$this->p->closeTag('inner');
		$this->p->closeTag('outer');
		$this->assertEquals("<outer>\n\t<inner />\n</outer>", $this->output);
	}
	public function testEmitNestedShortText() {
		$this->p->openTag('outer', array());
		$this->p->text("Hi there!");
		$this->p->closeTag('outer');
		$this->assertEquals("<outer>Hi there!</outer>", $this->output);
	}
	public function testEmitNestedTagAndText() {
		$this->p->openTag('outer', array());
		$this->p->openTag('inner', array());
		$this->p->closeTag('inner');
		$this->p->text("Hi there!");
		$this->p->closeTag('outer');
		$this->assertEquals("<outer>\n\t<inner />\n\tHi there!\n</outer>", $this->output);
	}
	public function testEmitNestedTextAndTag() {
		$this->p->openTag('outer', array());
		$this->p->text("Hi there!");
		$this->p->openTag('inner', array());
		$this->p->closeTag('inner');
		$this->p->closeTag('outer');
		$this->assertEquals("<outer>\n\tHi there!\n\t<inner />\n</outer>", $this->output);
	}
	public function testEmitNestedTextAndTagAndText() {
		$this->p->openTag('outer', array());
		$this->p->text("Sup\nhomies!");
		$this->p->openTag('inner', array());
		$this->p->closeTag('inner');
		$this->p->text("Hi there!");
		$this->p->closeTag('outer');
		$this->assertEquals("<outer>\n\tSup\n\thomies!\n\t<inner />\n\tHi there!\n</outer>", $this->output);
	}
}
