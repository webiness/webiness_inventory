<div class="uk-grid">
    <div class="uk-width-1-1">
        <h1 class="uk-text-center">
            <?php echo WsConfig::get('app_name'); ?>
            -
            <?php echo $user_email; ?>
        </h1>
    </div>
</div>

<div class="uk-grid ">
    <div class="uk-width-3-4 uk-container-center">
<?php

    if ($user_model == null) {
?>
        <div class="uk-alert uk-alert-danger">
            <?php
                header('HTTP/1.1 401 Unauthorized');
                WsLocalize::msg('Acces forbiden.')
            ?>
        </div>
<?php
    } else {
        $form = new WsModelForm($user_model, 'user_form');
        $form->show();
    }
?>
    </div>
</div>
