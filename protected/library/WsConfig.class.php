<?php
/**
 * WsConfig
 * Sets or fetch config variables. Good practice is to define all default config
 * variables in 'protected/config/config.php' file. 
 *
 * Example usage:
 *
 * <code>
 * // get database driver name
 * $cs = WsConfig::get('db_driver');
 *
 * // set database host name to localhost
 * WsConfig::set('db_host', 'localhost');
 * </code>
 * 
 */
class WsConfig
{
    private static $vars = array();

    /**
     * Sets global configuration variable.
     *
     * @param string $_name Variable name
     * @param string $_value Variable value
     * @return boolean True on success
     */
    public static function set($_name, $_value)
    {
        self::$vars[$_name] = $_value;
        return true;
    }


    /**
     * Fetch global configuration variable.
     *
     * @param string $_name Variable name
     * @return string Value of variable
     * 
     */
    public static function get($_name)
    {
        if(array_key_exists($_name, self::$vars)) {
            return self::$vars[$_name];
        } else {
            return '';
        }
    }


    /**
     * Check if config variable exists
     *
     * @param string $_name Variable name
     * @return boolean True or False
     * 
     */
    public static function exists($_name)
    {
        if(array_key_exists($_name, self::$vars)) {
            return true;
        }

        return false;
    }
}
