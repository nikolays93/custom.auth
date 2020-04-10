<?php
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Engine\Controller;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

class customAuthAjax extends Controller implements Controllerable
{
    /**
     * @return array
     */
    public function configureActions(): array
    {
        return [
            'getForm' => [
                'prefilters' => [],
            ],
            'formResult' => [
                'prefilters' => [],
            ]
        ];
    }

    public function getFormAction(): array
    {
        global $APPLICATION;

        $APPLICATION->IncludeComponent(
            "seo18:custom.auth",
            "",
            array(),
            false,
            array("HIDE_ICONS" => "Y")
        );
        $arResult['response'] = trim(ob_get_clean());

        return $arResult;
    }

    public function formResultAction(): array
    {
        global $APPLICATION;
        $arResult = [];

        ob_start();
        $APPLICATION->IncludeComponent(
            "seo18:custom.auth",
            "errors",
            array(),
            $this,
            array("HIDE_ICONS" => "Y")
        );
        $arResult['errors'] = trim(ob_get_clean());
        if(!empty($arResult['errors'])) {
            return $arResult;
        }

        ob_start();
        $APPLICATION->IncludeComponent(
            "seo18:custom.auth",
            "",
            array(),
            false,
            array("HIDE_ICONS" => "Y")
        );
        $arResult['response'] = trim(ob_get_clean());

        return $arResult;
    }
}
