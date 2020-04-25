<?php
/**
 * @param array $arParams
 * @param array $arResult
 * @param CBitrixComponentTemplate $this
 * @global CUser $USER
 * @global CMain $APPLICATION
 */

if ( ! defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

?>
<div class="custom-auth">
    <form name="custom-register-form" method="post" action="<?= POST_FORM_ACTION_URI ?>" enctype="multipart/form-data">
        <input type="hidden" name="register_submit_button" value="1">
        <? if ($arResult["BACKURL"] <> '') {
            echo '<input type="hidden" name="backurl" value="' . $arResult["BACKURL"] . '" />';
        } ?>

        <div class="custom-auth__errors" data-entity="error-messages">
            <?php
            if (count($arResult["ERRORS"]) > 0) {
                foreach ($arResult["ERRORS"] as $key => $error) {
                    if (intval($key) == 0 && $key !== 0) {
                        $arResult["ERRORS"][$key] = str_replace("#FIELD_NAME#",
                            "&quot;" . GetMessage("REGISTER_FIELD_" . $key) . "&quot;", $error);
                    }
                }

                ShowError(implode("<br />", $arResult["ERRORS"]));
            } elseif ($arResult["USE_EMAIL_CONFIRMATION"] === "Y") {
                echo '<p>' . GetMessage("REGISTER_EMAIL_WILL_BE_SENT") . '</p>';
            }
            ?>
        </div>

        <?php
        foreach ($arResult["SHOW_FIELDS"] as $FIELD) {
            switch ($FIELD) {
                case 'LOGIN':
                    ?>
                    <div class="form-group form-name">
                        <label>Введите ваш новый логин</label>
                        <input type="text" placeholder="Логин" name="REGISTER[LOGIN]"
                            value="<?= $arResult["VALUES"]["LOGIN"] ?>" class="form-control" autocomplete="login" />
                    </div>
                    <?php
                    break;

                case 'NAME':
                    ?>
                    <div class="form-group form-name">
                        <label>Введите ваше имя</label>
                        <input type="text" placeholder="Ф.И.О." name="REGISTER[NAME]"
                            value="<?= $arResult["VALUES"]["NAME"] ?>" class="form-control" autocomplete="name" />
                    </div>
                    <?php
                    break;

                case 'PERSONAL_PHONE':
                    ?>
                    <div class="form-group form-personal_phone">
                        <label>Введите ваш номер телефона</label>
                        <input type="tel" placeholder="Номер телефона" name="REGISTER[PERSONAL_PHONE]"
                            value="<?= $arResult["VALUES"]["PERSONAL_PHONE"] ?>" class="form-control" autocomplete="tel" />
                    </div>
                    <?php
                    break;

                case 'EMAIL':
                    ?>
                    <div class="form-group form-email">
                        <label>Введите ваш Email</label>
                        <input type="text" placeholder="Электронная почта" name="REGISTER[EMAIL]"
                            value="<?= $arResult["VALUES"]["EMAIL"] ?>" class="form-control" autocomplete="email" required="true" />
                    </div>
                    <?php
                    break;

                case 'PASSWORD':
                    ?>
                    <div class="form-group form-email">
                        <label>Введите ваш новый пароль</label>
                        <input type="password" placeholder="Пароль" name="REGISTER[PASSWORD]"
                            value="<?= $arResult["VALUES"]["PASSWORD"] ?>" class="form-control" autocomplete="off" />
                    </div>
                    <?php
                    break;

                case 'CONFIRM_PASSWORD':
                    ?>
                    <input type="hidden" name="REGISTER[CONFIRM_PASSWORD]"
                        value="<?= $arResult["VALUES"][$FIELD] ?>" class="form-control" autocomplete="off" />
                    <script>
                        jQuery(document).ready(function($) {
                            var $CONFIRM_PASSWORD = $('[name="REGISTER[CONFIRM_PASSWORD]"]');
                            $('[name="REGISTER[PASSWORD]"]').on('keyup', function () {
                                $CONFIRM_PASSWORD.val($(this).val());
                            });
                        });
                    </script>
                    <?php
                    break;

                default:
                    ?>
                    <div class="form-group">
                        <input type="text" class="form-control" name="REGISTER[<?= $FIELD ?>]"
                            value="<?= $arResult["VALUES"][$FIELD] ?>" />
                    </div>
                    <?php
                    break;
            }
        }

        ?>
        <div class="form-group">
        <? if ($arResult["USE_CAPTCHA"] == "Y") {
            ?>
            <input type="hidden" name="captcha_sid" value="<?= $arResult["CAPTCHA_CODE"] ?>"/>
            <img src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult["CAPTCHA_CODE"] ?>" width="180"
                 height="40" alt="CAPTCHA"/>
            <?= GetMessage("REGISTER_CAPTCHA_PROMT") ?>:<span class="starrequired">*</span>
            <input type="text" name="captcha_word" maxlength="50" value=""/>
            <?
        } ?>
        </div>

        <div class="form-helpers">
            <div class="form-check">
                <label class="form-check-label">
                    <input class="form-check-input" type="checkbox" name="privacy_accept" value="Y">
                    <span class="form-check-label">Нажимая кнопку "Зарегистрироваться", я подтверждаю, что я ознакомился с <a
                                href="#" data-fancybox="" data-type="ajax" data-src="/auth/?action=privacy"
                                href="javascript:;">политикой обработки персональных данных</a> и даю согласие на обработку мои персональных данных</span>
                </label>
            </div>
        </div>

        <div class="submit-wrap mt-2 mb-2">
            <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
        </div>

        <?php /*
        if ($arResult["AUTH_SERVICES"]):?>
            <div class="social-login social-register">
                <span>Зарегистрироваться с помощью:</span>
                <? $APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "flat",
                    array(
                        "AUTH_SERVICES" => $arResult["AUTH_SERVICES"],
                        "SUFFIX" => "form",
                    ),
                    $component->__parent,
                    array("HIDE_ICONS" => "Y")
                ); ?>
            </div>
        <? endif */?>
    </form>

    <!-- <a class="login" href="/auth/?login=yes" data-fancybox="" data-type="ajax" data-src="/auth/?action=getForm"
       rel="nofollow">Уже зарегистрированы? Войти</a> -->
</div>
