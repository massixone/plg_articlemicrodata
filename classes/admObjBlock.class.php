<?php

/**
 * @copyright   Copyright (C) 2005 - 2016 Massimo Di Primio. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

#namespace plgamd;        #namespace plg_content_article_microdata;

defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Description of admArticle
 *
 * @author dpm
 */
class admObjBlock extends plgContentArticleMicrodata {
    //put your code here

    public $amdObj;
    public $params;

    public function __construct($p1, $params) {
       $this->amdObj = $p1;
       $this->params = $params;
    }

    /**
     * static function wrapBlock($obj). Assemble the json block in a string, suitable to be appended to the content.
     * @param type $obj. The object containing all the json data.
     * @return type string. The encoded json block
     */
    static function wrapBlock($ObjBlk) {
        return "\n<script type='application/ld+json'>\n" . json_encode($ObjBlk, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "\n</script>";
    }

    /**
     * function jAuthorUrls (), fills up an array with ll the Author social urls as specified in the plugin configuration
     * @return array
     */
    private function jAuthorUrls ()  {
        $author_sameAs = array();
        $tmpIdx = 0;
        if ($this->params->get('amdcfg_author_social01') != "")  {  $author_sameAs[$tmpIdx] = $this->params->get('amdcfg_author_social01'); $tmpIdx++;}
        if ($this->params->get('amdcfg_author_social02') != "")  {  $author_sameAs[$tmpIdx] = $this->params->get('amdcfg_author_social02'); $tmpIdx++;}
        if ($this->params->get('amdcfg_author_social03') != "")  {  $author_sameAs[$tmpIdx] = $this->params->get('amdcfg_author_social03'); $tmpIdx++;}
        if ($this->params->get('amdcfg_author_social04') != "")  {  $author_sameAs[$tmpIdx] = $this->params->get('amdcfg_author_social04'); $tmpIdx++;}
        if ($this->params->get('amdcfg_author_social05') != "")  {  $author_sameAs[$tmpIdx] = $this->params->get('amdcfg_author_social05'); $tmpIdx++;}
        if ($this->params->get('amdcfg_author_social06') != "")  {  $author_sameAs[$tmpIdx] = $this->params->get('amdcfg_author_social06'); $tmpIdx++;}

        return $author_sameAs;
    }
    
    /**
     * function jPublisherUrls (). Fills up the stdClass Object $jauthor with all the Author elements as specified in the plugin configuration
     * @return \stdClass object $jauthor
     */
    public function jAuthor ()    {
        // Prepare name or username
        // Administrator must be carefull when configuring author image plugin parameters
        $tmp = JFactory::getUser($this->amdObj->Article->created_by);
        $tmpUserCreator = ($this->params->get('amdcfg_author_name') == "0") ? $tmp->name : $tmp->username;

        // Prepare author image parameters.
        if (($this->params->get('amdcfg_author_image_url') != "") && (JFILE::exists($this->params->get('amdcfg_author_image_url'))))   {
            $tmpUserImage   = JURI::base().$this->params->get('amdcfg_author_image_url');
            list($tmpUserImgW, $tmpUserImgH, $type, $attr) = getimagesize($tmpUserImage);
        } else {
            $tmpUserImage   = $this->params->get('amdcfg_author_image_url');
            $tmpUserImgW = 0;       # cannot me 'null'
            $tmpUserImgH = 0;       # cannot me 'null'
        }

        // prepare object $jauthor with all needed values
        $jauthor = new stdClass();
        $jauthor->{'@type'}         = 'person';
        $jauthor->{'name'}          = $tmpUserCreator;
        $jauthor->{'image'}         = array('@type'     => 'ImageObject',
                                            'url'       => $tmpUserImage,
                                            'width'     => $tmpUserImgW,
                                            'height'    => $tmpUserImgH
                                            );
        $jauthor->{'sameAs'}        = $this->jAuthorUrls();       #$author_sameAs;
        return $jauthor;
    }

    /**
     * function jPublisherUrls (), Fills up an array with ll the Publisher social urls as specified in the plugin configuration
     * @return array
     */
    private function jPublisherUrls ()  {
        $publisher_sameAs = array();
        $tmpIdx = 0;
        if ($this->params->get('amdcfg_author_social01') != "")  {  $publisher_sameAs[$tmpIdx] = $this->params->get('amdcfg_publisher_social01'); $tmpIdx++;}
        if ($this->params->get('amdcfg_author_social02') != "")  {  $publisher_sameAs[$tmpIdx] = $this->params->get('amdcfg_publisher_social02'); $tmpIdx++;}
        if ($this->params->get('amdcfg_author_social03') != "")  {  $publisher_sameAs[$tmpIdx] = $this->params->get('amdcfg_publisher_social03'); $tmpIdx++;}
        if ($this->params->get('amdcfg_author_social04') != "")  {  $publisher_sameAs[$tmpIdx] = $this->params->get('amdcfg_publisher_social04'); $tmpIdx++;}
        if ($this->params->get('amdcfg_author_social05') != "")  {  $publisher_sameAs[$tmpIdx] = $this->params->get('amdcfg_publisher_social05'); $tmpIdx++;}
        if ($this->params->get('amdcfg_author_social06') != "")  {  $publisher_sameAs[$tmpIdx] = $this->params->get('amdcfg_publisher_social06'); $tmpIdx++;}
        return $publisher_sameAs;
    }

    /**
     * function jPublisher (). Fills up the stdClass Object $jpublisher with all the Publisher elements as specified in the plugin configuration
     * @return \stdClass
     */
    public function jPublisher ()  {
        $jpublisher = new stdClass();
        $jpublisher->{'@type'}      = $this->params->get('amdcfg_publisher_type');
        $jpublisher->{'name'}       = $this->params->get('amdcfg_publisher_name');
        $jpublisher->{'logo'}       = array('@type'     => 'ImageObject',
                                            'url'       => $this->params->get('amdcfg_publisher_logo_url'),
                                            'width'     => intval($this->params->get('amdcfg_publisher_logo_width')),
                                            'height'    => intval($this->params->get('amdcfg_publisher_logo_height'))
                                            );
        $jpublisher->{'sameAs'}     = $this->jPublisherUrls();       #$publisher_sameAs;
        $jpublisher->{'email'}      = $this->params->get('amdcfg_pub_email');
        $jpublisher->{'faxNumber'}  = $this->params->get('amdcfg_pub_faxNumber');
        $jpublisher->{'telephone'}  = $this->params->get('amdcfg_pub_telephone');

        # Add Publisher address only if requested
        if ($this->params->get('amdcfg_publisher_address') == "1") {
            $jpublisher->{'address'}    = array(
                                        '@type'             => 'PostalAddress',
                                        'addressCountry'    => $this->params->get('amdcfg_pub_addressCountry'),
                                        'addressLocality'   => $this->params->get('amdcfg_pub_addressLocality'),
                                        'addressRegion'     => $this->params->get('amdcfg_pub_addressRegion'),
                                        'postalCode'        => $this->params->get('amdcfg_pub_postalCode'),
                                        'streetAddress'     => $this->params->get('amdcfg_pub_streetAddress')
                                        );
        }

        # Add Publisher foundation data only if requested
        if ($this->params->get('amdcfg_publisher_foundation') == "1") {
            if ($this->params->get('amdcfg_pub_founder') != "")          { $jpublisher->{'founder'}          = $this->params->get('amdcfg_pub_founder'); }
            if ($this->params->get('amdcfg_pub_foundingDate') != '')     { $jpublisher->{'foundingDate'}     = $this->params->get('amdcfg_pub_foundingDate');}
            if ($this->params->get('amdcfg_pub_foundingLocation') != '') { $jpublisher->{'foundingLocation'} = $this->params->get('amdcfg_pub_foundingLocation');}
        }

        return $jpublisher;
    }

    /**
     * function jArtileReviews() cycle though objet containing 
     * @return bidimentional array
     */
    private function jArtileReviews()    {
        $article_reviews = array();
        $tmpIdx = 0;
        if(!empty($this->amdObj->contentComments))   {   # usefull when no db table found for comments
            foreach ($this->amdObj->contentComments as $rec) {
                $article_reviews[$tmpIdx]['@type']          = 'Review';
                $article_reviews[$tmpIdx]['author']         = $rec->userName;
                $article_reviews[$tmpIdx]['datePublished']  = date('c', strtotime($rec->commentDate));
                $article_reviews[$tmpIdx]['description']    = $rec->commentText;
                $tmpIdx++;
            }
        }
        return $article_reviews;
    }
    
    public function jArticle($amdObj)   {
        // Prepare Article image parameters.
        if ((!empty($this->amdObj->contentImageFile)) && (JFILE::exists($this->amdObj->contentImageFile)))   {
            $tmpArticleImagePath   = $this->amdObj->contentImageFile;
            list($tmpArticleImageW, $tmpArticleImageH, $type, $attr) = getimagesize($tmpArticleImagePath);
            $tmpArticleImageUri = JURI::base().$tmpArticleImagePath;
        }
        else if (($this->params->get('amdcfg_def_article_img')) && (JFILE::exists($this->params->get('amdcfg_def_article_img'))))  {
            $tmpArticleImagePath   = $this->params->get('amdcfg_def_article_img');
            list($tmpArticleImageW, $tmpArticleImageH, $type, $attr) = getimagesize($tmpArticleImagePath);
            $tmpArticleImageUri = JURI::base().$tmpArticleImagePath;
        }
        else {
            $tmpArticleImageUri = JURI::base().$this->amdObj->contentImageFile;
            $tmpArticleImageW = 0;       # cannot be 'null'
            $tmpArticleImageH = 0;       # cannot be 'null'
        }

        // Prepare object $jarticle with all needed values
        $jarticle = new stdClass();
        $jarticle->{'@context'}             = $amdObj->schemaOrgUrl;             #'http://schema.org';
        $jarticle->{'@type'}                = $this->amdObj->contentType;
        $jarticle->{'mainEntityOfPage'}     = array(
                                                '@type' =>  'WebPage',
                                                '@id'   =>  $this->amdObj->contentCanonicalUrl);
        $jarticle->{'name'}                 = $this->amdObj->Article->title;
        $jarticle->{'image'}                = array(
                                                '@type'  => 'ImageObject',
                                                'url'    => $tmpArticleImageUri,        #$this->amdObj->ArticleImageUrl,
                                                'width'  => $tmpArticleImageW,          #$this->amdObj->ArticleImageWidth,
                                                'height' => $tmpArticleImageH);         #$this->amdObj->ArticleImageHeight);
        $jarticle->{'description'}          = $this->amdObj->Article->metadesc.'.';                          # cannot be empty, so add '.' anyway;
        $jarticle->{'datePublished'}        = date('c', strtotime($this->amdObj->Article->publish_up));
        $jarticle->{'dateModified'}         = date('c', strtotime($this->amdObj->Article->modified));
        $jarticle->{'headline'}             = substr(strip_tags($this->amdObj->Article->introtext), 0, intval($this->params->get('amdcfg_headline_size')));   #$this->amdObj->ArticleHeadline;
        $jarticle->{'inLanguage'}           = ($this->amdObj->Article->language == '*') ? JFactory::getLanguage()->getTag() : $this->amdObj->Article->language;        #($this->amdObj->Article->language == '*') ? $this->amdObj->contentLang : $this->Article->language;

        // Add article agregate rating only if requested
        if ( ($this->params->get('amdcfg_forceonzerorating') === "1") || ($this->amdObj->contentVotes)) { 
            $jarticle->{'aggregateRating'}      = array(
                                                '@type' => 'AggregateRating',
                                                'ratingValue' => $this->amdObj->contentVotes ?  number_format($this->amdObj->contentVotes->rating_sum/$this->amdObj->contentVotes->rating_count,1) : 0,
                                                'bestRating'  => $this->params->get('amdcfg_rating_best'),
                                                'worstRating' => $this->params->get('amdcfg_rating_worst'),
                                                'ratingCount' => $this->amdObj->contentVotes ? $this->amdObj->contentVotes->rating_count : 0);
        }
        
        // Add Interaction count only if requested
        if ($this->params->get('amdcfg_interactionCount') == "1") {
            $jarticle->{'interactionCount'}     = 'UserPageVisits:'.$this->amdObj->contentHits;
        }
        
        // Add article reviews only if some review exixts
        $reviews = $this->jArtileReviews();
        if (!empty($reviews))   {
            $jarticle->{'review'}               = $reviews;   #= $this->jArtileReviews();  #$article_reviews;
        }

        // Article data must include 'author' and 'publisher' data
        $jAuthor    = $this->jAuthor();
        if (!empty($jAuthor))    { $jarticle->{'author'}       = $jAuthor; }
        $jPublisher = $this->jPublisher();
        if (!empty($jPublisher)) { $jarticle->{'publisher'}    = $jPublisher; }
        return $jarticle;
    }

    
    public function jWebSite($amdObj)   {
        /*
        ===== Add PotentialAction Code below This must go on HOME PAGE ONLY=====
        "potentialAction": {
            "@type": "SearchAction",
            "target": "https://query.example.com/search?q={search_term_string}",
            "query-input": "required name=search_term_string"
        }
        */
        $target = rtrim(JURI::base(), "/").$this->params->get('amdcfg_sitesearch_string');
        $jwebsite = new stdClass();
        if($this->params->get('amdcfg_website_enable') == '1')  {
            $jwebsite->{'@context'} = $amdObj->schemaOrgUrl;                     #'http://schema.org';
            $jwebsite->{'@type'}    = 'WebSite';
            if($this->params->get('amdcfg_website_name') != "")             { $jwebsite->{'name'}             = $this->params->get('amdcfg_website_name');}
            if($this->params->get('amdcfg_website_altname') != "")          { $jwebsite->{'alternateName'}    = $this->params->get('amdcfg_website_altname');}  # Not supported by google
            if($this->params->get('amdcfg_website_url') != "")              { $jwebsite->{'url'}              = $this->params->get('amdcfg_website_url');}
            if($this->params->get('amdcfg_website_dateCreated') != "")      { $jwebsite->{'dateCreated'}      = $this->params->get('amdcfg_website_dateCreated');}
            if($this->params->get('amdcfg_website_image') != "")            { $jwebsite->{'image'}            = array(  '@type'  => 'ImageObject',
                                                                                                                        'url'    => $this->params->get('amdcfg_website_image'),
                                                                                                                        'width'  => 200,
                                                                                                                        'height' => 200);}
            if ($this->params->get('amdcfg_sitesearch_enable') == 1)        { $jwebsite->{'potentialAction'}  = array(  '@type'  => 'SearchAction',
                                                                                                                        'target' => $target,    //'https://query.example.com/search?q={search_term_string}',
                                                                                                                        'query-input'=> 'required name=search_term_string');}
            if($this->params->get('amdcfg_website_copyrightHolder') != "")  { $jwebsite->{'copyrightHolder'}  = $this->params->get('amdcfg_website_copyrightHolder');}
            if($this->params->get('amdcfg_website_copyrightYear') != "")    { $jwebsite->{'copyrightYear'}    = $this->params->get('amdcfg_website_copyrightYear');}
            if($this->params->get('amdcfg_isFamilyFriendly') == "1")        { $jwebsite->{'isFamilyFriendly'} = 'true';}
            return $jwebsite;
        } else {
            return null;
        }

    }
    
    public function jContactPage($amdObj)   {
        $jContactPg = new stdClass();
        $jContactPg->{'@context'} = $amdObj->schemaOrgUrl;                     #'http://schema.org';
        $jContactPg->{'@type'}              = 'ContactPage';
        $jContactPg->{'url'}                = 'XXXURL';
        $jContactPg->{'headline'}           = 'XXXHEADLINE';
        $jContactPg->{'description'}        = 'XXXDESCRIPTION';
        $jContactPg->{'mainEntityOfPage'}   = array('@type' => 'WebPage', '@id' => 'XXXID');
        $jContactPg->{'publisher'}          = $this->jPublisher();
        return $jContactPg;
    }

    public function jAboutPage($amdObj)   {
        $jAboutPg = 'TEMPORARY ABOUT PAGE';
        return $jAboutPg;
    }
    
    public function jLandingPage($amdObj)   {
        $jLandingPg = '';       #'TEMPORARY LANDING PAGE';
        return $jLandingPg;
    }
    
    public function jWebPage($amdObj)   {
        $jWebPg = 'TEMPORARY WEB PAGE';
        return $jWebPg;
    }
}
