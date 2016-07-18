<?php


/**
 * Ws_user_roleModel
 * Store conecction user=>role for role based access control module.
 * 
 * @see WsAuth
 * @see Ws_rolesModel
 * @see Ws_permissionsModel
 * @see Ws_role_permModel
 * @see Ws_userModel
 * 
 */ 
class Ws_user_roleModel extends WsModel
{
    public function __construct()
    {
        parent::__construct();

        // set foreign keys properties
        $this->foreignKeys['user_id']['table'] = 'ws_user';
        $this->foreignKeys['user_id']['column'] = 'id';
        $this->foreignKeys['user_id']['display'] = 'email';
        $this->foreignKeys['role_id']['table'] = 'ws_roles';
        $this->foreignKeys['role_id']['column'] = 'id';
        $this->foreignKeys['role_id']['display'] = 'name';
    }
}

