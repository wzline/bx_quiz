<?php 

namespace Aniart\Main\SmartSeo\Expressions;

use Aniart\Main\SmartSeo;

class FunctionExpression extends TerminalExpression
{
	protected $arguments;

	public function setArguments(array $arguments)
	{
		$this->arguments = $arguments;
		return $this;
	}
	
	public function getArguments()
	{
		return $this->arguments;
	}
}