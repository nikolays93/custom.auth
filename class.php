<?php
use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

global $APPLICATION;

if ( ! defined('PATH_TO_PROFILE')) {
    define('PATH_TO_PROFILE', '/user/');
}
if ( ! defined('PATH_TO_AUTH')) {
    define('PATH_TO_AUTH', $APPLICATION->GetCurPage());
}
if ( ! defined('PATH_TO_REGISTER')) {
    define('PATH_TO_REGISTER', $APPLICATION->GetCurPage() . '?register=yes');
}
if ( ! defined('PATH_TO_FORGOT_PASSWORD')) {
    define('PATH_TO_FORGOT_PASSWORD', $APPLICATION->GetCurPage() . '?forgot_password=yes');
}

class customAuthComponent extends CBitrixComponent
{
    public $arYes = ['yes', 'Y'];

    /**
     * Define template
     */
    public function getTemplateNameByRequest()
    {

        foreach (['forgot_password', 'change_password', 'register'] as $template) {
            if (in_array($this->request->get($template), $this->arYes, 1)) {
                return $template;
            }
        }

        return '';
    }

    function executeComponent()
    {
        global $APPLICATION, $USER;

        if (in_array($this->request->get('logout'), $this->arYes, 1)) {
            $USER->Logout();
        }

        $template = $USER->isAuthorized() ? 'authorized' : $this->getTemplateNameByRequest();
        $this->includeComponentTemplate($template);
    }
}