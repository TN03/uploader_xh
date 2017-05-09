<?php

/**
 * Copyright 2011-2017 Christoph M. Becker
 *
 * This file is part of Uploader_XH.
 *
 * Uploader_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Uploader_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Uploader_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Uploader;

class Widget
{
    /**
     * The upload type ('images', 'downloads', 'media' or 'userfiles').
     *
     * @var string
     */
    protected $type;

    /**
     * The subfolder of the configured folder of the type.
     *
     * @var string
     */
    protected $subdir;

    /**
     * The resize mode ('', 'small', 'medium' or 'large').
     *
     * @var string
     */
    protected $resize;

    /**
     * The resize width.
     *
     * @var int
     */
    protected $width;

    /**
     * The resize height.
     *
     * @var int
     */
    protected $height;

    /**
     * The resize quality.
     *
     * @var int
     */
    protected $quality;

    /**
     * The lib folder path.
     *
     * @var string
     */
    protected $libFolder;

    /**
     * The image folder path.
     *
     * @var string
     */
    protected $imageFolder;

    /**
     * The language filepath.
     *
     * @var string
     */
    protected $languageFile;

    /**
     * The configuration of the plugin.
     *
     * @var array
     */
    protected $config;

    /**
     * The localization of the plugin.
     *
     * @var array
     */
    protected $l10n;

    public function __construct()
    {
        global $pth, $sl, $cf, $plugin_cf, $plugin_tx;

        $this->type = isset($_GET['uploader_type'])
            && isset($pth['folder'][$_GET['uploader_type']])
            ? $_GET['uploader_type']
            : 'images';
        $subdir = !isset($_GET['uploader_subdir'])
            ? ''
            : preg_replace('/\.\.[\/\\\\]?/', '', stsl($_GET['uploader_subdir']));
        $this->subdir = is_dir($pth['folder'][$this->type] . $subdir)
            ? $subdir
            : '';
        $allowedSizes = array('small', 'medium', 'large', 'custom');
        $this->resize = isset($_GET['uploader_resize'])
            && in_array($_GET['uploader_resize'], $allowedSizes)
            ? $_GET['uploader_resize']
            : '';
        foreach (array('width', 'height', 'quality') as $name) {
            if ($this->resize == 'custom' && !empty($_GET['uploader_' . $name])
                && ctype_digit($_GET['uploader_' . $name])
            ) {
                $this->{$name} = $_GET['uploader_' . $name];
            }
        }
        $this->libFolder = $pth['folder']['plugins'] . 'uploader/lib/';
        $this->imageFolder = $pth['folder']['plugins'] . 'uploader/images/';
        $language = (strlen($sl) == 2) ? $sl : $cf['language']['default'];
        $this->languageFile = $this->libFolder . 'i18n/' . $language . '.js';
        $this->config = $plugin_cf['uploader'];
        $this->l10n = $plugin_tx['uploader'];
    }

    /**
     * @return string
     */
    public function render()
    {
        global $pth, $cf;

        $template = $pth['folder']['plugins'] . 'uploader/views/widget.php';
        ob_start();
        include $template;
        $o = ob_get_clean();
        if (!$cf['xhtml']['endtags']) {
            $o = str_replace('/>', '>', $o);
        }
        return $o;
    }
}
