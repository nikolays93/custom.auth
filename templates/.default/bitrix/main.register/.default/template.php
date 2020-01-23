<?
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2014 Bitrix
 */

/**
 * Bitrix vars
 * @global CMain $APPLICATION
 * @global CUser $USER
 * @param array $arParams
 * @param array $arResult
 * @param CBitrixComponentTemplate $this
 */

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

$rand = randString(6);
$columnClass = (!empty($arParams['IS_AJAX']) && 'Y' == $arParams['IS_AJAX']) ? 'col-sm-10 offset-sm-1' : 'col-sm-4 offset-sm-4';
?>
<div class="register-form<?= $columnClass ?>">
	<?if($USER->IsAuthorized()):?>
	<p><?echo GetMessage("MAIN_REGISTER_AUTH")?></p>
	<?else:?>
    <form id="register-form<?=$rand?>" class="form-register" method="post" action="<?=POST_FORM_ACTION_URI?>" name="regform" enctype="multipart/form-data">
    	<?if($arResult["BACKURL"] <> '') echo '<input type="hidden" name="backurl" value="' .$arResult["BACKURL"]. '" />';?>
        <div class="error-messages text-danger" data-entity="error-messages">
    	<?php
    	if (count($arResult["ERRORS"]) > 0) {
    		foreach ($arResult["ERRORS"] as $key => $error) {
    			if (intval($key) == 0 && $key !== 0) {
    				$arResult["ERRORS"][$key] = str_replace("#FIELD_NAME#", "&quot;".GetMessage("REGISTER_FIELD_".$key)."&quot;", $error);
    			}
    		}

    		ShowError(implode("<br />", $arResult["ERRORS"]));
    	}
	    elseif($arResult["USE_EMAIL_CONFIRMATION"] === "Y") {
	    	echo '<p>' .GetMessage("REGISTER_EMAIL_WILL_BE_SENT"). '</p>';
	    }
        ?>
        </div>

        <?foreach (array_reverse($arResult["SHOW_FIELDS"]) as $FIELD) {
	    	switch ($FIELD) {
	    		case 'LOGIN':
	    		case 'NAME':
	    			break;

	    		case 'PERSONAL_PHONE':
	    			?><div class="form-group">
                        <label>Введите ваш номер телефона</label>
	    				<input class="form-control" type="text" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" placeholder="Номер телефона" autocomplete="tel" />
                    </div><?
	    			break;

	    		case 'EMAIL':
		    		?><div class="form-group">
                        <label>Введите ваш Email</label>
	    				<input class="form-control" type="text" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" placeholder="Электронная почта" autocomplete="email" />
                    </div><?
	    			break;

	    		case 'PASSWORD':
	    			?><div class="form-group">
                        <label>Введите ваш новый пароль</label>
	    				<input class="form-control" type="text" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" placeholder="Пароль" autocomplete="off" />
                    </div><?
	    			break;

	    		case 'CONFIRM_PASSWORD':
	    			?><input class="form-control" type="hidden" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" autocomplete="off" /><?
	    			break;

	    		default:
		    		?><div class="form-group">
	    				<input class="form-control" type="text" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" />
                    </div><?
	    			break;
	    	}
	    }?>
        <div class="form-helpers">
            <div class="form-check">
                <label class="form-check-label">
                    <input class="form-check-input" type="checkbox" name="privacy_accept" value="Y">
                    <span class="form-check-label">Нажимая кнопку "Зарегистрироваться", я подтверждаю, что я ознакомился с <a href="#" data-fancybox="" data-type="ajax" data-src="/auth/?action=privacy" href="javascript:;">политикой обработки персональных данных</a><br> и даю согласие на обработку мои персональных данных</span>
                </label>
            </div>
        </div>
        <?if ($arResult["USE_CAPTCHA"] == "Y")
        {
        	?>
        	<input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
        	<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
        	<?=GetMessage("REGISTER_CAPTCHA_PROMT")?>:<span class="starrequired">*</span>
        	<input type="text" name="captcha_word" maxlength="50" value="" />
        	<?
        }?>
        <div class="submit-wrap mt-2 mb-2">
            <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
        </div>

        <?
        if($arResult["AUTH_SERVICES"]):?>
        <div class="social-login social-register">
        	<span>Зарегистрироваться с помощью:</span>
            <?$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "flat",
            	array(
            		"AUTH_SERVICES"=>$arResult["AUTH_SERVICES"],
            		"SUFFIX"=>"form",
            	),
            	$component->__parent,
            	array("HIDE_ICONS"=>"Y")
            );?>
        </div>
        <?endif?>
    </form>

    <a class="login" href="/auth/?login=yes" data-fancybox="" data-type="ajax" data-src="/auth/?action=getForm" rel="nofollow">Уже зарегистрированы? Войти</a>
    <?endif?>
</div>

<script>
    <?if(!empty($arParams['IS_AJAX']) && 'Y' == $arParams['IS_AJAX']):
    $arParamsToDelete = array(
        "login",
        "login_form",
        "logout",
        "register",
        "forgot_password",
        "change_password",
        "confirm_registration",
        "confirm_code",
        "confirm_user_id",
        "logout_butt",
        "auth_service_id",
        "fancybox"
    );?>
    var registerFormTarget = '#register-form<?=$rand?>',
        registerFormErrorsTarget = registerFormTarget + ' [data-entity="error-messages"]',
        registerRefferLink = '<?echo $APPLICATION->GetCurPageParam("", array($arParamsToDelete));?>';

    jQuery(document).ready(function($) {
        var $form = $(registerFormTarget),
        $errors = $(registerFormErrorsTarget);

        $form.on('submit', function () {
            $errors.hide();

            $.post('/local/components/nikolays93/custom.auth/ajax.php?register=yes', $form.serialize(), function (response) {

                if (response && response.STATUS)
                {
                    if ('OK' == response.STATUS) {
                        $form.addClass('success').html( response.MESSAGES );
                        // window.location.href = window.location.origin + registerRefferLink;
                }
                else {
                    $errors
                        .html(response.MESSAGES.map(function(elem, index) {
                            return elem + '<br>';
                        }))
                        .fadeIn(100);
                }
            }

        }, 'json');

            return false;
        });
        <?endif?>

        $('[name="privacy_accept"]').on('change', function () {
            var $submit = $(this).closest('form').find('[type="submit"]');

            if( !$(this).is(':checked') ) {
                $submit.addClass('disabled')
                .attr('disabled', 'disabled');
            }
            else {
                $submit.removeClass('disabled')
                .removeAttr('disabled');
            }
        }).trigger('change');

        var $CONFIRM_PASSWORD = $('[name="REGISTER[CONFIRM_PASSWORD]"]')
        $('[name="REGISTER[PASSWORD]"]').on('keyup', function () {
            $CONFIRM_PASSWORD.val( $(this).val() );
        });
    });
</script>


	<?/*foreach ($arResult["SHOW_FIELDS"] as $FIELD):?>
		<?if($FIELD == "AUTO_TIME_ZONE" && $arResult["TIME_ZONE_ENABLED"] == true):?>
			<tr>
				<td><?echo GetMessage("main_profile_time_zones_auto")?><?if ($arResult["REQUIRED_FIELDS_FLAGS"][$FIELD] == "Y"):?><span class="starrequired">*</span><?endif?></td>
				<td>
					<select name="REGISTER[AUTO_TIME_ZONE]" onchange="this.form.elements['REGISTER[TIME_ZONE]'].disabled=(this.value != 'N')">
						<option value=""><?echo GetMessage("main_profile_time_zones_auto_def")?></option>
						<option value="Y"<?=$arResult["VALUES"][$FIELD] == "Y" ? " selected=\"selected\"" : ""?>><?echo GetMessage("main_profile_time_zones_auto_yes")?></option>
						<option value="N"<?=$arResult["VALUES"][$FIELD] == "N" ? " selected=\"selected\"" : ""?>><?echo GetMessage("main_profile_time_zones_auto_no")?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td><?echo GetMessage("main_profile_time_zones_zones")?></td>
				<td>
					<select name="REGISTER[TIME_ZONE]"<?if(!isset($_REQUEST["REGISTER"]["TIME_ZONE"])) echo 'disabled="disabled"'?>>
			<?foreach($arResult["TIME_ZONE_LIST"] as $tz=>$tz_name):?>
						<option value="<?=htmlspecialcharsbx($tz)?>"<?=$arResult["VALUES"]["TIME_ZONE"] == $tz ? " selected=\"selected\"" : ""?>><?=htmlspecialcharsbx($tz_name)?></option>
			<?endforeach?>
					</select>
				</td>
			</tr>
		<?else:?>
		<?=GetMessage("REGISTER_FIELD_".$FIELD)?>:<?if ($arResult["REQUIRED_FIELDS_FLAGS"][$FIELD] == "Y"):?><span class="starrequired">*</span><?endif?>
		<?
		switch ($FIELD)
		{
			case "PASSWORD":
				?><input size="30" type="password" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" autocomplete="off" class="bx-auth-input" />
				<?if($arResult["SECURE_AUTH"]):?>
					<span class="bx-auth-secure" id="bx_auth_secure" title="<?echo GetMessage("AUTH_SECURE_NOTE")?>" style="display:none">
						<div class="bx-auth-secure-icon"></div>
					</span>
					<noscript>
					<span class="bx-auth-secure" title="<?echo GetMessage("AUTH_NONSECURE_NOTE")?>">
						<div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>
					</span>
					</noscript>
					<script type="text/javascript">
						document.getElementById('bx_auth_secure').style.display = 'inline-block';
					</script>
					<?endif?>
					<?
				break;
			case "CONFIRM_PASSWORD":
				?><input size="30" type="password" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" autocomplete="off" /><?
				break;

			default:
				if ($FIELD == "PERSONAL_BIRTHDAY"):?><small><?=$arResult["DATE_FORMAT"]?></small><br /><?endif;
				?><input size="30" type="text" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" /><?
					if ($FIELD == "PERSONAL_BIRTHDAY")
					?><?
		}?>
		<?endif?>
	<?endforeach*/?>

<?// ********************* User properties ***************************************************?>
<?/*if($arResult["USER_PROPERTIES"]["SHOW"] == "Y"):?>
	<tr><td colspan="2"><?=strlen(trim($arParams["USER_PROPERTY_NAME"])) > 0 ? $arParams["USER_PROPERTY_NAME"] : GetMessage("USER_TYPE_EDIT_TAB")?></td></tr>
	<?foreach ($arResult["USER_PROPERTIES"]["DATA"] as $FIELD_NAME => $arUserField):?>
	<tr><td><?=$arUserField["EDIT_FORM_LABEL"]?>:<?if ($arUserField["MANDATORY"]=="Y"):?><span class="starrequired">*</span><?endif;?></td><td>
			<?$APPLICATION->IncludeComponent(
				"bitrix:system.field.edit",
				$arUserField["USER_TYPE"]["USER_TYPE_ID"],
				array("bVarsFromForm" => $arResult["bVarsFromForm"], "arUserField" => $arUserField, "form_name" => "regform"), null, array("HIDE_ICONS"=>"Y"));?></td></tr>
	<?endforeach;?>
<?endif;*/?>
<?// ******************** /User properties ***************************************************?>

<?/*<p><?echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?></p>
<p><span class="starrequired">*</span><?=GetMessage("AUTH_REQ")?></p>*/ ?>
