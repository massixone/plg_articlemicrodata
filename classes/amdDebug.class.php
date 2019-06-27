<?php

/**
 * @copyright   Copyright (C) 2006 - 2016 Massimo Di Primio. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

#namespace plgamd;        #namespace plg_content_article_microdata;

defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Description of amdDebug
 *
 * @author Massimo Di Primio
 */
class amdPlgDebug {
    //put your code here

    private $debugLevel = 0;             # No debug by default
    private $debugString = "";

    public function __construct($gbgl) {
        #parent::__construct($gbgl);
        $this->debugLevel = intval($gbgl);
        $this->debugString = "";
        #$this->debugLevel = parent::dbgGet();
    }

    /**
     * Unset debug
     * @param   None
     * @return  None
     */
    public function Reset() {
        $this->debugLevel = 0;
    }

    /**
     * Set the debug level to the specified value
     * @param   type integer $gbgl
     * @return  None
     */
    public function Set($gbgl) {
        $this->debugLevel = intval($gbgl);
    }

    public function Get() {
        return $this->debugLevel;
    }

   /**
    * debug($str) add some debug information into the html as commented text.
    * @param     The string being debugged
    * @returns   none
    */
    public function HtmlComment($txt, $debug_obj)     {
        if ($this->debugLevel >= 1) {
            echo "\n";
            echo '<!-- plgContentArticleMicrodata.Debug:(Type=['.gettype($debug_obj).'])';
            if (gettype($debug_obj) == 'object' or gettype($debug_obj) == 'array') {
                echo '### begin-of-dump for array or object ['.$txt.'] ###';
                echo json_encode($debug_obj, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);    #var_dump($debug_obj);
                echo '### end-of-dump ['.$txt.']###';
            } else {
                echo 'varDebug:['.$txt.']:[sot]'.$debug_obj.'[eot]';
            }
            echo "-->\n";
        }
        return;
    }

   /**
    * debug($str) add some debug information into the html as commented text.
    * @param     The string being debugged
    * @returns   none
    */
    public function HtmlText($txt, $debug_obj)     {
        if ($this->debugLevel >= 1) {
            echo "\n";
            echo '<br /><pre> plgContentArticleMicrodata.Debug:(Type=['.gettype($debug_obj).'])';
            if (gettype($debug_obj) == 'object' or gettype($debug_obj) == 'array') {
                echo '### begin-of-dump for array or object ['.$txt.'] ###';
                echo json_encode($debug_obj, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);    #var_dump($debug_obj);
                echo '### end-of-dump ['.$txt.']###';
            } else {
                echo 'varDebug:['.$txt.']:[sot]'.$debug_obj.'[eot]';
            }
            echo "</pre><br />\n";
        }
        return;
    }

}
