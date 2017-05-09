<?php

/**
 * The autoloader.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Uploader
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2011-2017 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Uploader_XH
 */

spl_autoload_register(
    function ($class) {
        global $pth;
    
        $parts = explode('\\', $class, 2);
        if ($parts[0] == 'Uploader') {
            include_once $pth['folder']['plugins'] . 'uploader/classes/'
                . $parts[1] . '.php';
        }
    }
);

?>
