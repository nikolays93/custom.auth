<?php
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var customOrderComponent $component */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$APPLICATION->IncludeComponent(
    "bitrix:system.auth.form",
    "",
    array(
        "FORGOT_PASSWORD_URL" => PATH_TO_FORGOT_PASSWORD,
        "PROFILE_URL" => PATH_TO_PROFILE,
        "REGISTER_URL" => PATH_TO_REGISTER,
        "SHOW_ERRORS" => "Y",
    ),
    $component
);
