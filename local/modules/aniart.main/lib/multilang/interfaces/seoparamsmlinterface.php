<?php


namespace Aniart\Main\Multilang\Interfaces;


use Aniart\Main\Interfaces\SeoParamsInterface;

interface SeoParamsMLInterface extends SeoParamsInterface
{
    public function setSeoLang($lang);
    public function getSeoLang();
}