<?php
/**
 * WsImage
 * Provides simple interface for working with images using GD library.
 *
 * Example usage:
 *
 * <code>
 * $img = new WsImage()
 *
 * # load image from file
 * $img->read('public/img/webiness_white.png');
 *
 * # add wattermark to image
 * $img->addWatermark('copyright (c) me@myaddress.com');
 *
 * # show image thumbnail
 * $img->showThumbnail();
 *
 * # show image
 * $img->show();
 * </code>
 *
 */
class WsImage
{
    /**
     * @var bool $_has_gd_exstension Is GD exstension exist in PHP
     *
     */
    private $_has_gd_extension;

    /**
     * @var resource $_image Image
     *
     */
    private $_image;

    /**
     * @var string $_imagefile Current image file with full path
     *
     */
    private $_imagefile;


    public function __construct()
    {
        // check for GD extension
        if (!extension_loaded('gd')) {
            if (!dl('gd.so')) {
                $this->_has_gd_extension = false;
                return;
            }
        }

        $this->_has_gd_extension = true;
    }


    /**
     * Read image from file.
     *
     * @param string $file Image file name with path relative to application
     * root directory
     * @return booleane suceess or fail
     *
     */
    public function read($file)
    {
        if (!$this->_has_gd_extension) {
            return false;
        }

        // open image file
        $this->_imagefile = WsROOT.'/'.$file;
        $handle = @fopen($this->_imagefile, 'rb');
        if ($handle === false) {
            return false;
        }

        // read image
        $data = '';
        while (!feof($handle)) {
            $data .= fread($handle, 4192);
        }
        fclose($handle);

        // create image object
        $this->_image = imagecreatefromstring($data);

        // free memory
        unset($data, $handle);

        if ($this->_image === false) {
            return false;
        } else {
            return true;
        }
    }


    /**
     * Show image in browser
     *
     */
    public function show()
    {
        if (!$this->_has_gd_extension or $this->_image === false) {
            return false;
        }

        // temporary image file
        $uid = uniqid('wsimg_');
        $temp_file = WsROOT.'/runtime/'.$uid.basename($this->_imagefile);
        $temp_url = WsSERVER_ROOT.'/runtime/'.$uid.basename($this->_imagefile);

        imagepng($this->_image, $temp_file);

        // show image in browser
        echo '<img src="'.$temp_url.'"/>';

        // free memory
        unset($temp_url, $temp_file, $uid);
    }


    /**
     * Show image thumbnail
     *
     * @param integer $width Thumbnail width
     * @param integer $height Thumbnail height
     * @param string $text Thumbnail text
     *
     */
    public function showThumbnail($width = 100, $height = 100, $text = '')
    {
        if (!$this->_has_gd_extension or $this->_image === false) {
            return false;
        }

        list($orig_width, $orig_height) = getimagesize($this->_imagefile);
        $thumb_image = imagecreatetruecolor($width, $height);

        imagecopyresampled($thumb_image, $this->_image, 0, 0, 0, 0,
            $width, $height, $orig_width, $orig_height
        );

        ob_start();
        imagepng($thumb_image);
        $i =  ob_get_clean();
        imagedestroy($thumb_image);

        // temporary image file
        $uid = uniqid('wsimg_');
        $temp_file = WsROOT.'/runtime/'.$uid.basename($this->_imagefile);
        $temp_url = WsSERVER_ROOT.'/runtime/'.$uid.basename($this->_imagefile);

        imagepng($this->_image, $temp_file);

        // id of popup window
        $img_id = uniqid('thumb_'.round(rand()));
        // show thumbnail
        //echo '<div id="'.$img_id.'" style="display: inline-block;">';
        echo '<img id="'.$img_id.'" class="img_thumb" src="data:image/png;base64,'
            .base64_encode($i).'"/>';
        if (trim($text) != '') {
            echo '<p style="align: center;">'.$text.'</p>';
        }
        //echo '</div>';

        echo '<script type="text/javascript">'.PHP_EOL;
        echo 'var '.$img_id.'=document.getElementById("'.$img_id.'");'.PHP_EOL;
        echo $img_id.'.onclick = function() {'.PHP_EOL;
        echo 'window.ws_thumb_show = WsViewThumbnail("'
            .$temp_url.'");'.PHP_EOL;
        echo '}'.PHP_EOL;
        echo '</script>'.PHP_EOL;

        // free memory
        unset($i, $thumb_image,
            $orig_height, $orig_width, $temp_url, $temp_file
        );
    }


    /**
     * Add watermark text to image
     *
     * @param string $text Watermark text
     * @param integer $x Verical offset for text
     * @param integer $y Horizontal offset for text
     * @param string $font Full path, relative to server root, of font file
     * @param integer $size Font size
     */
    public function addWatermark(
        $text, $x = 8, $y = 8 , $font = null, $size = 11
    )
    {
        if (!$this->_has_gd_extension or $this->_image === false) {
            return false;
        }

        // no empty string
        if (trim($text) === '') {
            return false;
        }

        if ($font == null) {
            $font = WsROOT.'/public/fonts/FreeSans.ttf';
        } else {
            $font = WsROOT.'/'.$font;
        }

        # calculate maximum height of a character
        $bbox = imagettfbbox($size, 0, $font, 'ky');
        $y -= $bbox[5];

        $black = imagecolorallocate($this->_image, 0, 0, 0);
        $white = imagecolorallocate($this->_image, 255, 255, 255);
        imagettftext($this->_image, $size, 0, $x+1, $y+1, $black, $font, $text);
        imagettftext($this->_image, $size, 0, $x, $y+1, $black, $font, $text);
        imagettftext($this->_image, $size, 0, $x, $y, $white, $font, $text);

        unset($white, $black, $bbox);
    }


    /**
     * Get image width
     *
     * @return integer $width Image width
     *
     */
    public function getWidth()
    {
        if (!$this->_has_gd_extension or $this->_image === false) {
            return false;
        }

        $width = imagesx($this->_image);

        return $width;
    }


    /**
     * Get image height
     *
     * @return integer $height Image height
     *
     */
    public function getHeight()
    {
        if (!$this->_has_gd_extension or $this->_image === false) {
            return false;
        }

        $height = imagesy($this->_image);

        return $height;
    }
}
