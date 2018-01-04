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

include __DIR__ . '/preloads/autoloader.php';

$moduleDirName = basename(__DIR__);

$modversion['version']             = 1.01;
$modversion['module_status']       = 'Beta 1';
$modversion['release_date']        = '2017/07/20';
$modversion['name']                = _MI_MYCONFERENCE_NAME;
$modversion['description']         = _MI_MYCONFERENCE_DESC;
$modversion['author']              = 'Daniel Coletti ( http://www.xtech.com.ar/ ) XTech';
$modversion['official']            = 0;
$modversion['image']               = 'assets/images/logoModule.png';
$modversion['dirname']             = $moduleDirName;
$modversion['help']                = 'page=help';
$modversion['license']             = 'GNU GPL 2.0 or later';
$modversion['license_url']         = 'www.gnu.org/licenses/gpl-2.0.html';
$modversion['modicons16']          = 'assets/images/icons/16';
$modversion['modicons32']          = 'assets/images/icons/32';
$modversion['module_website_url']  = 'https://xoops.org';
$modversion['module_website_name'] = 'XOOPS';
$modversion['min_php']             = '5.5';
$modversion['min_xoops']           = '2.5.9';
$modversion['min_admin']           = '1.2';
$modversion['min_db']              = ['mysql' => '5.5'];

// Admin things
$modversion['hasAdmin']    = 1;
$modversion['system_menu'] = 1;
$modversion['adminindex']  = 'admin/index.php';
$modversion['adminmenu']   = 'admin/menu.php';

// Menu
$modversion['hasMain'] = 1;
//$modversion['sub'][1]['name'] = _MI_MYCONFERENCE_NEWSPEECH;
//$modversion['sub'][1]['url'] = "submit.php";
//$modversion['sub'][2]['name'] = _MI_MYCONFERENCE_NEWS_SMNAME2;
//$modversion['sub'][2]['url'] = "archive.php";

// ------------------- Mysql ------------------- //
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';

// Tables created by sql file (without prefix!)
$modversion['tables'] = [
    $moduleDirName . '_' . 'speakers',
    $moduleDirName . '_' . 'speeches',
    $moduleDirName . '_' . 'speech_types',
    $moduleDirName . '_' . 'tracks',
    $moduleDirName . '_' . 'main',
    $moduleDirName . '_' . 'sections',
];




// Blocks
$modversion['blocks'][1]['file']        = 'myconference.php';
$modversion['blocks'][1]['name']        = _MI_MYCONFERENCE_BNAME;
$modversion['blocks'][1]['description'] = 'Shows tracks on a congress';
$modversion['blocks'][1]['show_func']   = 'b_myconference_show';
$modversion['blocks'][1]['options']     = '1';
$modversion['blocks'][1]['template']    = 'myconference_block.tpl';

// Templates
$modversion['templates'] = [
    ['file' => 'myconference_index.tpl', 'description' => 'Index'],
    ['file' => 'myconference_speech.tpl', 'description' => 'Speech\'s info page'],
    ['file' => 'myconference_track.tpl', 'description' => 'Track\'s info page'],
    ['file' => 'myconference_speaker.tpl', 'description' => 'Speaker\'s info page'],
];

// ********************************************************************************************************************
// Preferences ********************************************************************************************************
// ********************************************************************************************************************
$modversion['config'][] = [
    'name'        => 'max_imgheight',
    'title'       => '_MI_MYCONFERENCE_MAX_HEIGHT',
    'description' => '_MI_MYCONFERENCE_MAX_HEIGHT_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => '800',
];

$modversion['config'][] = [
    'name'        => 'max_imgwidth',
    'title'       => '_MI_MYCONFERENCE_MAX_WIDTH',
    'description' => '_MI_MYCONFERENCE_MAX_WIDTH_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => '1200',
];

$modversion['config'][] = [
    'name'        => 'max_imgsize',
    'title'       => '_MI_MYCONFERENCE_MAX_SIZE',
    'description' => '_MI_MYCONFERENCE_MAX_SIZE_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => '1000000',
];

$modversion['config'][] = [
    'name'        => 'picsULdir',
    'title'       => '_MI_MYCONFERENCE_PICS_UPLOAD_DIR',
    'description' => '_MI_MYCONFERENCE_PICS_UPLOAD_DIR_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => 'myconference/images',
];

/**
 * Make Sample button visible?
 */
$modversion['config'][] = [
    'name'        => 'showsamplebutton',
    'title'       => '_MI_MYCONFERENCE_SHOW_SAMPLE_BUTTON',
    'description' => '_MI_MYCONFERENCE_SHOW_SAMPLE_BUTTON_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];
