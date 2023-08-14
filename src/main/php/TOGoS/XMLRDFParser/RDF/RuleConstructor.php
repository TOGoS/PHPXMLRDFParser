<?php

class TOGoS_XMLRDFParser_RDF_RuleConstructor
	implements TOGoS_XMLRDFParser_RDF_ObjectConstructor
{
	public $ruleLoader;
	
	public function createObject( $className ) {
		$shortClassName = TOGoS_XMLRDFParser_RDF_Namespaces::stripAnyNs($className);
		
		// Check for a few hard-coded types:
		switch( $shortClassName ) {
		case('True'): return new TOGoS_XMLRDFParser_Expr_LiteralExpression(true);
		case('False'): return new TOGoS_XMLRDFParser_Expr_LiteralExpression(false);
		case('Null'): return new TOGoS_XMLRDFParser_Expr_LiteralExpression(null);
		case('Rule'): return new TOGoS_XMLRDFParser_Rule;
		
		case('ActionOption'): return new TOGoS_XMLRDFParser_Expr_SimplePHPObjectConstructor('TOGoS_XMLRDFParser_ActionOption');
		case('AdvisoryOption'): return new TOGoS_XMLRDFParser_Expr_SimplePHPObjectConstructor('TOGoS_XMLRDFParser_AdvisoryOption');
		}
		
		// Then check for an existing expression class:
		$exprClassName = 'TOGoS_XMLRDFParser_Expr_'.$shortClassName.'Expression';
		if( class_exists($exprClassName,true) ) return new $exprClassName;
		
		if( ($expr = $this->ruleLoader->getExpression($shortClassName)) !== null ) {
			return $expr;
		}
		
		// If not found...
		throw new Exception("Don't know how to create object for class: $className");
	}
	
	public function addProperty( $subject, $propName, $value ) {
		if( !is_object($subject) ) {
			throw new Exception( "Can't set property ('$propName') on non-object (type: ".gettype($subject)."): ".$subject );
		}
		$subject->addProperty( $propName, $value );
	}
	
	public function closeObject( $obj ) {
		if( $obj instanceof TOGoS_XMLRDFParser_Expression ) {
			$obj->initHash();
		}
		return $obj;
	}
	
	public function resolveResource( $uri ) {
		if( preg_match('/^x-var-exp:(.*)$/', $uri, $bif) ) {
			return new TOGoS_XMLRDFParser_Expr_VariableValueExpression( $bif[1] );
		} else {
			return null;
		}
	}
}
