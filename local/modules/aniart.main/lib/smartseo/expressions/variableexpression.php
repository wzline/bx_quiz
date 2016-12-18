<?php 
namespace Aniart\Main\SmartSeo\Expressions;

use Aniart\Main\SmartSeo;

class VariableExpression extends TerminalExpression
{
	protected  $value;
	
	public function setValue($value)
	{
		$this->value = $value;
	}
	
	public function getValue()
	{
		return $this->value;
	}
}