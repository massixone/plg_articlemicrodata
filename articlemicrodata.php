<?php

/**
 * @package     Article MicroData - Plugin for Joomla!
 * @author      Massimo Di Primio - http://www.diprimio.com
 * @copyright   Copyright (c) 2006 - 2016 diprimio.com
 * @license     GNU/GPL license: http://www.gnu.org/licenses/gpl-2.0.html
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

require_once(JPATH_PLUGINS.'/content/articlemicrodata/classes/amdDebug.class.php');
require_once(JPATH_PLUGINS.'/content/articlemicrodata/classes/amdhelper.class.php');
/**
 * ArticleMicrodata Content plugin
 */
#if(!class_exists('plg_system_heartag\TagParser')) require_once('tagparser.php');
class plgContentArticleMicrodata extends JPlugin {

  public function __construct(&$subject, $params) {
    parent::__construct($subject, $params);
  }

  /**
   * The function that does all the dirty work
   * @in        The article object
   * @returns   nothing
   */
  protected function amd_code( $context, $article, $params ) {
    #
    #global $mainframe;
    #$lang = JFactory::getLanguage();
    #$lang->load('plg_content_articlemicrodata', JPATH_ADMINISTRATOR);
    $debug = new amdPlgDebug($this->params->get('amdcfg_debug'));
    $mdObj = new stdClass();                            # Standard object wher we confine most of our stuff [can be used as '$mdObj->autor = "Nick";']
    $mdObj->DebugTimeStart = microtime(true);           # for debugging purposes
    #
    $mdObj->Article = $article;
    $mdObj->Context = $context;
    $mdObj->Params  = $params;
    
    # Debug basic stuff
    $debug->HtmlComment('context', $context);
    // $debug->HtmlComment('article', $article);     # on production, do not use this debug: it exposes some critical data !!!
    // $debug->HtmlComment('params',  $params);      # on production, do not use this debug: it exposes some critical data !!!

    require_once(JPATH_PLUGINS.'/content/articlemicrodata/classes/admObjBlock.class.php');
    
    // Determine whether 'http' or 'https'
    if ($this->params->get('amdcfg_protocol') == 1) {
        $mdObj->schemaOrgUrl = 'https://schema.org';
    } else {
        $mdObj->schemaOrgUrl = 'http://schema.org';
    }
    $amdBlock = new admObjBlock($mdObj, $this->params);

    // Determine menu type
    $app = JFactory::getApplication();   #    $menu = &Jsite::getMenu();
    $menu = $app->getMenu();
    if ( ($menu->getActive() == $menu->getDefault()) || ($menu->getActive() == $this->params->get('selectedMenuHome')) ) {
        echo '<br />This is the HOME page ('.$this->params->get('selectedMenuHome').')';
        $mdObj->contentType      = 'LandingPage';
        $mdObj->scriptText .= admObjBlock::wrapBlock($amdBlock->jLandingPage($mdObj));
        $mdObj->scriptText .= admObjBlock::wrapBlock($amdBlock->jPublisher($mdObj));
        $mdObj->scriptText .= admObjBlock::wrapBlock($amdBlock->jWebSite($mdObj));
    }
    else if ( ($menu->getActive()->id == $this->params->get('selectedMenusAboutUs')) ) {
        echo '<br />This is the ABOUT page';
        $mdObj->contentType      = 'AboutPage';
        $mdObj->scriptText .= admObjBlock::wrapBlock($amdBlock->jAboutPage($mdObj));
    }
    else if ( ($menu->getActive()->id == $this->params->get('selectedMenusContacts')) ) {
        echo '<br />This is the CONTACT page';
        $mdObj->contentType      = 'ContactPage';
        $mdObj->scriptText .= admObjBlock::wrapBlock($amdBlock->jContactPage($mdObj));
    } #else {$mdObj->contentType      = 'Article';}

    // If 'Article', Different for K2 and J!
    if (($context == 'com_k2.item') &&  ($this->params->get('amdcfg_enableK2Content')) && (!isset($mdObj->contentType))) {                       # k2    Item
        $mdObj->contentType      = 'Article';
        $mdObj->category         = $article->category;  #$this->item->category;
        $mdObj->contentVotes     = amdHelper::getRatingDatak2($article->id);    
        $mdObj->contentComments  = amdHelper::getCommentsK2($article->id);      
        $mdObj->contentImageFile = amdHelper::getKImageFile($article->id);      
        $mdObj->contentUrlRaw    = K2HelperRoute::getItemRoute($article->id.':'.urlencode($article->alias), $article->catid.':'.urlencode($article->category->alias));
        $mdObj->contentUrlSeo    = urldecode(JRoute::_($mdObj->contentUrlRaw)); 
        $mdObj->contentRating    = $article->params->get('itemRating');
        $mdObj->contentHits      = $article->hits;
        //$mdObj->contentCanonicalUrl = rtrim(JURI::base(), DS) . $mdObj->contentUrlSeo;   #"http://www.canonicalurl.ext/article_path/"; # FIX FIX FIX
        $mdObj->contentCanonicalUrl = amdHelper::getCanonical($article->id);
        $mdObj->scriptText = admObjBlock::wrapBlock($amdBlock->jArticle($mdObj));
   }
    else if (($context == "com_content.article") &&  ($this->params->get('amdcfg_enableJoomlaContent')) && (!isset($mdObj->contentType))) {          # Joomla Article
        $mdObj->contentType      = 'Article';
        $mdObj->category         = $article->category;
        $mdObj->contentVotes     = amdHelper::getRatingDataJ($article->id);
        $mdObj->contentComments  = amdHelper::getCommentsJ($article->id);       # null so far
        $tmp = json_decode($article->images);               # FIX FIX FIX FIX FIX FIX FIX FIX FIX FIX FIX FIX FIX FIX FIX FIX FIX FIX FIX FIX 
        $mdObj->contentImageFile = ($tmp->image_fulltext != "") ? $tmp->image_fulltext : $tmp->image_intro; # No matter whether we found at least one image or not
        # To be verified
        $mdObj->contentUrlRaw    = "index.php?option=com_content&view=article&id=".$article->id;       #$mdObj->contentUrlRaw    = "index.php?option=com_content&view=article&id=".$article->id;
        $mdObj->contentUrlSeo    = urldecode(JRoute::_($mdObj->contentUrlRaw));               # To be verified
        $mdObj->contentRating    = $params->get('show_vote');
        $mdObj->contentHits      = $article->hits;
        //$mdObj->contentCanonicalUrl = rtrim(JURI::base(), DS) . $mdObj->contentUrlSeo;   #"http://www.canonicalurl.ext/article_path/"; # FIX FIX FIX
        $mdObj->contentCanonicalUrl = amdHelper::getCanonical($article->id);
        $mdObj->scriptText = admObjBlock::wrapBlock($amdBlock->jArticle($mdObj));
    }
    else if ($context == "com_contact.contact")  {          # Joomla Contact
        return "";
    }
    else if ($context == "com_content.category")  {         # Joomla Category Listing
        if (!isset($mdObj->contentType)) {$mdObj->contentType      = 'Blog';}
        return "";
    }
    else if ($context == "com_k2.itemlist")  {              # K2 Category Listing
        if (!isset($mdObj->contentType)) {$mdObj->contentType      = 'Blog';}
        ###$mdObj->contentType      = 'blogArticle';
        return "";
    } else {
        return "";                  # Don't do anything for other applications (at leat for now...)
    }

    # ~~~~~ Common for Joomla (com_content.article) & K2 (com_k2.item)
    #$mdObj->contentCanonicalUrl = rtrim(JURI::base(), DS) . $mdObj->contentUrlSeo;   #"http://www.canonicalurl.ext/article_path/"; # FIX FIX FIX
    $debug->HtmlComment('RAW', $mdObj->contentUrlRaw);
    $debug->HtmlComment('SEO', $mdObj->contentUrlSeo);
    $debug->HtmlComment('URL-Canonical(?)', $mdObj->contentCanonicalUrl);
    $debug->HtmlComment('ContentVotes)', $mdObj->contentVotes);

    #if ($this->params->get('amdcfg_website_enable') != "0")   {
    #    $mdObj->scriptText .= admObjBlock::wrapBlock($amdBlock->jWebSite($mdObj));
    #}
    return $mdObj->scriptText;
  }

  /**
   * Joomla trigger functions
   */
  public function onContentBeforeDisplay( $context, &$article, &$params ) {
    $app = JFactory::getApplication();
    if( $app->isAdmin() ){
        return;                     # This is not for the backed!
    }

    if ($this->params->get('amdcfg_position') ==1){
        return $this->amd_code($context, $article, $params);

    }
  }

  public function onContentAfterDisplay( $context, &$article, &$params ) {
    $app = JFactory::getApplication();
    if( $app->isAdmin() ){
        return;                     # This is not for the backed!
    }

    if ($this->params->get('amdcfg_position')==2){
        return $this->amd_code($context, $article, $params);
    }
 }
}
