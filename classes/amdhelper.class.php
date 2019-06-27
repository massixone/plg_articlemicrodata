<?php

/**
 * @copyright   Copyright (C) 2006 - 2016 Massimo Di Primio. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

#namespace plgamd;        #namespace plg_content_article_microdata;

/**
 * Description of helper 
 *
 * @author Massimo Di Primio
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

class amdHelper {

    #public function __construct(&$subject, $params) {
    #    parent::__construct($subject, $params);
    #}

    /**
     * Gt K2 article comments/reviews
     * @param     the k2 item id
     * @return    K2 commen records
     */
    static function getCommentsK2 ($id) {
        $db = JFactory::getDBO();
        try {
           $query = 'SELECT * FROM #__k2_comments WHERE itemID = '. $id . ' AND published = 1';  # LIMIT 100';
           $db->setQuery($query);
           $dbcomments = $db->loadObjectList();
        } catch (RuntimeException $e)      {
            $this->setError($e->getMessage());
        }
        return $dbcomments;
    }

    /**
     * function getCommentsJ() To be implemented soon !!!
     * @param type $id
     * @return type
     */
    static function getCommentsJ ($id) {
        return null;
        /*
        $db = JFactory::getDBO(); 
        try {
            $query = 'SELECT * FROM #__Joomla_comments WHERE content_id = '. $id . ' AND published = 1';  # LIMIT 100';
            $db->setQuery($query);
            $dbcomments = $db->loadObjectList();
        } catch (RuntimeException $e)      {
            $this->setError($e->getMessage());
        }
        return $dbcomments;
        */
    }

    /**
     * Get K2 article rating
     * @param     the k2 item Id
     * @returns   the k2 database rating record
     */
    static function getRatingDatak2($id) {
        $db = JFactory::getDBO();
        try {
            $query = 'SELECT * FROM #__k2_rating WHERE itemID='. $id;
            $db->setQuery($query);
            $dbvotes = $db->loadObject();
        } catch (RuntimeException $e)      {
            $this->setError($e->getMessage());
        }
        return $dbvotes;
    }

    /**
     * Get Joomla article rating
     * @param     the joomla article Id
     * @returns   Jthe jomla database rating record
     */
    static function getRatingDataJ($id) {
        $db = JFactory::getDBO();
        try {
            $query = 'SELECT * FROM #__content_rating WHERE content_id='. $id;
            $db->setQuery($query);
            $dbvotes = $db->loadObject();
        } catch (RuntimeException $e)      {
            $this->setError($e->getMessage());
        }
        return $dbvotes;
    }

    /**
     * Get K2 Best ossible article image
     * @param       the k2 article Id
     * @returns     the K2 image Url
     */
    static function getKImage($id) {
        $k2ImgUrl = "";
        return $k2ImgUrl;
    }

    /**
     * Get K2 Item image url, from the biggest to the smallest.
     * Generic ('Generic') image is taken if nothing matches 'XL', 'X', 'M', 'S', nor 'XS'.
     * If 'Generic' can be found, then Config parameter is returned.
     * @param     the k2 ithem Id
     * @returns   the K2 image absolute path url
     */
    static function getKImageFile($id)  {
        foreach (array('_XL', '_L', '_M', '_S', '_XS', '_Generic') as $type)  {
            foreach (array('.jpg', '.png', '.gif') as $ext) {
                $tmpFileName = 'media'.DS.'k2'.DS.'items'.DS.'cache'.DS.md5("Image".$id).$type.$ext ;
                if (JFile::exists(JPATH_SITE.DS.$tmpFileName)) {
                    return JPath::clean($tmpFileName);  #$tmpFileName;    #JPath::clean($tmpFileName);
                }
            }
        }
        return null;
    }
    
    static function getCanonical ($id)  {
        $dbg = new amdPlgDebug(1);
        
        $app = JFactory::getApplication();
        $menu = $app->getMenu();
        if ($menu->getActive() == $menu->getDefault())  {
            $TRASH = 0;
        }
        $doc = JFactory::getDocument();
        foreach ($doc->_links as $linkurl => $link)    { 
            if (($link['relation'] == 'canonical') && $link['relation'] === 'canonical' ) {
                $canonical = $linkurl;  
                break;
            }
        }
        // if a canonical html tag already exists get the canonical and change it to use the SEF plugin domain field
        if (!empty($canonical))  {
            unset ($doc->_links[$canonical]);                                   // remove current canonial link.
            $canonical = rtrim(JURI::base(), "/"). JUri::getInstance($canonical)->toString(array('path', 'query', 'fragment'));     // Get the canonical
        } else {
            $canonical = rtrim(JURI::base(),"/").JUri::getInstance()->toString(array('path', 'query', 'fragment'));                 // Set new canonical
        }
        return $canonical;
    }

}
