<?php


/**
 * Ws_permissionsModel
 * Stores permission name records for role based access control module.
 * 
 * @see WsAuth
 * @see Ws_rolesModel
 * @see Ws_userModel
 * @see Ws_role_permModel
 * @see Ws_user_roleModel
 * 
 */ 
class Ws_permissionsModel extends WsModel
{
    public function __construct()
    {
        parent::__construct();

        // set metaName for displaying in grid and form
        $this->metaName = WsLocalize::msg('Permissions');

        // column headers for grid and form
        $this->columnHeaders = array(
            'id' => WsLocalize::msg('permission id'),
            'name' => WsLocalize::msg('permission name'),
            'description' => WsLocalize::msg('description of permission')
        );
    }
}

