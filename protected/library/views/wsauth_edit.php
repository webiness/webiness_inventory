<div class="row">
    <div class="col-sm-12">
        <h3 class="text-center">
            <?php echo WsConfig::get('app_name'); ?>
            -
            <?php echo $user_email; ?>
        </h3>
    </div>
</div>

<div class="row">
    <div class="col-sm-12 col-md-10 col-md-offset-1">
<?php

    if ($user_model == null) {
?>
        <div class="alert alert-danger">
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
