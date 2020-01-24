<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?

ShowMessage($arParams["~AUTH_RESULT"]);

$isAjax = false;
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 'xmlhttprequest' === strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    $isAjax = true;
}
elseif(!empty($arParams['IS_AJAX']) && 'Y' == $arParams['IS_AJAX']) {
    $isAjax = true;
}

?>
<div class="login-form">

	<form name="bform" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
	<?
	if (strlen($arResult["BACKURL"]) > 0)
	{
	?>
		<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
	<?
	}
	?>
		<input type="hidden" name="AUTH_FORM" value="Y">
		<input type="hidden" name="TYPE" value="SEND_PWD">
		<input type="hidden" name="SENT" value="1">
		<div class="login-form__errors auth-form__errors text-danger mb-2" data-entity="error-messages">
		<?if(!empty($_REQUEST['SENT']) && '1' == $_REQUEST['SENT']): ?>
			<?$APPLICATION->IncludeComponent(
				"bitrix:system.auth.form", 
				"errors", 
				array("SHOW_ERRORS" => "Y"),
				$component->__parent
			);?>
		</div>
		<?else:?>
		<p class="mb-1">
			Введите электронный адрес указанный при регистрации.
			<?//=GetMessage("AUTH_FORGOT_PASSWORD_1")?>
		</p>
		<?endif;?>

		<div class="form-inline">
			<label class="col-form-label mr-2">
				<input class="form-control" type="text" name="USER_EMAIL" maxlength="255" placeholder="Email" autocomplete="email" />
			</label>

			<input class="btn btn-primary" type="submit" name="send_account_info" value="Получить код восстановления" />
		</div>

		<?if($arResult["USE_CAPTCHA"]):?>
		<input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
		<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
		<?echo GetMessage("system_auth_captcha")?>
		<input type="text" name="captcha_word" maxlength="50" value="" />
		<?endif?>
	</form>
</div>
<!-- <script type="text/javascript">
document.bform.USER_LOGIN.focus();
</script>
 -->
<?if($isAjax):?>
<script>
    var authFormTarget = '.login-form [name="bform"]',
        authFormErrorsTarget = authFormTarget + ' [data-entity="error-messages"]';

    jQuery(document).ready(function($) {
        var $form = $(authFormTarget),
        	$errors = $(authFormErrorsTarget);

        $form.on('submit', function () {
            $errors.hide();

            $.post('/local/components/nikolays93/custom.auth/ajax.php', $form.serialize(), function (response) {

                if (response && response.STATUS)
                {
                    if ('OK' == response.STATUS) {
                        $form.after( response.HTML );
                        $form.remove();
                    }
                    else {
                        $errors
                            .html(response.MESSAGES)
                            .html(response.HTML)
                            .fadeIn(100);
                    }
                }

            }, 'json');

            return false;
        });
    });
</script>
<?endif;?>