<?php

class TOGoS_XMLRDFParser_XML_XMLEmitterTest
extends TOGoS_SimplerTest_TestCase
{
	protected $output = '';
	public function appendToOutput($text) {
		$this->output .= $text;
	}
	public function setUp() {
		$this->output = '';
	}
	public function testEmitText() {
		$m = new TOGoS_XMLRDFParser_XML_XMLEmitter(array($this, 'appendToOutput'));
		$m->text("Foo & Bar are \"<friends>\"");
		$this->assertEquals("Foo &amp; Bar are \"&lt;friends&gt;\"", $this->output);
	}
	public function testEmitSelfClosingTag() {
		$m = new TOGoS_XMLRDFParser_XML_XMLEmitter(array($this, 'appendToOutput'));
		$m->openTag('shindig', array('color'=>'"quite" <blue>'));
		$m->closeTag('shindig');
		$this->assertEquals('<shindig color="&quot;quite&quot; &lt;blue&gt;" />', $this->output);
	}
	public function testEmitSelfTagWithText() {
		$m = new TOGoS_XMLRDFParser_XML_XMLEmitter(array($this, 'appendToOutput'));
		$m->openTag('shindig', array('color'=>'"quite" <blue>'));
		$m->text("Poop dog");
		$m->closeTag('shindig');
		$this->assertEquals('<shindig color="&quot;quite&quot; &lt;blue&gt;">Poop dog</shindig>', $this->output);
	}
	public function testEmitNextedTag() {
		$m = new TOGoS_XMLRDFParser_XML_XMLEmitter(array($this, 'appendToOutput'));
		$m->openTag('shindig', array('color'=>'"quite" <blue>'));
		$m->openTag('gratification', array());
		$m->closeTag('gratification');
		$m->closeTag('shindig');
		$this->assertEquals('<shindig color="&quot;quite&quot; &lt;blue&gt;"><gratification /></shindig>', $this->output);
	}
}
