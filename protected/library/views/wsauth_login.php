<div class="uk-grid">
    <div class="uk-width-small-1-1 uk-grid-medium-1-2 uk-container-center">
        <h3 class="uk-text-center">
            <?php echo WsConfig::get('app_name'); ?>
            -
            <?php echo WsLocalize::msg('Login'); ?>
        </h3>
    </div>
</div>

<?php
    $error_message = '';
    $email = '';
    $password = '';

    // initialize auth module
    $auth = new WsAuth();

    // check if user is allready loged in
    if ($auth->checkSession()) {
        $error_message =  WsLocalize::msg('Current user is allready loged in.')
            .WsLocalize::msg(' If you are not current user')
            .WsLocalize::msg(' or you just want to logaout,')
            .WsLocalize::msg(' then click following link ')
            .'<a href="'.WsUrl::link('wsauth', 'logout').'">'
            .WsLocalize::msg('logout').'</a>';
    } else {
        if (isset($_POST['email']) and isset($_POST['password'])) {
            $email = filter_input(INPUT_POST, 'email');
            $password = filter_input(INPUT_POST, 'password');

            // login user
            $res = $auth->login($email, $password);
            if ($res === WS_AUTH_NO_MATCH) {
                $error_message = WsLocalize::msg(
                    'Access forbiden. Register first.');
            } else if ($res === WS_AUTH_NOT_ACTIVE) {
                $error_message = WsLocalize::msg(
                    'Can\'t login. Your account is not active.');
            } else if ($res === WS_AUTH_NOT_VERIFIED) {
                $error_message = WsLocalize::msg(
                    'Can\'t login. Your account is not verified yet.');
            } else {
                $error_message = 'success';
            }
        }

        // begining of login form
        $login_form = new WsForm(WsUrl::link('Wsauth', 'login'));

        $login_form->submitButtonText = WsLocalize::msg('login');
?>
    <div class="uk-grid">
        <div class="uk-width-small-9-10 uk-width-medium-1-2 uk-container-center">
<?php
        // email field
        $login_form->textInput(array(
           'label' => WsLocalize::msg('email address'),
           'name' => 'email',
           'value' => $email,
           'type' => 'email'
        ));
        // password field
        $login_form->textInput(array(
            'label' => WsLocalize::msg('password'),
            'name' => 'password',
            'value' => $password,
            'type' => 'password'
        ));
        
        // show login form
        $login_form->show();
    }

    if ($error_message != '' and $error_message != 'success') {
?>
        </div>
    </div>

<div class="uk-grid">
    <div class="uk-width-small-1-1 uk-width-medium-1-2 uk-container-center">
        <div class="uk-alert uk-alert-danger">
        <?php
            header('HTTP/1.1 401 Unauthorized');
            echo $error_message;
        ?>
        </div>
    </div>
</div>

<?php
    } else if ($error_message == 'success') {
        $url = WsUrl::link('site', 'index');
?>
<script>
    setTimeout(function () {
        window.location.href='<?php echo $url; ?>'; // the redirect goes here
    }, 100);
</script>
<?php
    }
?>
