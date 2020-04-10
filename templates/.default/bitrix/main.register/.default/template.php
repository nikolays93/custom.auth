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

        <? foreach (array_reverse($arResult["SHOW_FIELDS"]) as $FIELD) {
            switch ($FIELD) {
                case 'LOGIN':
                case 'NAME':
                    break;

                case 'PERSONAL_PHONE':
                    ?>
                    <div class="form-group">
                    <label>Введите ваш номер телефона</label>
                    <input class="form-control" type="text" name="REGISTER[<?= $FIELD ?>]"
                           value="<?= $arResult["VALUES"][$FIELD] ?>" placeholder="Номер телефона"
                           autocomplete="tel"/>
                    </div><?
                    break;

                case 'EMAIL':
                    ?>
                    <div class="form-group">
                    <label>Введите ваш Email</label>
                    <input class="form-control" type="text" name="REGISTER[<?= $FIELD ?>]"
                           value="<?= $arResult["VALUES"][$FIELD] ?>" placeholder="Электронная почта"
                           autocomplete="email"/>
                    </div><?
                    break;

                case 'PASSWORD':
                    ?>
                    <div class="form-group">
                    <label>Введите ваш новый пароль</label>
                    <input class="form-control" type="text" name="REGISTER[<?= $FIELD ?>]"
                           value="<?= $arResult["VALUES"][$FIELD] ?>" placeholder="Пароль" autocomplete="off"/>
                    </div><?
                    break;

                case 'CONFIRM_PASSWORD':
                    ?><input class="form-control" type="hidden" name="REGISTER[<?= $FIELD ?>]"
                             value="<?= $arResult["VALUES"][$FIELD] ?>" autocomplete="off" />
                    <script>
                        jQuery(document).ready(function($) {
                            var $CONFIRM_PASSWORD = $('[name="REGISTER[CONFIRM_PASSWORD]"]');
                            $('[name="REGISTER[PASSWORD]"]').on('keyup', function () {
                                $CONFIRM_PASSWORD.val($(this).val());
                            });
                        });
                    </script>
                             <?
                    break;

                default:
                    ?>
                    <div class="form-group">
                    <input class="form-control" type="text" name="REGISTER[<?= $FIELD ?>]"
                           value="<?= $arResult["VALUES"][$FIELD] ?>"/>
                    </div><?
                    break;
            }
        } ?>
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
