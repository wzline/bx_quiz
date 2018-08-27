<?php 

namespace Aniart\Main\SmartSeo\Interfaces;

interface ParserInterface
{
	public function __construct($data = null);
	public function setData($data);
	public function parse();
}