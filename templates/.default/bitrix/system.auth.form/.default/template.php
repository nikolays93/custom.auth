<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

CJSCore::Init();

global $APPLICATION;

$isAjax = false;
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 'xmlhttprequest' === strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    $isAjax = true;
}
elseif(!empty($arParams['IS_AJAX']) && 'Y' == $arParams['IS_AJAX']) {
    $isAjax = true;
}

?>
<div class="auth-form mt-5">
    <form name="system_auth_form<?=$arResult["RND"]?>" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
        <div class="auth-form__errors text-danger mb-2" data-entity="error-messages">
    	<?if ($arResult['SHOW_ERRORS'] == 'Y' && $arResult['ERROR']) {
    		ShowMessage($arResult['ERROR_MESSAGE']);
        }?>
        </div>

        <div class="form-group">
            <input class="form-control" type="text" placeholder="Логин или эл. почта" autocomplete="username" name="USER_LOGIN" maxlength="50">
            <script>
    			BX.ready(function() {
    				var loginCookie = BX.getCookie("<?=CUtil::JSEscape($arResult["~LOGIN_COOKIE_NAME"])?>");
    				if (loginCookie)
    				{
    					var form = document.forms["system_auth_form<?=$arResult["RND"]?>"];
    					var loginInput = form.elements["USER_LOGIN"];
    					loginInput.value = loginCookie;
    				}
    			});
    		</script>
        </div>

        <div class="form-group">
            <input class="form-control" type="password" placeholder="Введите пароль" autocomplete="current-password" name="USER_PASSWORD" maxlength="50"><!-- autocomplete="off" -->
            <?if($arResult["SECURE_AUTH"]):?>
                <span class="bx-auth-secure" id="bx_auth_secure<?=$arResult["RND"]?>" title="<?echo GetMessage("AUTH_SECURE_NOTE")?>" style="display:none">
                	<div class="bx-auth-secure-icon"></div>
                </span>
                <noscript>
                	<span class="bx-auth-secure" title="<?echo GetMessage("AUTH_NONSECURE_NOTE")?>">
                		<div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>
                	</span>
                </noscript>
                <script type="text/javascript">
                	document.getElementById('bx_auth_secure<?=$arResult["RND"]?>').style.display = 'inline-block';
                </script>
            <?endif?>
        </div>

        <div class="auth-form__helpers text-nowrap text-center mb-2">
        	<?if ($arResult["STORE_PASSWORD"] == "Y"):?>
        	<label title="<?=GetMessage("AUTH_REMEMBER_ME")?>" class="form-check">
        		<input class="remember form-check-input" type="checkbox" name="USER_REMEMBER" value="Y" id="USER_REMEMBER_frm" />
        		<span class="form-check-label">запомнить меня</span>
        	</label>
        	<?endif?>
        	<noindex><a class="forgot" href="<?=$arResult["AUTH_FORGOT_PASSWORD_URL"]?>" data-fancybox data-type="ajax" rel="nofollow" data-src="/local/components/nikolays93/custom.auth/ajax.php?forgot_password=yes">Забыли пароль?</a></noindex>
        </div>

        <?if ($arResult["CAPTCHA_CODE"]):?>
            <?echo GetMessage("AUTH_CAPTCHA_PROMT")?>:<br />
            <input type="hidden" name="captcha_sid" value="<?echo $arResult["CAPTCHA_CODE"]?>" />
            <img src="/bitrix/tools/captcha.php?captcha_sid=<?echo $arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" /><br /><br />
            <input type="text" name="captcha_word" maxlength="50" value="" />
        <?endif?>

        <div class="auth-form__submit-wrap">
        	<input class="btn btn-primary aligncenter" type="submit" name="Login" value="Войти" />
        </div>

        <?if($arResult["AUTH_SERVICES"]):?>
        <div class="auth-form__social-login mt-4">
        	<span>Войти с помощью:</span>
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

        <?if($arResult["BACKURL"] <> ''):?>
        <input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
        <?endif?>
        <?foreach ($arResult["POST"] as $key => $value):?>
        <input type="hidden" name="<?=$key?>" value="<?=$value?>" />
        <?endforeach?>
        <input type="hidden" name="AUTH_FORM" value="Y" />
        <input type="hidden" name="TYPE" value="AUTH" />

        <?if($arResult["NEW_USER_REGISTRATION"] == "Y"):?>
        <div class="mt-3 text-center"><!-- col-sm-4 offset-sm-4  -->
            <noindex><a href="/auth/?register=yes" data-fancybox data-type="ajax"
        data-src="/local/components/nikolays93/custom.auth/ajax.php?register=yes" rel="nofollow">Регистрация</a></noindex>
        </div>
        <?endif?>
    </form>
</div>
<?if($isAjax):?>
<script>
    <?
    // $arParamsToDelete = array(
    //     "login",
    //     "login_form",
    //     "logout",
    //     "register",
    //     "forgot_password",
    //     "change_password",
    //     "confirm_registration",
    //     "confirm_code",
    //     "confirm_user_id",
    //     "logout_butt",
    //     "auth_service_id",
    //     "fancybox"
    // );
    ?>
    var authFormTarget = '[name="system_auth_form<?=$arResult["RND"]?>"]',
        authFormErrorsTarget = authFormTarget + ' [data-entity="error-messages"]';
        // ,authRefferLink = '<?//= $APPLICATION->GetCurPageParam("", $arParamsToDelete);?>';

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
