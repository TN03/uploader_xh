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
    private $type;

    /**
     * The subfolder of the configured folder of the type.
     *
     * @var string
     */
    private $subdir;

    /**
     * The resize mode ('', 'small', 'medium' or 'large').
     *
     * @var string
     */
    private $resize;

    /**
     * The resize width.
     *
     * @var int
     */
    private $width;

    /**
     * The resize height.
     *
     * @var int
     */
    private $height;

    /**
     * The resize quality.
     *
     * @var int
     */
    private $quality;

    /**
     * The lib folder path.
     *
     * @var string
     */
    private $libFolder;

    /**
     * The configuration of the plugin.
     *
     * @var array
     */
    private $config;

    /**
     * The localization of the plugin.
     *
     * @var array
     */
    private $l10n;

    public function __construct()
    {
        global $pth, $plugin_cf, $plugin_tx;

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
        $this->config = $plugin_cf['uploader'];
        $this->l10n = $plugin_tx['uploader'];
    }

    private function getJsonConfig()
    {
        $url = CMSIMPLE_ROOT . '?function=uploader_upload&uploader_type=' . urlencode($this->type)
            . '&uploader_subdir=' . urlencode($this->subdir);
        $config = array(
            'runtimes' => 'html5,silverlight,html4',
            'browse_button' => 'pickfiles',
            'container' => 'container',
            'url' => $url,
            'max_file_size' => $this->config['size_max'],
            'filters' => [[
                'title' => $this->l10n['title_' . $this->type],
                'extensions' => $this->config['ext_' . $this->type]
            ]],
            'flash_swf_url' => "{$this->libFolder}Moxie.swf",
            'silverlight_xap_url' => "{$this->libFolder}Moxie.xap",
            'file_data_name' => 'uploader_file'
        );
        if ($this->config['size_chunk'] !== '') {
            $config['chunk_size'] = $this->config['size_chunk'];
        }
        if (isset($this->width, $this->height, $this->quality)) {
            $config['resize'] = array(
                'width' => $this->width,
                'height' => $this->height,
                'quality' => $this->quality
            );
        } elseif ($this->resize != '') {
            $config['resize'] = array(
                'width' => $this->config['resize-' . $this->resize . '_width'],
                'height' => $this->config['resize-' . $this->resize . '_height'],
                'quality' => $this->config['resize-' . $this->resize . '_quality']
            );
        }
        return json_encode($config);
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
