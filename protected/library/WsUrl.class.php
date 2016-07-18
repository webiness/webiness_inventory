<?php
/**
 * WsUrl
 * Constructs URLs.
 *
 * Example usage:
 *
 * <code>
 * # returns '/server_address/site/index/'
 * # returns '/server_address/index.php?request=site/index'
 * # servers.
 * $url = WsUrl::link('site', 'index');
 *
 * # returns '/server_address/site/edit_item/id=2/'
 * # returns '/server_address/index.php?request=site/index&id=2'
 * $url = WsUrl::link('site', 'index', array('id'=>2));
 *
 * # points to file '/server_name/public/js/jquery.min.js'
 * $asset = WsUrl::asset('js/jquery.min.js');
 * </code>
 * 
 */
class WsUrl
{
    /**
     * Constructs URL address.
     *
     * If web page is served by Apache HTTP server then uses mod_rewrite and
     * .htaccess file to construct prety urls. On other web servers returns
     * standard url string in form '/index.php?request='.
     *
     * @param string $controller Name of controller.
     * @param string $action Name of controller action.
     * @param array $params Optional parameters
     * @return string $url
     * 
     */
    public static function link($controller, $action, $params = array())
    {
        // if we have support for semantic (pretty) urls
        if (WsConfig::get('pretty_urls') == 'yes'){
            $url = WsSERVER_ROOT.'/'.$controller.'/'.$action.'/';
            if (count($params) > 0) {
                while ($name = current($params)) {
                    $url .= urlencode($name).'/';
                    next($params);
                }
            }
        } else {
            $url = WsSERVER_ROOT.'/index.php?request='
                .$controller.'/'.$action.'/';
            if (count($params) > 0) {
                while ($name = current($params)) {
                    $url = $url.'&'.urlencode(key($params)).'='.urlencode($name);
                    next($params);
                }
            }
        }

        return $url;
    }


    /**
     * Construct URLs for static files.
     *
     * Static files are placed in /public directory.
     *
     * @param string $file File name
     * @return string $asset
     * 
     */
    public static function asset($file)
    {
        $fileName = WsROOT.'/public/'.$file;
        $asset = '';

        if (file_exists($fileName)) {
            $asset = WsSERVER_ROOT.'/public/'.$file;
        }

        return $asset;
    }
}

