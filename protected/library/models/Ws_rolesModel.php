<?php


/**
 * Ws_rolesModel
 * Store role name recods for role based access control module.
 * 
 * @see WsAuth
 * @see Ws_userModel
 * @see Ws_permissionsModel
 * @see Ws_role_permModel
 * @see Ws_user_roleModel
 * 
 */ 
class Ws_rolesModel extends WsModel
{
    public function __construct()
    {
        parent::__construct();

        // set metaName for displaying in grid and form
        $this->metaName = WsLocalize::msg('User Roles');

        // column headers for grid and form
        $this->columnHeaders = array(
            'id' => WsLocalize::msg('role id'),
            'name' => WsLocalize::msg('role name'),
            'description' => WsLocalize::msg('role description'),
        );
    }
}

