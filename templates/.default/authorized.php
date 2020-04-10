<p>
    Вы зарегистрированы и успешно авторизовались.
    <?php if (!empty($arResult['REDIRECT_URL'])): ?>
        Через несколько секунд вы буете перенаправленны на стрианцу профиля.
        <script>setTimeout(function() { window.location.href = "<?= $arResult['REDIRECT_URL'] ?>"; }, 4000);</script>
    <?php endif ?>
</p>
<p>
    <a href="/">Вернуться на главную страницу</a>
    | <a href="<?= PATH_TO_PROFILE; ?>">Просмотреть свой профиль</a>
    | <a href="<?= PATH_TO_AUTH; ?>?logout=yes">Выйти</a>
</p>