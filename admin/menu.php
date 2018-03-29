<?php
// ------------------------------------------------------------------------- //
// Copyright 2004, Daniel Coletti (dcoletti@xtech.com.ar)                    //
// This file is part of myconference XOOPS' module.                          //
//                                                                           //
// This program is free software; you can redistribute it and/or modify      //
// it under the terms of the GNU General Public License as published by      //
// the Free Software Foundation; either version 2 of the License, or         //
// (at your option) any later version.                                       //
//                                                                           //
// This program is distributed in the hope that it will be useful,           //
// but WITHOUT ANY WARRANTY; without even the implied warranty of            //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             //
// GNU General Public License for more details.                              //
//                                                                           //
// You should have received a copy of the GNU General Public License         //
// along with This program; if not, write to the Free Software               //
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA //
// ------------------------------------------------------------------------- //

use XoopsModules\Myconference;

// require_once __DIR__ . '/../class/Helper.php';
//require_once __DIR__ . '/../include/common.php';
$helper = Myconference\Helper::getInstance();

$pathIcon32 = \Xmf\Module\Admin::menuIconPath('');
$pathModIcon32 = $helper->getModule()->getInfo('modicons32');

$adminmenu[] = [
    'title' => _MI_MYCONFERENCE_HOME,
    'link'  => 'admin/index.php',
    'icon'  => $pathIcon32 . '/home.png'
];

$adminmenu[] = [
    'title' => _MI_MYCONFERENCE_MAIN,
    'link'  => 'admin/main.php',
    'icon'  => $pathIcon32 . '/manage.png'
];

$adminmenu[] = [
    'title' => _MI_MYCONFERENCE_SPEAKERS,
    'link'  => 'admin/speakers.php',
    'icon'  => $pathIcon32 . '/users.png'
];

$adminmenu[] = [
    'title' => _MI_MYCONFERENCE_SPEECHES,
    'link'  => 'admin/speeches.php',
    'icon'  => $pathIcon32 . '/face-smile.png'
];

$adminmenu[] = [
    'title' => _MI_MYCONFERENCE_TRACKS,
    'link'  => 'admin/tracks.php',
    'icon'  => $pathIcon32 . '/event.png'
];

$adminmenu[] = [
    'title' => _MI_MYCONFERENCE_SECTIONS,
    'link'  => 'admin/sections.php',
    'icon'  => $pathIcon32 . '/category.png'
];

$adminmenu[] = [
    'title' => _MI_MYCONFERENCE_ABOUT,
    'link'  => 'admin/about.php',
    'icon'  => $pathIcon32 . '/about.png'
];
