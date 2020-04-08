<? if ( ! defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;

if ( ! defined('PATH_TO_PROFILE')) {
    define('PATH_TO_PROFILE', '/user/');
}
if ( ! defined('PATH_TO_AUTH')) {
    define('PATH_TO_AUTH', '/auth/');
}
if ( ! defined('PATH_TO_REGISTER')) {
    define('PATH_TO_REGISTER', '/auth/?register=yes');
}
if ( ! defined('PATH_TO_FORGOT_PASSWORD')) {
    define('PATH_TO_FORGOT_PASSWORD', '/auth/?forgot_password=yes');
}

class customAuthComponent extends CBitrixComponent
{
    static $bConfirmReq = false;

    /** @var array */
    private $errors = array();

    /** @var array Field for ajax request data */
    private $arResponse = array(
        'STATUS' => '',
        'MESSAGES' => array(),
        'HTML' => '',
    );

    private $template = '';

    function __construct($component = null)
    {
        parent::__construct($component);
        // Loader::includeModule( 'moduleName' );
    }

    function onPrepareComponentParams($arParams)
    {
        /**
         * @var $arParams ['ACTION'] string
         * If is ACTION param exists, strval to define
         */
        if (isset($arParams['ACTION']) && strlen($arParams['ACTION']) > 0) {
            $arParams['ACTION'] = strval($arParams['ACTION']);
        } elseif ( ! empty($this->request['action'])) {
            $arParams['ACTION'] = strval($this->request['action']);
        } else {
            $arParams['ACTION'] = '';
        }

        /**
         * @var $arParams ['IS_AJAX'] boolean
         * If is IS_AJAX param exists, check the true defined
         */
        $arParams['IS_AJAX'] = (isset($arParams['IS_AJAX']) && "Y" == $arParams['IS_AJAX']);

        return $arParams;
    }

    protected function sendResultPostMessage($arFields)
    {
        if ('OK' === $this->arResponse['STATUS']) {
            /** @var CEvent */
            $event = new CEvent;

            // To send user email with new data
            $event->SendImmediate(static::$bConfirmReq ? "NEW_USER_CONFIRM" : "USER_INFO", SITE_ID, $arFields);

            // To send admin mail about new user
            $event->SendImmediate("NEW_USER", SITE_ID, $arFields);
        } /**
         * Attention about fake try register
         */
        else {
        }
    }

    /**
     * @param  [type] &$arFields [description]
     * @return [type]            [description]
     * @todo
     */
    protected function customValidateUserFields($arFields)
    {
        if (empty($arFields['PERSONAL_PHONE'])) {
            $this->arResponse['STATUS'] = 'ERROR';
            $this->arResponse['MESSAGES'][] = 'Поле Номер телефона обязательно для заполнения.';
        }
    }

    /**
     * Try insert new user
     * @param Array $arFields [description]
     */
    protected function insertUser($arFields)
    {
        $CUser = new CUser;
        $USER_ID = $CUser->Add($arFields);

        if (0 < ($arFields['USER_ID'] = intval($USER_ID))) {
            $this->arResponse['STATUS'] = 'OK';
            $this->arResponse['MESSAGES'][] = 'Вы успешно зарегистрированы';

            if (static::$bConfirmReq) {
                $this->arResponse['MESSAGES'][] = ', на указанный Вами EMail отправлено письмо для потверждения.';
            }

            $arFields["STATUS"] = $arFields["ACTIVE"] == "Y" ? 'Активен' : 'Не активен';
            $arFields["URL_LOGIN"] = urlencode($arFields["LOGIN"]);
        } else {
            $this->arResponse['STATUS'] = 'ERROR';

            $errors = explode('<br>', $CUser->LAST_ERROR);
            foreach ($errors as $error) {
                if ( ! $error) {
                    continue;
                }

                $this->arResponse['MESSAGES'][] = $error;
            }
        }
    }

    protected function doRegisterAction()
    {
        define("NO_KEEP_STATISTIC", true);
        define("NOT_CHECK_PERMISSIONS", true);

        global $USER, $DB;

        /** @todo var */
        // $requiredFields = array();

        /** @todo var bool */
        $useCaptha = false;

        /** @var bool if is the user must be confirm registration at email (from main module settings) */
        static::$bConfirmReq = (COption::GetOptionString("main", "new_user_registration_email_confirmation",
                "N")) == "Y";

        /** @var array */
        $REGISTER = $_REQUEST['REGISTER'];

        /** @var get user password from request */
        $paswd = ! empty($REGISTER['PASSWORD']) ? strip_tags(trim($REGISTER['PASSWORD'])) : '';

        /** @var array Properties for new user */
        $arFields = Array(
            "LAST_NAME" => '',
            "EMAIL" => ! empty($REGISTER['EMAIL']) ? strip_tags(trim($REGISTER['EMAIL'])) : '',
            "LID" => SITE_ID,
            "ACTIVE" => static::$bConfirmReq ? "N" : "Y",
            "GROUP_ID" => array(2),
            "PASSWORD" => $paswd,
            "CONFIRM_PASSWORD" => $paswd,
            "CHECKWORD" => md5(CMain::GetServerUniqID() . uniqid()),
            "~CHECKWORD_TIME" => $DB->CurrentTimeFunction(),
            "CONFIRM_CODE" => static::$bConfirmReq ? randString(8) : "",
            "PERSONAL_PHONE" => ! empty($REGISTER['PERSONAL_PHONE']) ? strip_tags(trim($REGISTER['PERSONAL_PHONE'])) : '',
        );

        list($arFields['NAME']) = explode('@', $arFields['EMAIL']);
        $arFields['LOGIN'] = $arFields['EMAIL'];

        /**
         * @todo check captcha
         * if($APPLICATION->CaptchaCheckCode($_REQUEST["captcha_word"], $_REQUEST["captcha_sid"]))
         */
        $this->customValidateUserFields($arFields);
        $this->insertUser($arFields);
        $this->sendResultPostMessage($arFields);
    }

    protected function getPageContent($pagename = '')
    {
        global $APPLICATION;

        $ext = explode('.', $pagename);
        $ext = end($ext);

        if ( ! in_array($ext, array('html', 'htm', 'php'))) {
            $pagename .= 'index.php';
        }

        $filename = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . '/' . $pagename);

        if (file_exists($filename)) {
            $APPLICATION->RestartBuffer();

            define('EXCLUDE_FOOTER', true);
            include $filename;
            die();
        } else {
            $this->arResponse['STATUS'] = 'ERROR';
            $this->arResponse['MESSAGES'] = 'К сожалению, на данный момент страница не доступна.';
        }

        return false;
    }

    private function privacyAction()
    {
        $this->getPageContent($this->arParams['PRIVACY_PAGE']);
    }

    private function personalAction()
    {
        $this->getPageContent($this->arParams['PERSONAL_PAGE']);
    }

    private function getFormAction()
    {
        global $APPLICATION, $USER;

        if ( ! $USER->isAuthorized()) {
            $APPLICATION->RestartBuffer();
        }
    }

    function executeComponent()
    {
        global $APPLICATION, $USER;

        $request = Main\Application::getInstance()->getContext()->getRequest();
        $request->addFilter(new Main\Web\PostDecodeFilter);

        // $action = $request->get("action");

        // remove it
        if (($register = $request->get("REGISTER")) && ! empty($register)) {
            $this->arParams['ACTION'] = 'doRegister';
        }

        if ( ! empty($this->arParams['ACTION'])) {
            if (is_callable(array($this, $this->arParams['ACTION'] . "Action"))) {
                try {
                    call_user_func(array($this, $this->arParams['ACTION'] . "Action"));
                } catch (\Exception $e) {
                    $this->errors[] = $e->getMessage();
                }
            }
        }

        /**
         * Set browser title
         */
        if ("N" !== $this->arParam['SET_TITLE']) {
            if ("yes" == $request->get('forgot_password')) {
                $APPLICATION->SetTitle("Запрос пароля на восстановление");
            } elseif ("yes" == $request->get('change_password')) {
                $APPLICATION->SetTitle("Востановление пароля");
            } elseif ("yes" == $request->get('register')) {
                $APPLICATION->SetTitle("Регистрация");
            } else {
                $APPLICATION->SetTitle("Авторизация");
            }
        }

        if ($this->arParams['IS_AJAX']) {
            $json = false;

            // is auth action
            if ("Y" == $request->get("AUTH_FORM")) {
                if ( ! empty($this->errors)) {
                    $this->arResponse['STATUS'] = 'ERROR';
                    $this->arResponse['MESSAGES'] = $this->errors;
                }

                ob_start();

                $APPLICATION->IncludeComponent(
                    "bitrix:system.auth.form",
                    "errors",
                    array(
                        "SHOW_ERRORS" => "Y"
                    ),
                    $this,
                    array("HIDE_ICONS" => "Y")
                );

                $ob = ob_get_clean();
                $errors = trim($ob);

                if ($errors) {
                    $this->arResponse['STATUS'] = 'ERROR';
                    $this->arResponse['HTML'] = $errors;
                } else {
                    ob_start();

                    /**
                     * You are authorized!
                     */
                    $this->arResult['REDIRECT_URL'] = '/auth/';
                    $this->includeComponentTemplate();

                    $this->arResponse['STATUS'] = 'OK';
                    $this->arResponse['HTML'] = ob_get_clean();
                }

                $json = true;
            } // is register action
            elseif (($register = $request->get("REGISTER")) && ! empty($register)) {
                $json = true;
            }

            if ($json) {
                $APPLICATION->RestartBuffer();

                header('Content-Type: application/json');
                echo \Bitrix\Main\Web\Json::encode($this->arResponse);
                $APPLICATION->FinalActions(); // need?
                die();
            }
        }

        if ( ! empty($this->errors)) {
            $this->arResult['STATUS'] = 'ERROR';
            $this->arResult['MESSAGES'] = $this->errors;
        }

        if ("yes" == $_REQUEST['logout']) {
            $USER->Logout();
        }

        if ( ! $this->template) {
            $templates = array('forgot_password', 'change_password', 'register');
            foreach ($templates as $template) {
                if ('yes' === $request->get($template)) {
                    $this->template = $template;
                }
            }
        }

        // $this->setTemplateName();
        $this->includeComponentTemplate($this->template);
        if ('getForm' == $this->arParams['ACTION']) {
            die();
        }
    }
}