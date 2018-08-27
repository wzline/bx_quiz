<?php 

namespace Aniart\Main\SmartSeo\Interfaces;

interface PagesRepositoryInterface
{
	public function getByCurrentUri();
	public function getByUri($uri);
}