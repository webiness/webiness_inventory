<?php


/**
 * Ws_userModel
 * Store user records for role based access control module.
 * 
 * @see WsAuth
 * @see Ws_rolesModel
 * @see Ws_permissionsModel
 * @see Ws_role_permModel
 * @see Ws_user_roleModel
 * 
 */ 
class Ws_userModel extends WsModel
{
    public function __construct()
    {
        parent::__construct();

        // mysql/maridb don't detect boolean type properly
        $this->columnType['is_verified'] = 'bool_type';
        $this->columnType['is_active'] = 'bool_type';

        // don't show user salt and verification code in admin or
        // edit forms
        $this->columnType['user_salt'] = 'hidden_type';
        $this->columnType['verification_code'] = 'hidden_type';
        $this->hiddenColumns = array(
            'user_salt',
            'verification_code',
        );

        // set metaName for displaying in grid and form
        $this->metaName = WsLocalize::msg('User Accounts');

        // column headers for grid and form
        $this->columnHeaders = array(
            'id' => WsLocalize::msg('user id'),
            'email' => WsLocalize::msg('mail address'),
            'password' => WsLocalize::msg('password'),
            'is_verified' => WsLocalize::msg('verified account?'),
            'is_active' => WsLocalize::msg('active account?'),
        );
    }


    /**
     * Returns random string of specific length.
     *
     * @param integer $length Length of string.
     * @return string $string Random sting
     *
     */
    public function randomString($length = 50)
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        $string = '';

        for ($p = 0; $p < $length; $p++) {
            $string .= $chars[mt_rand(0, strlen($chars)-1)];
        }

        return $string;
    }


    /**
     * Encript data with sha512 algorithm
     *
     * @param string $data Data to encript
     * @return string $data Encripted data
     *
     */
    public function hashData($data)
    {
        return hash_hmac('sha512', $data, $this->user_salt);
    }


    /**
     * this function is called before every save() to ensure that password is
     * encripted
     *
     * @return boolean
     *
     */
    public function beforeSave()
    {
        // admin account is always verified and active
        if ($this->email == WsConfig::get('auth_mail')) {
            if (WsConfig::get('db_driver') == 'pgsql') {
                $user_model->is_verified = 't';
                $user_model->is_active = 't';
            } else {
                $user_model->is_verified = 1;
                $user_model->is_active = 1;
            }
        }

        /* prepare password and verification code
         * generate user salt
         */
        $this->user_salt = $this->randomString();;
        // salt and hash the password
        $password = $this->user_salt.$this->password;
        $password = $this->hashData($password);

        $this->password = $password;

        return true;
    }


    /*
     * this function is called before every delete() to ensure that nobody can
     * delete administrator's account
     *
     * @return boolean
     *
     */
    public function beforeDelete()
    {

        // prevent removal of admin user account
        if ($this->email == WsConfig::get('auth_mail')) {
            return false;
        }

        return true;
    }
}
