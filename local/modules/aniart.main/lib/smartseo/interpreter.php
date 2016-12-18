<?php 

namespace Aniart\Main\SmartSeo;

use Aniart\Main\Ext\CIBlockExt;

class Interpreter implements Interfaces\InterpreterInterface
{
    /**
     * @var Page
     */
	protected $page;
	protected $variablesValues = array();
	
	public function setPage(Page $page){
		$this->page = $page;
	}
	
	public function getPage(){
		return $this->page;
	}
	
	public function interpret(Expressions\AbstractExpression $expression)
	{
		$result = '';
		if($expression instanceof Expressions\FieldExpression){
			$search = $replace = array();
			foreach($expression->getChildren() as $expr){
				$search[] = $expr->getPattern();
				$replace[] = $this->interpret($expr);
			}
			$result = str_replace($search, $replace, $expression->getPattern());
			$result = $this->format($result);
		}
		elseif($expression instanceof Expressions\PhraseExpression){
			$search = $replace = array();
			foreach($expression->getChildren() as $expr){
				$search[] = $expr->getPattern();
				$replace[] = $this->interpret($expr);
			}
			$value = str_replace($search, $replace, $expression->getPattern());
			$result = trim($value, '{}');
		}
		elseif($expression instanceof Expressions\VariableExpression){
			$vars = &$this->getPage()->getVars();
			$result = (string)$vars[$expression->getName()];
		}
		elseif($expression instanceof Expressions\FunctionExpression){
			$function = '__'.$expression->getName();
			if(method_exists($this, $function)){
				$result = call_user_func_array(array($this, $function), $expression->getArguments());
			}
		}
		else{
			throw new \DomainException('Expression "'.get_class($expression).'" is not allow');
		}
		
		return $result;
	}

	protected function format($str)
	{
		$str = trim($str);
		if(!empty($str)){
			$str = preg_replace('/\s{2,}/', ' ', $str);
			$str = str_replace(array(' . ', ' , '), array('. ', ', '), $str);
		}
		return mb_ucfirst($str);
	}
	
	protected function getVar($varName)
	{
		$vars = &$this->getPage()->getVars();
		if(!isset($vars[$varName])){
			$vars = &$this->getPage()->getUriVars();
		}
		return (string)$vars[$varName];
	}
}