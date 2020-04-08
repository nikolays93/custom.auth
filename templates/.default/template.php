<? if ( ! defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

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

if ($USER->isAuthorized()) {
    echo '<p>';
    echo 'Вы зарегистрированы и успешно авторизовались.';

    if ( ! empty($arResult['REDIRECT_URL'])) {
        echo ' Через несколько секунд вы буете перенаправленны на стрианцу профиля';
        // echo '<script>setTimeout(function() { window.location.href = "'.$arResult['REDIRECT_URL'].'"; }, 4000);</script>';
    }
    echo '</p>';
    ?>
    <p>
        <a href="/">Вернуться на главную страницу</a>
        | <a href="<?= PATH_TO_PROFILE; ?>">Просмотреть свой профиль</a>
        | <a href="<?= PATH_TO_AUTH; ?>?logout=yes">Выйти</a>
    </p>
    <?
} else {
    $APPLICATION->IncludeComponent(
        "bitrix:system.auth.form",
        "", // $arParams['IS_AJAX'] ? "ajax" : ".default",
        array(
            "FORGOT_PASSWORD_URL" => '/auth/',
            "PROFILE_URL" => "/user/",
            "REGISTER_URL" => '/auth/',
            "SHOW_ERRORS" => "Y",
            "IS_AJAX" => ! empty($arParams['IS_AJAX']) ? $arParams['IS_AJAX'] : 'N',
            "COMPONENT_TEMPLATE" => ".default",
        ),
        $component
    );
}