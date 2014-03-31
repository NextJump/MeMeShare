<div class="login-container">
    <div class="logo"></div>
    <div class="minutes-logged text-center">
        <h1><?= number_format($minutesLogged) ?></h1>
        <h2>Minutes Logged</h2>
        <h3>last 30 Days</h3>
    </div>
    <div class="text-center">
        <a id="login-button" href="<?= $authUrl ?>">
            <h3>Login</h3>
            <span class="subtext">with your Google Application Account</span>
        </a>
    </div>
</div>