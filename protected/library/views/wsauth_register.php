<div class="row">
    <div class="col col-sm-12 col-md-6 col-md-offset-3">
        <h3 class="text-center">
            <?php echo WsConfig::get('app_name'); ?>
            -
            <?php echo WsLocalize::msg('user registration'); ?>
        </h3>
    </div>
</div>

<?php
    $error_message = '';
    $email = '';
    $password = '';


    // check if we have input data
    if (isset($_POST['email']) and isset($_POST['password'])) {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = filter_input(INPUT_POST, 'password',FILTER_SANITIZE_STRING);

        // initialize auth module
        $auth = new WsAuth();

        // add user
        $ret = $auth->createUser($email, $password);
        if (!$ret) {
            $error_message = WsLocalize::msg(
                'Database server is down. '
            ).WsLocalize::msg('Try to register later.');
        } else if ($ret === WS_AUTH_USER_EXISTS) {
            $error_message = WsLocalize::msg(
                'User account could not be created. '
            ).WsLocalize::msg('Email address allready in use. ');
        }
    }
?>

<div class="row">
    <div class="col-sm-12 col-md-6 col-md-offset-3">
<?php
    // begining of registration form
    $registration_form = new WsForm(WsUrl::link('Wsauth', 'register'));

    $registration_form->submitButtonText = 'register user';

    // email field
    $registration_form->textInput(array(
        'label' => WsLocalize::msg('email address'),
        'name' => 'email',
        'value' => $email,
        'type' => 'email'
    ));
    // password field
    $registration_form->textInput(array(
        'label' => WsLocalize::msg('password'),
        'name' => 'password',
        'value' => $password,
        'type' => 'password'
    ));

    // show registration form
    $registration_form->show();
?>
    </div>
</div>

<?php
    if (isset($_POST) and $error_message != '') {
?>
        <br/>
        <br/>
        <div class="row">
            <div class="col-sm-12 col-md-6 col-md-offset-3 alert alert-error">
            <?php
                header('HTTP/1.1 500 Internal Server Error');
                echo $error_message;
    } else if (isset($_POST) and $error_message != '') {
?>
        <br/>
        <br/>
        <div class="row">   
            <div class="col-sm-12 col-md-6 col-md-offset-3 alert alert-success">
<?php
        // check if we have input data
        if (isset($_POST['email']) and isset($_POST['password'])) {
            echo WsLocalize::msg('User account succesfuly creted.');
            echo WsLocalize::msg('Check your email to activate account.');
        }
    }
?>
        </div>
    </div>
