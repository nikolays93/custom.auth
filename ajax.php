<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;

$request = Main\Application::getInstance()->getContext()->getRequest();
$request->addFilter(new Main\Web\PostDecodeFilter);

$action = $request->get("action");

$APPLICATION->IncludeComponent(
    "nikolays93:custom.auth",
    ".default",
    array("IS_AJAX" => "Y"),
    false
);
