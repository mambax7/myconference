<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
/**
 * WfdownloadsChoiceByLetter class
 *
 * @copyright   XOOPS Project (https://xoops.org)
 * @license     http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author      lucio <lucio.rota@gmail.com>
 * @package     wfdownloads
 * @since       1.00
 * @version     $Id:$
 *
 * Example:
 * $choicebyletter = new WfdownloadsChoiceByLetter($objHandler, null, null, range('a', 'z'), 'letter');
 * echo $choicebyletter->render();
 */
defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');
require_once __DIR__ . '/../../include/common.php';

/**
 * Class WfdownloadsChoiceByLetter
 */
class WfdownloadsChoiceByLetter
{
    /**
     * @var WfdownloadsWfdownloads
     * @access public
     */
    public $wfdownloads = null;

    /**
     * *#@+
     *
     * @access private
     */
    private $objHandler;
    private $criteria;
    private $field_name;
    private $alphabet;
    private $arg_name;
    private $url;
    private $extra;
    private $caseSensitive;

    /**
     * *#@-
     */

    /**
     * Constructor
     *
     * @param XoopsPersistableObjectHandler $objHandler {@link XoopsPersistableObjectHandler}
     * @param CriteriaElement               $criteria   {@link CriteriaElement}
     * @param string                        $field_name search by field
     * @param array                         $alphabet   array of alphabet letters
     * @param string                        $arg_name   item on the current page
     * @param string                        $url
     * @param string                        $extra_arg  Additional arguments to pass in the URL
     * @param boolean                       $caseSensitive
     */
    public function __construct(
        $objHandler,
        $criteria = null,
        $field_name = null,
        $alphabet = [],
        $arg_name = 'letter',
        $url = null,
        $extra_arg = '',
        $caseSensitive = false
    ) {
        $this->wfdownloads = WfdownloadsWfdownloads::getInstance();
        $this->objHandler  = $objHandler;
        $this->criteria    = null === $criteria ? new \CriteriaCompo() : $criteria;
        $this->field_name  = null === $field_name ? $this->objHandler->identifierName : $field_name;
        $this->alphabet    = (count($alphabet) > 0) ? $alphabet : range('a', 'z'); // is there a way to get locale alphabet?
        $this->arg_name    = $arg_name;
        $this->url         = null === $url ? $_SERVER['PHP_SELF'] : $url;
        if ('' !== $extra_arg && ('&amp;' !== substr($extra_arg, -5) || '&' !== substr($extra_arg, -1))) {
            $this->extra = '&amp;' . $extra_arg;
        }
        $this->caseSensitive = $caseSensitive;
    }

    /**
     * Create choice by letter
     *
     * @return string
     */
    public function render()
    {
        $ret = '';
        //
        if (!$this->caseSensitive) {
            $this->criteria->setGroupBy('UPPER(LEFT(' . $this->field_name . ',1))');
        } else {
            $this->criteria->setGroupBy('LEFT(' . $this->field_name . ',1)');
        }
        $countsByLetters = $this->objHandler->getCounts($this->criteria);
        // fill alphabet array
        $alphabet_array = [];
        foreach ($this->alphabet as $letter) {
            $letter_array = [];
            if (!$this->caseSensitive) {
                if (isset($countsByLetters[strtoupper($letter)])) {
                    $letter_array['letter'] = $letter;
                    $letter_array['count']  = $countsByLetters[strtoupper($letter)];
                    $letter_array['url']    = $this->url . '?' . $this->arg_name . '=' . $letter . $this->extra;
                } else {
                    $letter_array['letter'] = $letter;
                    $letter_array['count']  = 0;
                    $letter_array['url']    = '';
                }
            } else {
                if (isset($countsByLetters[$letter])) {
                    $letter_array['letter'] = $letter;
                    $letter_array['count']  = $countsByLetters[$letter];
                    $letter_array['url']    = $this->url . '?' . $this->arg_name . '=' . $letter . $this->extra;
                } else {
                    $letter_array['letter'] = $letter;
                    $letter_array['count']  = 0;
                    $letter_array['url']    = '';
                }
            }
            $alphabet_array[$letter] = $letter_array;
            unset($letter_array);
        }
        // render output
        if (!isset($GLOBALS['xoTheme']) || !is_object($GLOBALS['xoTheme'])) {
            require_once $GLOBALS['xoops']->path('/class/theme.php');
            $GLOBALS['xoTheme'] = new \xos_opal_Theme();
        }
        require_once $GLOBALS['xoops']->path('/class/template.php');
        $choiceByLetterTpl          = new \XoopsTpl();
        $choiceByLetterTpl->caching = false; // Disable cache
        $choiceByLetterTpl->assign('alphabet', $alphabet_array);
        // IN PROGRESS
        // IN PROGRESS
        // IN PROGRESS
        $ret .= $choiceByLetterTpl->fetch("db:{$this->wfdownloads->getModule()->dirname()}_co_choicebyletter.tpl");
        unset($choiceByLetterTpl);

        return $ret;
    }
}
