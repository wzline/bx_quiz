<?php 

namespace Aniart\Main\SmartSeo\Interfaces;

use Aniart\Main\SmartSeo\Expressions\AbstractExpression;
use Aniart\Main\SmartSeo\Page;

interface InterpreterInterface
{
	public function setPage(Page $page);
	public function interpret(AbstractExpression $expression);
}
?>