<?php

class TOGoS_XMLRDFParser_RDF_XMLRDFifier
	implements TOGoS_XMLRDFParser_XML_XMLConsumer
{
	/* States:
	 * root
	 * object, parent=..., tagname=..., subject=rdfobject
	 * prop, parent=..., tagname=... subject=rdfobject, propname=something, 'valuegiven'=>t/f
	 * collection, parent=..., tagname=..., subject=rdfobject, propname=something, collection=rdfobject (representing the collection)
	 */
	
	protected $state = array('type'=>'root');
	protected $rootObject;
	protected $namedObjects = array();
	protected $strict = false;
	
	public function __construct(array $options=array()) {
		foreach( $options as $k=>$v ) {
			switch( $k ) {
			case 'strict': $this->strict = (bool)$v; break;
			default: throw new Exception("Unrecognized option: '$k'");
			}
		}
	}
	
	protected function logUnrecognizedAttribute($name) {
		if( $this->strict ) throw new Exception("Unrecognized attribute '$name'");
	}
	
	public function text( $text ) {
		$this->provideValue( TOGoS_XMLRDFParser_RDF_RDFObject::dataValue($text) );
	}
	protected function provideValue( TOGoS_XMLRDFParser_RDF_RDFObject $obj ) {
		//echo "{$this->state['type']}: got value: ".var_export($obj,true)."\n";
		switch( $this->state['type'] ) {
		case 'root':
			$this->rootObject = $obj;
			return;
		case 'prop':
			if( $this->state['valuegiven'] and $this->strict ) {
				throw new Exception("More than one value given for property {$this->state['propname']}");
			}
			$this->state['subject']->addProperty($this->state['propname'], $obj);
			$this->state['valuegiven'] = true;
			return;
		case 'collection':
			$this->state['collection']->addItem($obj);
			return;
		default:
			throw new Exception($obj->getRdfTypeName()." value encountered while in {$this->state['type']} state.");
		}
	}
	public function openTag( $name, array $attributes ) {
		switch( $this->state['type'] ) {
		case 'root': case 'prop': case 'collection':
			$classUri = $name !== TOGoS_XMLRDFParser_RDF_Namespaces::RDF_DESCRIPTION ? $name : null;
			$nodeId = null;
			$uri = null;
			foreach( $attributes as $k=>$v ) {
				switch( $k ) {
				case TOGoS_XMLRDFParser_RDF_Namespaces::RDF_NODEID:
					$nodeId = $v;
					break;
				case TOGoS_XMLRDFParser_RDF_Namespaces::RDF_ABOUT:
					$uri = $v;
					break;
				default:
					$this->logUnrecognizedAttribute($k);
				}
			}
			$object = new TOGoS_XMLRDFParser_RDF_RDFObject($classUri, array(), $uri);
			if( $nodeId !== null ) $this->namedObjects[$nodeId] = $object;
			$this->state = array('type'=>'object', 'parent'=>$this->state, 'tagname'=>$name, 'subject'=>$object);
			break;
		case 'object':
			// Goign to parse a property!
			$uri = null;
			$parseType = 'Normal';
			foreach( $attributes as $k=>$v ) {
				switch( $k ) {
				case TOGoS_XMLRDFParser_RDF_Namespaces::RDF_PARSETYPE:
					$parseType = $v;
					break;
				case TOGoS_XMLRDFParser_RDF_Namespaces::RDF_RESOURCE:
					$uri = $v;
					break;
				default:
					$this->logUnrecognizedAttribute($k);
				}
			}
			
			switch( $parseType ) {
			case 'Normal':
				$this->state = array('type'=>'prop', 'parent'=>$this->state, 'tagname'=>$name, 'subject'=>$this->state['subject'], 'propname'=>$name, 'valuegiven'=>false);
				if( $uri !== null ) {
					$this->provideValue(TOGoS_XMLRDFParser_RDF_RDFObject::ref($uri));
				}
				break;
			case 'Collection':
				// Since this tag represents both the property and the collection, it's state has to reference both, too.
				$collection = TOGoS_XMLRDFParser_RDF_RDFObject::collection();
				$objState = $this->state;
				$this->state = array('type'=>'prop', 'parent'=>$objState, 'tagname'=>$name, 'subject'=>$this->state['subject'], 'propname'=>$name, 'valuegiven'=>false);
				$this->provideValue($collection);
				$this->state = array('type'=>'collection', 'parent'=>$objState, 'tagname'=>$name, 'collection'=>$collection);
				break;
			default:
				throw new Exception("Unrecognized parseType '$parseType' on property open tag '$name'");
			}
			
			break;
		default:
			throw new Exception("Don't know how to handle open tag '$name' in {$this->state['type']} state.");
		}
	}
	public function closeTag( $name ) {
		if( $this->state['type'] === 'root' ) {
			throw new Exception("Unexpected close tag in root state");
		} else if( $name !== $this->state['tagname'] ) {
			throw new Exception("Closing tag '$name' does not match opening tag '{$this->state['tagname']}'");
		}
		
		switch( $this->state['type'] ) {
		case 'object':
			$object = $this->state['subject'];
			$this->state = $this->state['parent'];
			$this->provideValue( $object );
			return;
		case 'prop': case 'collection':
			$this->state = $this->state['parent'];
			break;
		default:
			throw new Exception("Don't know how to handle close tag '$name' in {$this->state['type']} state.");
		}
	}
	
	public function getRootObject() {
		return $this->rootObject;
	}
	
	/**
	 * Convenience method so you don't have to make your own XMLParsers, etc
	 */
	public function parse( $xml ) {
		$namespacifier = new TOGoS_XMLRDFParser_XML_XMLNamespacifier( $this );
		$xmlparser = new TOGoS_XMLRDFParser_XML_XMLParser( $namespacifier );
		$xmlparser->parse( $xml );
		return $this->getRootObject();
	}
}
