<div class="uk-grid">
    <div class="uk-width-small-1-1 uk-width-medium-1-1">
        <h1 class="uk-text-center">
            <?php echo WsConfig::get('app_name'); ?>
            -
            <?php echo WsLocalize::msg('Users, Roles and Permissions'); ?>
        </h1>
    </div>
</div>

<div class="uk-grid">
    <div class="uk-width-small-1-1 uk-width-medium-1-1">
        <?php
            $user_grid = new WsModelGridView($user_model);
            $user_grid->show();
        ?>
    </div>
</div>

<div class="uk-grid">
    <div class="uk-width-small-1-1 uk-width-medium-1-2">
        <?php
            $roles_grid = new WsModelGridView($roles_model);
            $roles_grid->show();
        ?>
    </div>
    <div class="uk-width-small-1-1 uk-width-medium-1-2">
        <?php
            $perms_grid = new WsModelGridView($perms_model);
            $perms_grid->show();
        ?>
    </div>
</div>
