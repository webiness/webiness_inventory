<?php


/**
 * Ws_userModel
 * Store records of current logged in users.
 * 
 * @see WsAuth
 * @see Ws_rolesModel
 * @see Ws_permissionsModel
 * @see Ws_role_permModel
 * @see Ws_user_roleModel
 * 
 */ 
class Ws_logged_inModel extends WsModel
{
    public function __construct()
    {
        parent::__construct();

        // set foreign keys properties
        $this->foreignKeys['user_id']['table'] = 'ws_user';
        $this->foreignKeys['user_id']['column'] = 'id';
        $this->foreignKeys['user_id']['display'] = 'email';
    }
}

