<?php


/**
 * WsAuthController
 * Controller for WsAuth module. It contains next views:
 *
 * * register
 * * login
 * * logout
 * * admin
 * * rolePerms
 * * userRoles
 * * edit
 * * verify
 *
 * @see WsController
 * @see WsAuth
 *
 */
class WsauthController
{
    /**
     * @var string $layout Name of layout file located in '/public/layouts'
     *
     */
    public $layout;
    /**
     * @var string $title Web page title
     *
     */
    public $title;
    /**
     * @var array $breadcrumbs List of links that indicate position in Web app
     *
     */
    public $breadcrumbs;
    /**
     * @var string $_action Name of controller action
     *
     */
    private $_action;
    /**
     * @var array $_params List of parameters that would be passed to the action
     *
     */
    private $_params = array();
    /**
     * @var WsAuth $_auth WsAuth instance
     *
     */
    private $_auth;


    public function __construct()
    {
        $this->layout = WsConfig::get('html_layout');
        $this->title = WsConfig::get('app_name');
        $this->breadcrumbs = array();

        $this->_auth = new WsAuth();
    }


    /**
     * Read 'view' and return its contet.
     *
     * @return string content of 'view'.
     *
     */
    private function renderView()
    {
        // file to render
        $fileName = WsROOT.'/protected/library/views/'
            .$this->_action.'.php';

        // extract parameters so that they can be used in view
        if (!empty($this->_params)) {
            extract($this->_params);
        }

        ob_start();

        if (is_file($fileName)) {
            include($fileName);
        } else {
            ob_get_clean();
            header('HTTP/1.1 500 Internal Server Error');
            trigger_error('The view file <strong>'.$fileName.
                '</strong> is not available.', E_USER_ERROR);
        }

        $content = ob_get_clean();

        return $content;
    }

    /**
     *
     * Renders controller action in web brovser
     *
     * @param $action string Optional. Name of action.If it is not set then calls 'index' action.
     * @param array $params {
     *     Optional. List of key=>value parameters that are passed to action
     * }
     *
     */
    private function render($action = 'admin', $params = array())
    {
        // name of action
        $this->_action = $action;

        $this->_params = $params;
        // $this->params = array_unique($this->params);

        // page title
        $WsTitle = $this->title;
        // breadcrumbs
        $WsBreadcrumbs = $this->breadcrumbs;
        // content to show
        $WsContent = self::renderView();

        // layout file
        $layoutFile = WsROOT.'/public/layouts/'.$this->layout;
        if (is_file($layoutFile)) {
            // show view in layaout if exists
            include($layoutFile);
        } else {
            // or if not exists, show the content of view, only
            echo $WsContent;
        }
    }


    /**
     * display registration form
     *
     */
    public function register($email=null, $password=null)
    {
        // breadcrumbs
        $this->breadcrumbs = array(
            WsLocalize::msg('home') => array(
                'site',
                'index'
            ),
            WsLocalize::msg('register') => array(
                'wsauth',
                'register'
            ),
        );

        $this->render('wsauth_register');
    }


    /**
     * Try to login user by it's email address and password and shows login
     * form if it fail or if login informations are not provided
     *
     * @param string $email User email address
     * @param string $password User password
     *
     */
    public function login($email=null, $password=null)
    {
        // breadcrumbs
        $this->breadcrumbs = array(
            WsLocalize::msg('home') => array(
                'site',
                'index'
            ),
            WsLocalize::msg('login') => array(
                'wsauth',
                'login'
            ),
        );

        $this->render('wsauth_login');
    }


    /**
     * logout current loged in user
     *
     */
    public function logout()
    {
        if ($this->_auth->checkSession()) {
            if ($this->_auth->logout()) {
                $this->render('wsauth_logout');
            }
        }
    }


    /**
     * administer user accounts
     *
     */
    public function admin()
    {
        if ($this->_auth->hasPermission('admin') != true) {
            trigger_error('Access denied', E_USER_ERROR);
            return;
        }

        // breadcrumbs
        $this->breadcrumbs = array(
            WsLocalize::msg('home') => array(
                'site',
                'index'
            ),
            WsLocalize::msg('auth') => array(
                'wsauth',
                'admin'
            ),
        );

        $user_model = new Ws_userModel();
        $roles_model = new Ws_rolesModel();
        $perms_model = new Ws_permissionsModel();

        $this->render('wsauth_admin', array(
            'user_model' => $user_model,
            'roles_model' => $roles_model,
            'perms_model' => $perms_model,
        ));
    }


    /**
     * manage permissions for roles
     *
     */
    public function rolePerms()
    {
        if ($this->_auth->hasPermission('admin') != true) {
            trigger_error('Access denied', E_USER_ERROR);
            return;
        }

        $this->render('wsauth_rolePerms');
    }


    /**
     * manage permissions for roles
     *
     */
    public function userRoles()
    {
        if ($this->_auth->hasPermission('admin') != true) {
            trigger_error('Access denied', E_USER_ERROR);
            return;
        }

        $this->render('wsauth_userRoles');
    }


    /**
     * edit user account for currently loged in user
     *
     */
    public function edit()
    {
        if (!$this->_auth->checkSession()) {
            return $user_model = null;
        } else {
            $user_model = new Ws_userModel();

            $condition = 'email=\''.$_SESSION['ws_auth_user_email'].'\'';
            $res = $user_model->search($condition);

            if ($res == false or $user_model->nRows != 1) {
                $user_model = null;
            }
        }

        $this->render('wsauth_edit', array(
            'user_model' => $user_model,
            'user_email' => $_SESSION['ws_auth_user_email']
        ));
    }


    /**
     * verify new user account
     *
     * @param string $verification_code Verification code
     *
     */
    public function verify($verification_code=null)
    {
        $user_model = new Ws_userModel();

        // check if verification code exists in database
        $condition = 'verification_code=\''.$verification_code.'\' AND '
            .'is_verified = \'f\'';
        $res = $user_model->search($condition);

        if ($res == false or $user_model->nRows != 1) {
            header('HTTP/1.1 401 Unauthorized');
            trigger_error(WsLocalize::msg('Invalid verification code'),
                E_USER_ERROR);
        } else {
            // verify account
            $user_model->is_verified = true;
            $user_model->is_active = true;
            $user_model->save();

            // login new user
            $auth = new WsAuth();
            $auth->login($user_model->email, $user_model->password);
        }

        $this->render('wsauth_verifyed');
    }
}
