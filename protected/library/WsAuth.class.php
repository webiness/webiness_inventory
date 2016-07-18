<?php
/**
 * WsAuth
 * Base class for user authentication.
 *
 * Example usage:
 *
 * <code>
 * $auth = new WsAuth();
 *
 * # check if any user is logged in
 * if ($auth->checkSessioun()) {
 * .
 * .
 * .
 * }
 *
 * # check if logged in user has specific permission
 * if ($auth->hasPermission('perm_name')) {
 * .
 * .
 * .
 * }
 * </code>
 *
 */
class WsAuth
{
    /**
     * checks if database object is available. Database is needed for WsAuth
     * module.
     */
    public $isUsable;
    private $_db;


    public function __construct()
    {
        // connect to database
        $this->_db = new WsDatabase();

        if (!$this->_db->isConnected) {
            $this->isUsable = false;
            return false; // cant't use WsAuth if there is no db connection
        } else {
            $this->isUsable = true;
        }

        // auth database tables for PostgreSQL server
        if (WsConfig::get('db_driver') === 'pgsql') {
            $create_table = '
CREATE TABLE IF NOT EXISTS ws_user (
    id SERIAL PRIMARY KEY,
    email VARCHAR(128) NOT NULL,
    password VARCHAR(128) NOT NULL,
    user_salt VARCHAR(50) NOT NULL,
    is_verified BOOLEAN NOT NULL,
    is_active BOOLEAN NOT NULL,
    verification_code VARCHAR(65) NOT NULL
);
';
            $this->_db->execute($create_table);
            $create_table = '
CREATE TABLE IF NOT EXISTS ws_logged_in (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES ws_user(id),
    session_id CHAR(32) NOT NULL,
    token CHAR(128) NOT NULL
);
';
            $this->_db->execute($create_table);
            $create_table = '
CREATE TABLE IF NOT EXISTS ws_roles (
    id SERIAL PRIMARY KEY,
    name VARCHAR(25) NOT NULL,
    description VARCHAR(50)
);
';
            $this->_db->execute($create_table);
            $create_table = '
CREATE TABLE IF NOT EXISTS ws_permissions (
    id SERIAL PRIMARY KEY,
    name VARCHAR(25) NOT NULL,
    description VARCHAR(50)
);
';
            $this->_db->execute($create_table);
            $create_table = '
CREATE TABLE IF NOT EXISTS ws_role_perm (
    role_id INTEGER NOT NULL REFERENCES ws_roles(id),
    permissions_id INTEGER NOT NULL REFERENCES ws_permissions(id)
);
';
            $this->_db->execute($create_table);
            $create_table = '
CREATE TABLE IF NOT EXISTS ws_user_role (
    user_id INTEGER NOT NULL REFERENCES ws_user(id),
    role_id INTEGER NOT NULL REFERENCES ws_roles(id)
);
';
            $this->_db->execute($create_table);

        // auth tables for MariaDB/MySql database server
        } else if (WsConfig::get('db_driver') === 'mysql') {
            $create_table = '
CREATE TABLE IF NOT EXISTS ws_user (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(128) NOT NULL,
    password VARCHAR(128) NOT NULL,
    user_salt VARCHAR(50) NOT NULL,
    is_verified BOOLEAN NOT NULL,
    is_active BOOLEAN NOT NULL,
    verification_code VARCHAR(65) NOT NULL
);
';
            $this->_db->execute($create_table);
            $create_table = '
CREATE TABLE IF NOT EXISTS ws_logged_in (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    user_id INTEGER NOT NULL,
    session_id CHAR(32) NOT NULL,
    token CHAR(128) NOT NULL,

    FOREIGN KEY (user_id) REFERENCES ws_user(id)
);
';
            $this->_db->execute($create_table);
            $create_table = '
CREATE TABLE IF NOT EXISTS ws_roles (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(25) NOT NULL,
    description VARCHAR(50)
);
';
            $this->_db->execute($create_table);
            $create_table = '
CREATE TABLE IF NOT EXISTS ws_permissions (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(25) NOT NULL,
    description VARCHAR(50)
);
';
            $this->_db->execute($create_table);
            $create_table = '
CREATE TABLE IF NOT EXISTS ws_role_perm (
    role_id INTEGER NOT NULL,
    permissions_id INTEGER NOT NULL,

    FOREIGN KEY (role_id) REFERENCES ws_roles(id),
    FOREIGN KEY (permissions_id) REFERENCES ws_permissions(id)
);
';
            $this->_db->execute($create_table);
            $create_table = '
CREATE TABLE IF NOT EXISTS ws_user_role (
    user_id INTEGER NOT NULL,
    role_id INTEGER NOT NULL,

    FOREIGN KEY (user_id) REFERENCES ws_user(id),
    FOREIGN KEY (role_id) REFERENCES ws_roles(id)
);
';
            $this->_db->execute($create_table);

        // auth tables for sqlite database
        } else if (WsConfig::get('db_driver') === 'sqlite') {
            $create_table = '
CREATE TABLE IF NOT EXISTS ws_user (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email VARCHAR(128) NOT NULL,
    password VARCHAR(128) NOT NULL,
    user_salt VARCHAR(50) NOT NULL,
    is_verified BOOLEAN NOT NULL,
    is_active BOOLEAN NOT NULL,
    verification_code VARCHAR(65) NOT NULL
);
';
            $this->_db->execute($create_table);
            $create_table = '
CREATE TABLE IF NOT EXISTS ws_logged_in (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    session_id CHAR(32) NOT NULL,
    token CHAR(128) NOT NULL,

    FOREIGN KEY (user_id) REFERENCES ws_user(id)
);
';
            $this->_db->execute($create_table);
            $create_table = '
CREATE TABLE IF NOT EXISTS ws_roles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(25) NOT NULL,
    description VARCHAR(50)
);
';
            $this->_db->execute($create_table);
            $create_table = '
CREATE TABLE IF NOT EXISTS ws_permissions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(25) NOT NULL,
    description VARCHAR(50)
);
';
            $this->_db->execute($create_table);
            $create_table = '
CREATE TABLE IF NOT EXISTS ws_role_perm (
    role_id INTEGER NOT NULL,
    permissions_id INTEGER NOT NULL,

    FOREIGN KEY (role_id) REFERENCES ws_roles(id),
    FOREIGN KEY (permissions_id) REFERENCES ws_permissions(id)
);
';
            $this->_db->execute($create_table);
            $create_table = '
CREATE TABLE IF NOT EXISTS ws_user_role (
    user_id INTEGER NOT NULL,
    role_id INTEGER NOT NULL,

    FOREIGN KEY (user_id) REFERENCES ws_user(id),
    FOREIGN KEY (role_id) REFERENCES ws_roles(id)
);
';
            $this->_db->execute($create_table);
        }

        // create admin user account if it doesent exists
        $this->createAdminUser();

        unset($create_table);
    }


    /**
     * Create administrator user account if it doesent exist.
     * Default password is set to 'admin'
     *
     */
    private function createAdminUser()
    {
        if (!$this->isUsable) {
            return false;
        }

        // get administrato mail addres from config file
        $email = WsConfig::get('auth_admin');

        // add new user to database
        $user_model = new Ws_userModel();

        // check if administrator account is allready created
        $user_model->search("email='$email'");
        if ($user_model->nRows > 0) {
            return WS_AUTH_USER_EXISTS;
        }

        // add new user to database
        $user_model->email = $email;
        $user_model->is_verified = true;
        $user_model->is_active = true;
        $user_model->password = 'admin';
        $user_model->verification_code = $user_model->randomString(65);

        $user_model->save();

        return true;
    }


    /**
     * Create new user record.
     *
     * @param string $email User email
     * @param string $password User password
     * @return boolean
     */
    public function createUser($email, $password)
    {
        if (!$this->isUsable) {
            return false;
        }

        // add new user to database
        $user_model = new Ws_userModel();

        // check if email allready exists
        $user_model->search("email='$email'");
        if ($user_model->nRows > 0) {
            return WS_AUTH_USER_EXISTS;
        }

        // add new user to database
        $user_model->email = $email;
        if (WsConfig::get('db_driver') == 'pgsql') {
            $user_model->is_verified = 'f';
            $user_model->is_active = 'f';
        } else {
            $user_model->is_verified = 0;
            $user_model->is_active = 0;
        }
        $user_model->password = $password;
        $verification_code = $user_model->randomString(65);
        $user_model->verification_code = $verification_code;

        if ($user_model->save()) {
            if (
                filter_input(INPUT_SERVER, 'SERVER_NAME', FILTER_SANITIZE_STRING)
                    == 'localhost') {
                $server = filter_input(INPUT_SERVER, 'SERVER_ADDR',
                    FILTER_SANITIZE_STRING);
            } else {
                $server = filter_input(INPUT_SERVER, 'SERVER_NAME',
                    FILTER_SANITIZE_URL);
            }
            $link = 'http://'.$server
                .WsUrl::link('Wsauth', 'verify', array(
                    'code'=>$verification_code
                ));

            // send verification mail
            $to = $email;
            // subject
            $subject = WsConfig::get('app_name')
                .WsLocalize::msg(' - Thank you for registering');
            // content
            $html = "
            <HTML>
                <HEAD>
                    <TITLE>$subject</TITLE>
                </HEAD>
                <BODY>
                    Welcome,
                    <BR/>
                    <BR/>
            ";
            $html .= '<p>';
            $html .= WsLocalize::msg('We sent you this email to verify your email address.');
            $html .= WsLocalize::msg('To complete user registration, click on the following link');
            $html .= '</p>';
            $html .= '<p><a href="'.$link.'">';
            $html .= WsLocalize::msg('finish registration');
            $html .= '</a></p>'.$link;
            $html .= '<br/>';
            $html .= WsLocalize::msg('Thank you and welcome.');
            $html .= '</BODY></HTML>';
            // To send HTML mail, the Content-type header must be set
            $headers  = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=utf-8\r\n";
            $headers .= 'From: <'
                .WsConfig::get('auth_admin').'>'."\r\n";

            // send user verification mail
            mail($to, $subject, $html, $headers);
            return true;
        } else {
            return false;
        }
    }


    /**
     * Login existing user and star user session.
     *
     * @param string $email User email address
     * @param string $password User password
     * @return boolean
     *
     */
    public function login($email, $password)
    {
        // check for database connection
        if (!$this->isUsable) {
            return false;
        }

        $user_model = new Ws_userModel();
        $user_model->search("email='$email'");

        if ($user_model->nRows != 1) {
            return WS_AUTH_NO_MATCH;
        }

        // Salt and hash password for checking
        $password1 = $user_model->user_salt.$password;
        $password2 = $user_model->hashData($password1);

        // check for valid username(email)/password combination
        $user_model->search("email='$email' AND password='$password2'");

        if ($user_model->nRows != 1) {
            return WS_AUTH_NO_MATCH;
        }

        // check if user account is verified and actived.
        $is_active = (boolean)$user_model->is_active;
        $is_verified = (boolean)$user_model->is_verified;

        // account is not verified, send user verification email.
        if (!$is_verified) {
            // send verification mail
            return WS_AUTH_NOT_VERIFIED;
        }

        // account is not active/
        if (!$is_active) {
           return WS_AUTH_NOT_ACTIVE;
        }

        // all OK; login user
        $random = $user_model->randomString();
        // build the session token
        $token = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT',
            FILTER_SANITIZE_STRING).$random;
        $token = $user_model->hashData($token);
        // user id
        $user_id = $user_model->id;
        // session id
        $session_id = session_id();

        // set session variables
        $_SESSION['ws_auth_token'] = $token;
        $_SESSION['ws_auth_user_id'] = $user_id;
        $_SESSION['ws_auth_user_email'] = $user_model->email;
        $_SESSION['ws_auth_generated_time'] = time();
        $_SESSION['ws_auth_client_ip'] =
            filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_SANITIZE_STRING);
        $_SESSION['ws_auth_user_agent'] =
            filter_input(INPUT_SERVER,'HTTP_USER_AGENT',FILTER_SANITIZE_STRING);

        $logged_in_model = new Ws_logged_inModel();
        // delete old logged_in record from database
        $logged_in_model->search("user_id=$user_id");
        $logged_in_model->delete();

        // save new logged_in record to database
        $logged_in_model->user_id = $user_id;
        $logged_in_model->session_id = $session_id;
        $logged_in_model->token = $token;
        $logged_in_model->save();

        return WS_AUTH_LOGIN_OK;
    }


    /**
     * Checks current user session.
     *
     * @return boolean
     *
     */
    public function checkSession()
    {
        if (!isset($_SESSION['ws_auth_user_id'])) {
            return false;
        }

        // check if session is expired (1 hour)
        if(!isset($_SESSION['ws_auth_generated_time'])) {
            return false;
        }
        if (time() > ($_SESSION['ws_auth_generated_time'] + 3600)) {
            return false;
        }

        // validate ip address
        if(!isset($_SESSION['ws_auth_client_ip'])) {
            return false;
        }
        if ($_SESSION['ws_auth_client_ip'] !==
            filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_SANITIZE_STRING)) {
                return false;
        }

        // validate user agent
        if(!isset($_SESSION['ws_auth_user_agent'])) {
            return false;
        }
        if ($_SESSION['ws_auth_user_agent'] !==
            filter_input(INPUT_SERVER, 'HTTP_USER_AGENT',
                FILTER_SANITIZE_STRING)) {
                return false;
        }

        // check is user is logged in
        $user_id = $_SESSION['ws_auth_user_id'];
        $logged_in_model = new Ws_logged_inModel();
        $logged_in_model->search("user_id=$user_id");

        // if user is logged in
        if ($logged_in_model->nRows >= 1) {
            // check session ID and Token
            if(session_id() == trim($logged_in_model->session_id)
                && $_SESSION['ws_auth_token'] == trim($logged_in_model->token)){

                    // expand expiration time for 1 hour
                    $_SESSION['ws_auth_generated_time'] = time();

                    /** Id and token match, refresh the session
                     * for the next request
                     */
                    return true;
            }
        }

        return false;
    }


    /*
     * Logout current user from session
     *
     * @return boolean
     *
     */
    public function logout()
    {
        if (!$this->checkSession()) {
            return false;
        }

        $user_id = $_SESSION['ws_auth_user_id'];
        $logged_in_model = new Ws_logged_inModel();
        // delete logged_in record from database
        $logged_in_model->search("user_id=$user_id");
        $logged_in_model->delete();

        // delete session records
        $_SESSION = array();

        session_destroy();

        return true;
    }


    /**
     * check if user has specific permission
     *
     * @param string $perm Permission name
     * @return boolean
     *
     */
    public function hasPermission($perm)
    {
        // if no user is logged in then return false
        if (!$this->checkSession()) {
            return false;
        }

        // check if admin user is logged in
        if ($_SESSION['ws_auth_user_email'] === WsConfig::get('auth_admin')) {
            return true;
        } else if ($perm === 'admin' and (
            $_SESSION['ws_auth_user_email'] === WsConfig::get('auth_admin')
            )) {
            return true;
        }

        $db = new WsDatabase();
        $user_id = $_SESSION['ws_auth_user_id'];

        // check if user has permission
        $sql = '
            SELECT
                wp.id
            FROM ws_permissions wp,
                ws_role_perm rp,
                ws_roles wr,
                ws_user_role wur,
                ws_user wu
            WHERE wp.id=rp.permissions_id
                AND rp.role_id=wr.id
                AND wr.id=wur.role_id
                AND wur.user_id=wu.id
                AND wu.id=:user_id
                AND wp.name=:perm
            GROUP BY wp.id
        ';
        $db->query($sql, array(
            'user_id' => $user_id,
            'perm' => $perm
        ));

        if ($db->nRows >= 1) {
            return true;
        }

        return false;
    }


    /**
     * return email address of current logged in user
     *
     * @return string
     *
     */
    public function currentUser()
    {
        // if no user is logged in then return empty string
        if (!$this->checkSession()) {
            return '';
        }

        return($_SESSION['ws_auth_user_email']);
    }


    /**
     * return ID of current logged in user
     *
     * @return integer
     *
     */
    public function currentUserID()
    {
        // if no user is logged in then return empty string
        if (!$this->checkSession()) {
            return -1;
        }

        return($_SESSION['ws_auth_user_id']);
    }
}

