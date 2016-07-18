<div class="row">
    <div class="column column-12">
        <h3 class="text-center">
            <?php echo WsConfig::get('app_name'); ?>
            -
            <?php echo WsLocalize::msg('User Roles'); ?>
        </h3>
    </div>
</div>

<?php
// database object
$db = new WsDatabase();

if (filter_input(INPUT_POST, 'role') !== NULL) {
    $roles = $_POST['role'];

    // truncate ws_user_role table
    $db->execute('TRUNCATE TABLE ws_user_role');

    // set new user roles
    foreach($roles as $user => $role) {
        foreach ($role as $r => $value) {
            if ($value === 'true' or $value === 'on') {
                $db->execute('INSERT INTO ws_user_role'
                    .' VALUES (:user_id, :role_id)', array(
                        'user_id' => intval($user),
                        'role_id' => intval($r)
                ));
            }
        }
    }
    $status = WsLocalize::msg('User roles successfully updated');
} else {
    $status = '';
}

if ($status !== '') {
?>
<div class="row">
    <div class="callout success">
        <?php echo $status; ?>
    </div>
</div>
<?php
}


// list of users
$users = new Ws_userModel();
$list_of_users = $users->getAll();

// list of roles
$roles = new Ws_rolesModel();
$list_of_roles = $roles->getAll();

// role permissions
$sql = 'SELECT user_id, role_id FROM ws_user_role'
    .' WHERE user_id=:user_id AND role_id=:role_id';

// display form for editing user roles
echo '<form method="post" action="'.WsUrl::link('wsauth','userRoles').'">';

if ($users->nRows >= 1 and $roles->nRows >= 1) {
    foreach ($list_of_users as $lu) {
        // skip admin user
        if ($lu['email'] === $_SESSION['ws_auth_user_email']) {
            continue;
        }

        echo '<div class="row">';
        echo '<div class="column column-8 column-offset-2">';

        echo '<br/>';
        echo '<span class="label">'.$lu['email'].'</span>';
        echo '<input type="hidden" name="user_id" value="'.$lu['id'].'"/>';

        foreach ($list_of_roles as $lr) {
            $db->query($sql, array(
                'user_id' => $lu['id'],
                'role_id' => $lr['id']
            ));

            if ($db->nRows == 1) {
                $hasRole = true;
            } else {
                $hasRole = false;
            }

            echo '<label>';
            echo '<input type="checkbox" name="role['
                .$lu['id'].']['.$lr['id'].']" ';
            if ($hasRole) {
                echo 'checked/>';
            } else {
                echo '/>';
            }
            echo '<span>'.$lr['name'].' ('.$lr['description'].')</span>';
            echo '</label>';
        }

        echo '</div></div>';
    }

    echo '<div class="text-center">';
    echo '<input class="button success" type="submit"'
        .' value="'.WsLocalize::msg('Save').'"/>';
    echo '</div>';

    echo '</form>';
}

unset($db, $users, $list_of_users, $roless, $list_of_roles);
