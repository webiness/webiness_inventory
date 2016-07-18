<?php


/**
 * Ws_role_permModel
 * Store connections role=>permission for role based access control module.
 * 
 * @see WsAuth
 * @see Ws_rolesModel
 * @see Ws_permissionsModel
 * @see Ws_userModel
 * @see Ws_user_roleModel
 * 
 */ 
class Ws_role_permModel extends WsModel
{
    public function __construct()
    {
        parent::__construct();

        // set foreign keys properties
        $this->foreignKeys['role_id']['table'] = 'ws_roles';
        $this->foreignKeys['role_id']['column'] = 'id';
        $this->foreignKeys['role_id']['display'] = 'name';
        $this->foreignKeys['permissions_id']['table'] = 'ws_permissions';
        $this->foreignKeys['permissions_id']['column'] = 'id';
        $this->foreignKeys['permissions_id']['display'] = 'email';
    }
}

