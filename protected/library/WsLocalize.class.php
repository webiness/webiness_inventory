<?php
/**
 * WsLocalize
 * Translates strings in application to specific language. Create language text
 * file in "lang" directory. Language file is in JSON format. For example:
 * create "lang/hr.txt" file for croatian translations, with example content:
 * <code>
 * {
 *      'message string':'prva poruka',
 *      'message string2':'druga poruka'
 * }
 * </code>
 *
 * Example usage:
 *
 * <code>
 * // this will display "prva poruka" if browser language is set to croatian
 * echo WsLocalize::msg('message string');
 * </code>
 *
 */
class WsLocalize
{
    public static function msg($str)
    {
        /* translation file
         * (static is used for ensure only one loading of file)
         */
        static $translations = NULL;

        if (is_null($translations)) {
            // language
            $lang = self::getLang();

            // language file
            $lang_file = WsROOT.'/lang/'.$lang.'.txt';

            // load default language file if specific language don't exist
            if (!file_exists($lang_file)) {
                // no translation file found; return unchanged message
                return $str;
            }

            /* Load the language file as a JSON object and transform it into
             * an associative array
             */
            $lang_file_content = file_get_contents($lang_file);
            $translations = json_decode($lang_file_content, true);
        }
        
        unset($lang_file_content, $lang_file);

        if (!empty($translations[$str])) {
            return $translations[$str];
        } else {
            return $str;
        }
    }


    /**
     * get current language
     *
     * @return string $lang
     *
     */
    public static function getLang()
    {
        $lang = substr(
            filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE',
                FILTER_SANITIZE_STRING), 0, 2);

        return($lang);
    }
}

