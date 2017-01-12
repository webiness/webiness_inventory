<?php

/**
 * WsController
 * Is controller part of Model-View-Controller. WsController manages a set of
 * actions which deal with the corresponding user requests. Through the actions,
 * WsController coordinates the data flow between models and views.
 *
 * Example usage:
 *
 * <code>
 * class SiteController extends WsController
 * {
 *     // this function handles request to 'http://server_name/site/index'
 *     public function index()
 *     {
 *         // use variable $m in view file
 *         $m = 'this is test';
 *
 *         // view file 'index.php' is placed in directory
 *         // '/application/views/site'
 *         $this->render('index', array('m' => $m));
 *     }
 * }
 * </code>
 *
 */
class WsController {

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

    public function __construct() {
        $this->layout = WsConfig::get('html_layout');
        $this->title = WsConfig::get('app_name');
        $this->breadcrumbs = array();
    }

    /**
     * Read 'view' and return its contet.
     *
     * @return string content of 'view'.
     *
     */
    private function renderView() {
        // get directory name which contains view file of controller
        $className = get_class($this);
        $dirName = strtolower(
            substr($className, 0, strpos($className, 'Controller'))
        );

        // file to render
        $fileName = WsROOT . '/application/views/' . $dirName . '/'
            . $this->_action . '.php';

        // extract parameters so that they can be used in view
        if (!empty($this->_params)) {
            extract($this->_params);
        }

        if (!ob_start('ob_gzhandler')) {
            ob_start();
        }

        if (is_file($fileName)) {
            include($fileName);
        } else {
            ob_get_clean();
            trigger_error(get_class($this)
                . ': The view file <strong>' . $fileName
                . '</strong> is not available.', E_USER_ERROR);
        }

        $content = ob_get_clean();

        return $content;
    }

    /**
     *
     * Renders controller action in web brovser
     *
     * @param $action string Optional name of action.
     * @param array $params {
     *     Optional list of key=>value parameters that are passed to action
     * }
     *
     */
    public function render($action = 'index', $params = array()) {
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

        unset($WsTitle, $WsBreadcrumbs, $layoutFile, $WsContent);
        unset($this->_action, $this->_params, $this->title, $this->breadcrumbs);
        unset($this->layout, $params, $action);
    }

    /**
     *
     * Prepares string for usage in CSV output
     *
     * @param string $string Text that will be send to CSV file
     * @return string CSV encoded sting
     *
     */
    public function encodeCSVField($string) {
        if (
            strpos($string, ',') !== false ||
            strpos($string, '"') !== false ||
            strpos($string, "\n") !== false
        ) {
            $string = '"' . str_replace('"', '""', $string) . '"';
        }

        return $string;
    }

    /**
     *
     * Get HTTP request method. It's used for implementation of RESTful API.
     *
     * @return string HTTP method
     *
     */
    public function getRequestMethod() {
        $method = filter_input(INPUT_SERVER, 'REQUEST_METHOD',
            FILTER_SANITIZE_STRING);

        if ($method == 'POST'
            and array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {

            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $method = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $method = 'PUT';
            } else {
                $method = 'POST';
            }
        }

        return ($method);
    }

    /**
     * Send HTTP response with status and JSON data. It's used for
     * implementation of RESTful API.
     *
     * @param mixed $data JSON data to send
     * @param integer $status HTTP status code
     *
     */
    public function sendResponse($data, $status = 200) {
        // list of supported response codes
        $response_code = array(
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            204 => 'No Content',
            403 => 'Forbiden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        );

        if (!array_key_exists($status, $response_code)) {
            $status = 500;
        }

        // send HTTP header and data
        header('Access-Control-Allow-Orgin: *');
        header('Access-Control-Allow-Methods: *');
        header('Content-Type: application/json');
        header('HTTP/1.1 ' . $status . ' ' . $response_code[$status]);
        echo json_encode($data);

        unset($response_code, $status, $data);
    }

    /**
     * Redirect HTTP request
     *
     * @param string $controller Controller name
     * @param string $action Action name
     * @param array $params Optional parameters
     *
     */
    public function redirect($controller, $action = 'index', $params = array()) {
        $url = WsUrl::link($controller, $action, $params);

        header('Location: ' . $url);
        unset($url);
    }

    /**
     *
     * check if call is ajax request
     *
     * @return boolean isAjax
     *
     */
    public function isAjax() {
        return (
            isset(
                $_SERVER['HTTP_X_REQUESTED_WITH']
            ) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
            );
    }

}
