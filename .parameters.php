<?php
/**
 * @global array $arCurrentValues
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$arComponentParameters = array(
    "GROUPS" => array(
        'BASE',
    ),
    "PARAMETERS" => array(
        "PRIVACY_PAGE" => array(
            "PARENT" => "BASE",
            "NAME" => "Политика конфиденциальности",
            "TYPE" => "STRING",
        ),
        "PERSONAL_PAGE" => array(
            "PARENT" => "BASE",
            "NAME" => "Страница с условиями обработки персональных данных",
            "TYPE" => "STRING",
        ),
    ),
);
