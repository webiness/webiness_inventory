<div class="row">
    <div class="column column-12">
        <h3 class="text-center">
            <?php echo WsConfig::get('app_name'); ?>
            -
            <?php echo WsLocalize::msg('Users, Roles and Permissions'); ?>
        </h3>
    </div>
</div>

<div class="row">
    <div class="column column-8 column-offset-2">
        <?php
            $user_grid = new WsModelGridView($user_model);
            $user_grid->show();
        ?>
    </div>
</div>

<div class="row">
    <div class="column column-4 column-offset-2">
        <?php
            $roles_grid = new WsModelGridView($roles_model);
            $roles_grid->show();
        ?>
    </div>
    <div class="column column-4">
        <?php
            $perms_grid = new WsModelGridView($perms_model);
            $perms_grid->show();
        ?>
    </div>
</div>
