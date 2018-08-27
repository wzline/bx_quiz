<?
use Bitrix\Main\Loader;
$modulePath = dirname(__FILE__);
include $modulePath.'/events.php';

Loader::includeModule('highloadblock');

$arClasses = array(
	'\Aniart\Main\Observers\BitrixObserver' => 'lib/observers/bitrixobserver.php',
	'\Aniart\Main\Seo\SeoParamsCollector' => 'lib/seo/seoparamscollector.php',
	'\Aniart\Main\SmartSeo\SmartSeo' => 'lib/smartseo/smartseo.php',
	'\Aniart\Main\SmartSeo\Repositories\HLBlockPagesRepository' => 'lib/smartseo/repositories/hlblockpagesrepository.php',
	'\Aniart\Main\SmartSeo\Repositories\AbstractPagesRepository' => 'lib/smartseo/repositories/abstractpagesrepository.php',
	'\Aniart\Main\SmartSeo\Page' => 'lib/smartseo/page.php',
	'\Aniart\Main\SmartSeo\Parser' => 'lib/smartseo/parser.php',
	'\Aniart\Main\SmartSeo\Interpreter' => 'lib/smartseo/interpreter.php',
		
	'\Aniart\Main\SmartSeo\Interfaces\PagesRepositoryInterface' => 'lib/smartseo/interfaces/pagesrepositoryinterface.php',
	'\Aniart\Main\SmartSeo\Interfaces\ParserInterface' => 'lib/smartseo/interfaces/parserinterface.php',
	'\Aniart\Main\SmartSeo\Interfaces\InterpretedInterface' => 'lib/smartseo/interfaces/interpretedinterface.php',
	'\Aniart\Main\SmartSeo\Interfaces\InterpreterInterface' => 'lib/smartseo/interfaces/interpreterinterface.php',

	'\Aniart\Main\SmartSeo\Expressions\FieldExpression' => 'lib/smartseo/expressions/fieldexpression.php',
	'\Aniart\Main\SmartSeo\Expressions\NonTerminalExpression' => 'lib/smartseo/expressions/nonterminalexpression.php',
	'\Aniart\Main\SmartSeo\Expressions\AbstractExpression' => 'lib/smartseo/expressions/abstractexpression.php',
);

\Bitrix\Main\Loader::registerAutoLoadClasses("aniart.seo", $arClasses);

app()->singleton([
	'SeoParamsCollector' => '\Aniart\Main\Seo\SeoParamsCollector',
	'SeoPagesRepository' => function(\Aniart\Main\ServiceLocator $locator){
		return new Aniart\Main\Repositories\SeoPagesRepository(HL_SEO_PAGES_ID);
	},
	'SmartSeo' => function(\Aniart\Main\ServiceLocator $locator) {
		$smartSeo = \Aniart\Main\SmartSeo\SmartSeo::getInstance();
		try {
			$smartSeo->init(new \Aniart\Main\SmartSeo\Repositories\HLBlockPagesRepository());
			return $smartSeo;
		} catch (Exception $e) {
		}
	},
]);