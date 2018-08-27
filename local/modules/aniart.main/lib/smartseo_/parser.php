<?php 

namespace Aniart\Main\SmartSeo;

class Parser implements Interfaces\ParserInterface
{
	protected $data;
	
	public function __construct($data = null)
	{
		if(!is_null($data)){
			$this->setData($data);
		}
	}
	
	public function setData($data)
	{
		$this->data = $data;
		return $this;
	}
	
	public function parse()
	{
		if(!$this->data){
			throw new \UnexpectedValueException('Not even parse, data is null');
		}
		$matches = array();
		$expression  = new Expressions\FieldExpression($this->data);
		if($this->canParse($matches)){
			foreach($this->parsePhrase($matches) as $expr){
				$expression->addChild($expr);
			}
		}

		return $expression;
	}
	
	protected function parsePhrase(array $matches)
	{
		$result = array();
		foreach($matches[1] as $i => $match){
			$expression = new Expressions\PhraseExpression($matches[0][$i]);
			//find variables
			foreach($this->parseVars($match) as $varExpression){
				$expression->addChild($varExpression);
			}
			//find functions
			foreach($this->parseFunctions($match) as $funcExpression){
				$expression->addChild($funcExpression);
			}
			$result[] = $expression;
		}
		
		return $result;
	}
	
	protected function parseVars($str)
	{
		$result = array();
		$matches = array();
		if(preg_match_all('/\$([\w\d]+)/', $str, $matches)){
			foreach($matches[1] as $i => $var){
				$expr = new Expressions\VariableExpression($matches[0][$i]);
				$expr->setName($var);
				$result[] = $expr;
			}
		}
		return $result;
	}
	
	protected function parseFunctions($str)
	{
		$result = array();
		$matches = array();
		if(preg_match_all('/func\((.+?)\)/', $str, $matches)){
			foreach($matches[1] as $i => $funcStr){
				$funcParams = array_map(function($param){
					return trim(str_replace('\\,', ',', $param));
				}, preg_split('/(?<!\\\),/', $funcStr));
				$funcName = array_shift($funcParams);
				$expr = new Expressions\FunctionExpression($matches[0][$i]);
				$expr
					->setName($funcName)
					->setArguments($funcParams);
				$result[] = $expr;
			}
		}
		return $result;
	}
	
	public function canParse(&$matches = array())
	{
		return preg_match_all('/{{(.+?)}}/', $this->data, $matches);
	}
}