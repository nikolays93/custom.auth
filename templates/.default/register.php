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

$APPLICATION->IncludeComponent("bitrix:main.register", ".default", Array(
    "COMPONENT_TEMPLATE" => ".default",
    "SHOW_FIELDS" => array( // Поля, которые показывать в форме
        0 => "NAME",
        1 => "LOGIN",
        2 => "EMAIL",
        3 => "PERSONAL_PHONE",
        4 => "PASSWORD",
        5 => "CONFIRM_PASSWORD",
    ),
    "REQUIRED_FIELDS" => "", // Поля, обязательные для заполнения
    "AUTH" => "Y",  // Автоматически авторизовать пользователей
    "USE_BACKURL" => "Y", // Отправлять пользователя по обратной ссылке, если она есть
    "SUCCESS_PAGE" => "?register=success", // Страница окончания регистрации
    "SET_TITLE" => "N", // Устанавливать заголовок страницы
    "USER_PROPERTY" => "", // Показывать доп. свойства
    "USER_PROPERTY_NAME" => "", // Название блока пользовательских свойств
),
    $component
);