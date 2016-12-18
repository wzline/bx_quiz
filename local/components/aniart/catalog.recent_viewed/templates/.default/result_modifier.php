<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

//STUBS
$productRepository = app()->make('ProductsRepository'); //same as app('ProductsRepository')
$arResult['ELEMENTS'] = $productRepository->getItemsByIds($someIds = []);