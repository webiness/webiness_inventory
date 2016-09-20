<div class="row">
    <div class="col-sm-12">
        <h3 class="text-center">
            <?php echo WsConfig::get('app_name'); ?>
            -
            <?php echo WsLocalize::msg('Role Permissions'); ?>
        </h3>
    </div>
</div>

<?php
// database object
$db = new WsDatabase();

if (filter_input(INPUT_POST, 'permission', FILTER_SANITIZE_STRING) !== NULL) {
    $permissions = $_POST['permission'];

    // truncate ws_role_perm table
    $db->execute('TRUNCATE TABLE ws_role_perm');

    // set new role permissions
    foreach($permissions as $role => $permission) {
        foreach ($permission as $perm => $value) {
            if ($value === 'true' or $value === "on") {
                $db->execute('INSERT INTO ws_role_perm'
                    .' VALUES (:role_id, :permissions_id)', array(
                        'role_id' => intval($role),
                        'permissions_id' => intval($perm)
                ));
            }
        }
    }
    $status = WsLocalize::msg('Role permissions successfully updated');
} else {
    $status = '';
}

if ($status !== '') {
?>
<div class="row">
    <div class="alert alert-success">
        <?php echo $status; ?>
    </div>
</div>
<?php
}


// list of roles
$roles = new Ws_rolesModel();
$list_of_roles = $roles->getAll();

// list of permissions
$permissions = new Ws_permissionsModel();
$list_of_permissions = $permissions->getAll();

// role permissions
$sql = 'SELECT role_id, permissions_id FROM ws_role_perm'
    .' WHERE role_id=:roles_id AND permissions_id=:permissions_id';

// display form for editing permissions per role
echo '<form method="post" action="'.WsUrl::link('wsauth','rolePerms').'">';
$rp_form = new WsForm(WsUrl::link('wsauth','rolePerms'));
$rp_form->submitButtonText = WsLocalize::msg('Save');

if ($roles->nRows >= 1 and $permissions->nRows >= 1) {
    foreach ($list_of_roles as $lr) {
        echo '<div class="row">';
        echo '<div class="col-sm-12 col-md-10 col-md-offset-1">';

        echo '<br/>';
        echo '<h4 class="text-primary">'.$lr['name'];
        echo ' ('.$lr['description'].')</h4>';
        echo '<input type="hidden" name="role_id" value="'.$lr['id'].'"/>';

        foreach ($list_of_permissions as $lp) {
            $db->query($sql, array(
                'roles_id' => $lr['id'],
                'permissions_id' => $lp['id']
            ));

            if ($db->nRows == 1) {
                $hasPermission = true;
            } else {
                $hasPermission = false;
            }
            
            echo '<div class="form-group">';
            echo '<div class="checkbox">';
            echo '<label for="permission['
                .$lr['id'].']['.$lp['id'].']">';
            echo '<input type="checkbox" name="permission['
                .$lr['id'].']['.$lp['id'].']" ';
            if ($hasPermission) {
                echo 'checked/>';
            } else {
                echo '/>';
            }
            echo $lp['name'].' ('.$lp['description'].')';
            echo '</label>';
            echo '</div></div>';
        }

        echo '</div></div>';
    }

    echo '<div class="text-center">';
    echo '<input class="btn btn-success" type="submit"'
        .' value="'.WsLocalize::msg('Save').'"/>';
    echo '</div>';

    echo '</form>';
}

unset($db, $roles, $list_of_roles, $permissions, $list_of_permissions, $rp_form);
