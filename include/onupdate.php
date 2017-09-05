<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    XOOPS Project https://xoops.org/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author       XOOPS Development Team
 */

if ((!defined('XOOPS_ROOT_PATH')) || !($GLOBALS['xoopsUser'] instanceof XoopsUser)
    || !$GLOBALS['xoopsUser']->IsAdmin()
) {
    exit('Restricted access' . PHP_EOL);
}

/**
 * @param string $tablename
 *
 * @return bool
 */
function tableExists($tablename)
{
    $result = $GLOBALS['xoopsDB']->queryF("SHOW TABLES LIKE '$tablename'");

    return ($GLOBALS['xoopsDB']->getRowsNum($result) > 0) ? true : false;
}

/**
 *
 * Prepares system prior to attempting to install module
 * @param XoopsModule $module {@link XoopsModule}
 *
 * @return bool true if ready to install, false if not
 */
function xoops_module_pre_update_myconference(XoopsModule $module)
{
    $moduleDirName = basename(dirname(__DIR__));
    $utilityClass     = ucfirst($moduleDirName) . 'Utility';
    if (!class_exists($utilityClass)) {
        xoops_load('utility', $moduleDirName);
    }
    //check for minimum XOOPS version
    if (!$utilityClass::checkVerXoops($module)) {
        return false;
    }

    // check for minimum PHP version
    if (!$utilityClass::checkVerPhp($module)) {
        return false;
    }

    return true;
}

/**
 *
 * Performs tasks required during update of the module
 * @param XoopsModule $module {@link XoopsModule}
 * @param null        $previousVersion
 *
 * @return bool true if update successful, false if not
 */

function xoops_module_update_myconference(XoopsModule $module, $previousVersion = null)
{
    $moduleDirName = basename(dirname(__DIR__));
    $capsDirName   = strtoupper($moduleDirName);

    if ($previousVersion < 240) {
        require_once __DIR__ . '/config.php';
        $configurator = new XmyconferenceConfigurator();
        $utilityClass    = ucfirst($moduleDirName) . 'Utility';
        if (!class_exists($utilityClass)) {
            xoops_load('utility', $moduleDirName);
        }

        //rename column EXAMPLE
        $tables     = new Tables();
        $table      = 'myconferencex_categories';
        $column     = 'ordre';
        $newName    = 'order';
        $attributes = "INT(5) NOT NULL DEFAULT '0'";
        if ($tables->useTable($table)) {
            $tables->alterColumn($table, $column, $attributes, $newName);
            if (!$tables->executeQueue()) {
                echo '<br />' . _AM_XXXXX_UPGRADEFAILED0 . ' ' . $migrate->getLastError();
            }
        }

        //delete old HTML templates
        if (count($configurator->templateFolders) > 0) {
            foreach ($configurator->templateFolders as $folder) {
                $templateFolder = $GLOBALS['xoops']->path('modules/' . $moduleDirName . $folder);
                if (is_dir($templateFolder)) {
                    $templateList = array_diff(scandir($templateFolder, SCANDIR_SORT_NONE), ['..', '.']);
                    foreach ($templateList as $k => $v) {
                        $fileInfo = new SplFileInfo($templateFolder . $v);
                        if ('html' === $fileInfo->getExtension() && 'index.html' !== $fileInfo->getFilename()) {
                            if (file_exists($templateFolder . $v)) {
                                unlink($templateFolder . $v);
                            }
                        }
                    }
                }
            }
        }

        //  ---  DELETE OLD FILES ---------------
        if (count($configurator->oldFiles) > 0) {
            //    foreach (array_keys($GLOBALS['uploadFolders']) as $i) {
            foreach (array_keys($configurator->oldFiles) as $i) {
                $tempFile = $GLOBALS['xoops']->path('modules/' . $moduleDirName . $configurator->oldFiles[$i]);
                if (is_file($tempFile)) {
                    unlink($tempFile);
                }
            }
        }

        //  ---  DELETE OLD FOLDERS ---------------
        xoops_load('XoopsFile');
        if (count($configurator->oldFolders) > 0) {
            //    foreach (array_keys($GLOBALS['uploadFolders']) as $i) {
            foreach (array_keys($configurator->oldFolders) as $i) {
                $tempFolder = $GLOBALS['xoops']->path('modules/' . $moduleDirName . $configurator->oldFolders[$i]);
                /* @var $folderHandler XoopsObjectHandler */
                $folderHandler = XoopsFile::getHandler('folder', $tempFolder);
                $folderHandler->delete($tempFolder);
            }
        }

        //  ---  CREATE FOLDERS ---------------
        if (count($configurator->uploadFolders) > 0) {
            //    foreach (array_keys($GLOBALS['uploadFolders']) as $i) {
            foreach (array_keys($configurator->uploadFolders) as $i) {
                $utilityClass::createFolder($configurator->uploadFolders[$i]);
            }
        }

        //  ---  COPY blank.png FILES ---------------
        if (count($configurator->blankFiles) > 0) {
            $file = __DIR__ . '/../assets/images/blank.png';
            foreach (array_keys($configurator->blankFiles) as $i) {
                $dest = $configurator->blankFiles[$i] . '/blank.png';
                $utilityClass::copyFile($file, $dest);
            }
        }

        //delete .html entries from the tpl table
        $sql = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('tplfile') . " WHERE `tpl_module` = '" . $module->getVar('dirname', 'n') . '\' AND `tpl_file` LIKE \'%.html%\'';
        $GLOBALS['xoopsDB']->queryF($sql);

        /** @var XoopsGroupPermHandler $gpermHandler */
        $gpermHandler = xoops_getHandler('groupperm');
        return $gpermHandler->deleteByModule($module->getVar('mid'), 'item_read');
    }
    return true;
}

//============== OLD ==========================================

/**
 *
 * Performs tasks required during update of the module
 * @param XoopsModule $module {@link XoopsModule}
 * @param null        $prev_version
 *
 * @return bool true if update successful, false if not
 */

function xoops_module_update_myconference(XoopsModule $module, $prev_version = null)
{
    global $xoopsDB;
    if ($prev_version < 102) {
        // delete old HTML template files ============================
        $templateDirectory = $GLOBALS['xoops']->path('modules/' . $module->getVar('dirname', 'n') . '/templates/');
        $templateList      = array_diff(scandir($templateDirectory, SCANDIR_SORT_NONE), ['..', '.']);
        foreach ($templateList as $k => $v) {
            $fileInfo = new SplFileInfo($templateDirectory . $v);
            if ('html' === $fileInfo->getExtension() && 'index.html' !== $fileInfo->getFilename()) {
                if (file_exists($templateDirectory . $v)) {
                    unlink($templateDirectory . $v);
                }
            }
        }

        // delete old block html template files ============================
        $templateDirectory = $GLOBALS['xoops']->path('modules/' . $module->getVar('dirname', 'n') . '/templates/blocks/');
        $templateList      = array_diff(scandir($templateDirectory, SCANDIR_SORT_NONE), ['..', '.']);
        foreach ($templateList as $k => $v) {
            $fileInfo = new SplFileInfo($templateDirectory . $v);
            if ('html' === $fileInfo->getExtension() && 'index.html' !== $fileInfo->getFilename()) {
                if (file_exists($templateDirectory . $v)) {
                    unlink($templateDirectory . $v);
                }
            }
        }

        require_once __DIR__ . '/config.php';
        if (count($oldFiles) > 0) {
            foreach (array_keys($oldFiles) as $file) {
                if (is_file($file)) {
                    unlink($GLOBALS['xoops']->path('modules/' . $module->getVar('dirname', 'n') . $oldFiles[$file]));
                }
            }
        }

        //delete .html entries from the tpl table
        $sql = 'DELETE FROM ' . $xoopsDB->prefix('tplfile') . " WHERE `tpl_module` = '" . $module->getVar('dirname', 'n') . '\' AND `tpl_file` LIKE \'%.html%\'';
        $xoopsDB->queryF($sql);

        // Load class XoopsFile ====================
        xoops_load('XoopsFile');

        //delete /images directory ============
        $imagesDirectory = $GLOBALS['xoops']->path('modules/' . $module->getVar('dirname', 'n') . '/images/');
        $folderHandler   = XoopsFile::getHandler('folder', $imagesDirectory);
        $folderHandler->delete($imagesDirectory);

        //delete /css directory ==============
        $cssDirectory  = $GLOBALS['xoops']->path('modules/' . $module->getVar('dirname', 'n') . '/css/');
        $folderHandler = XoopsFile::getHandler('folder', $cssDirectory);
        $folderHandler->delete($cssDirectory);

        //delete /js directory ==================
        $jsDirectory   = $GLOBALS['xoops']->path('modules/' . $module->getVar('dirname', 'n') . '/js/');
        $folderHandler = XoopsFile::getHandler('folder', $jsDirectory);
        $folderHandler->delete($jsDirectory);

        //delete /tcpdf directory ======================
        $tcpdfDirectory = $GLOBALS['xoops']->path('modules/' . $module->getVar('dirname', 'n') . '/tcpdf/');
        $folderHandler  = XoopsFile::getHandler('folder', $tcpdfDirectory);
        $folderHandler->delete($tcpdfDirectory);

        //create upload directories, if needed (defined in /include/config.php) ====================
        $moduleDirName = $module->getVar('dirname');
        include $GLOBALS['xoops']->path('modules/' . $moduleDirName . '/include/config.php');

        $className = ucfirst($moduleDirName) . 'Util';
        foreach (array_keys($uploadFolders) as $i) {
            $className::createFolder($uploadFolders[$i]);
        }
        //copy blank.png files, if needed
        $file = XXXX_ROOT_PATH . '/assets/images/blank.png';
        foreach (array_keys($copyFiles) as $i) {
            $dest = $copyFiles[$i] . '/blank.png';
            $className::copyFile($file, $dest);
        }
    }

    $gpermHandler = xoops_getHandler('groupperm');

    return $gpermHandler->deleteByModule($module->getVar('mid'), 'item_read');
}

//===========================================

//=============================================

if ((!defined('XOOPS_ROOT_PATH'))
    || !($GLOBALS['xoopsUser'] instanceof XoopsUser)
    || !$GLOBALS['xoopsUser']->IsAdmin()
) {
    exit('Restricted access' . PHP_EOL);
}

/**
 * @param string $tablename
 *
 * @return bool
 */
function tableExists($tablename)
{
    $result = $GLOBALS['xoopsDB']->queryF("SHOW TABLES LIKE '$tablename'");

    return ($GLOBALS['xoopsDB']->getRowsNum($result) > 0) ? true : false;
}

/**
 * @return bool
 */
function xoops_module_update_randomquote(&$module, $oldversion = null)
{
    $errors = 0;
    if (tableExists($GLOBALS['xoopsDB']->prefix('citas'))) {
        $sql    = sprintf('ALTER TABLE ' . $GLOBALS['xoopsDB']->prefix('citas') . ' CHANGE `citas` `quote` TEXT');
        $result = $GLOBALS['xoopsDB']->queryF($sql);
        if (!$result) {
            $module->setErrors(_AM_RANDOMQUOTE_UPGRADEFAILED0);
            ++$errors;
        }

        $sql    = sprintf('ALTER TABLE '
                          . $GLOBALS['xoopsDB']->prefix('citas')
                          . " ADD COLUMN `quote_status` int (10) NOT NULL default '0',"
                          . " ADD COLUMN `quote_waiting` int (10) NOT NULL default '0',"
                          . " ADD COLUMN `quote_online` int (10) NOT NULL default '0';");
        $result = $GLOBALS['xoopsDB']->queryF($sql);
        if (!$result) {
            $module->setErrors(_AM_RANDOMQUOTE_UPGRADEFAILED1);
            ++$errors;
        }

        $sql    = sprintf('ALTER TABLE ' . $GLOBALS['xoopsDB']->prefix('citas') . ' RENAME ' . $GLOBALS['xoopsDB']->prefix('quotes'));
        $result = $GLOBALS['xoopsDB']->queryF($sql);
        if (!$result) {
            $module->setErrors(_AM_RANDOMQUOTE_UPGRADEFAILED2);
            ++$errors;
        }
    } elseif (tableExists($GLOBALS['xoopsDB']->prefix('randomquote_quotes'))) {

        // change status to indicate quote waiting approval
        $sql    = 'UPDATE ' . $GLOBALS['xoopsDB']->prefix('randomquote_quotes') . ' SET quote_status=2 WHERE `quote_waiting` > 0';
        $result = $GLOBALS['xoopsDB']->queryF($sql);
        if (!$result) {
            $module->setErrors(_AM_RANDOMQUOTE_UPGRADEFAILED1);
            ++$errors;
        }

        // change status to indicate quote online
        $sql    = 'UPDATE ' . $GLOBALS['xoopsDB']->prefix('randomquote_quotes') . ' SET quote_status=1 WHERE `quote_online` > 0';
        $result = $GLOBALS['xoopsDB']->queryF($sql);
        if (!$result) {
            $module->setErrors(_AM_RANDOMQUOTE_UPGRADEFAILED1);
            ++$errors;
        }

        // drop the waiting and online columns
        $sql    = sprintf('ALTER TABLE ' . $GLOBALS['xoopsDB']->prefix('randomquote_quotes') . ' DROP COLUMN `quote_waiting`,' . ' DROP COLUMN `quote_online`;');
        $result = $GLOBALS['xoopsDB']->queryF($sql);
        if (!$result) {
            $module->setErrors(_AM_RANDOMQUOTE_UPGRADEFAILED1);
            ++$errors;
        }

        // change the table name (drops the module name prefix)
        $sql    = sprintf('ALTER TABLE ' . $GLOBALS['xoopsDB']->prefix('randomquote_quotes') . ' RENAME ' . $GLOBALS['xoopsDB']->prefix('quotes'));
        $result = $GLOBALS['xoopsDB']->queryF($sql);
        if (!$result) {
            $module->setErrors(_AM_RANDOMQUOTE_UPGRADEFAILED2);
            ++$errors;
        }
    }

    if ($installedVersion < 233) {
        /* add column for poll anonymous which was created in versions prior
         * to 1.40 of xoopspoll but not automatically created
         */
        $result    = $db->queryF('SHOW COLUMNS FROM ' . $db->prefix('quotes') . " LIKE 'create_date'");
        $foundAnon = $db->getRowsNum($result);
        if (empty($foundAnon)) {
            // column doesn't exist, so try and add it
            $success = $db->queryF('ALTER TABLE ' . $db->prefix('quotes') . ' ADD create_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER quote_status');
            if (false === $success) {
                $module->setErrors(sprintf(_AM_RANDOMQUOTE_ERROR_COLUMN, 'create_date'));
                ++$errors;
            }
        }
    }

    return $errors ? false : true;
}
