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

class InfoController
{
    public function defaultAction()
    {
        global $pth;

        $view = new View('info');
        $view->logo = "{$pth['folder']['plugins']}uploader/uploader.png";
        $view->version = Plugin::VERSION;
        $view->checks = (new SystemCheckService)->getChecks();
        $view->iconFolder = "{$pth['folder']['plugins']}uploader/images/";
        $view->render();
    }
}
