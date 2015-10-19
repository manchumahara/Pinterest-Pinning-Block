<?php
/*------------------------------------------------------------------------
# pinterestpinningblocker.php - System - Pinterest Pinning Blocker
# ------------------------------------------------------------------------
# author    Shabab Mustafa of Codeboxr Team
# copyright Copyright (C) 2010-2012 codeboxr.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://codeboxr.com
# Technical Support:  Forum - http://codeboxr.com/product/pinterest-pinning-block-in-joomla-website
-------------------------------------------------------------------------*/

//error_reporting(E_ALL);
//ini_set("display_errors", 1);


// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * System - Pinterest Pinning Blocker plugin
 * @package		Joomla
 * @subpackage	plgSystemPinterestpinningblocker
 */
class plgSystemPinterestpinningblocker extends JPlugin
{
        /**
         * Constructor
         *
         * For php4 compatability we must not use the __constructor as a constructor for plugins
         * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
         * This causes problems with cross-referencing necessary for the observer design pattern.
         *
         * @access      protected
         * @param       object  $subject The object to observe
         * @param       array   $config  An array that holds the plugin configuration
         * @since       1.0
         */
        function plgSystempinterestpinningblocker( &$subject, $config )
        {
            parent::__construct( $subject, $config );
            
            
            $this->_display = true;
                // Do some extra initialisation in this constructor if required
                // load plugin parameters
            
                // return if params are empty
                //var_dump($this->params);
            if(!$this->params){ $this->_display = false; return;}
            
                //basic params                
                $this->_skipitemid      	= $this->params->get('skipitemid',''); //show/hide for specific itemid
                $this->_itemidfiltertype        = $this->params->get('itemidfiltertype', 1);     // 0 exclude  1 include   (only)
                $this->_curitemid       	= JRequest::getCmd('Itemid','');
                $this->_skipfront       	= $this->params->get('skipfront', 1 ); //show/hide in front page
                $this->_disabeonoffline         = $this->params->get('disabeonoffline',1);
                $this->_popup     	        = $this->params->get('optionalpopup',1); //remove widget while popup, default yes 1
                $this->_rss                     = $this->params->get('optionalrss',1); //remove widget from rss feed, default yes 1, for rss feed need to send format = feed
                $this->_ajax                    = $this->params->get('optionalajax',1); //remove widget from raw/ajax output, for ajax output need to send format=raw
                
                

                $app        = JFactory::getApplication(); //global $mainframe;  in j1.5
                $offline    =  $app->getCfg('offline');

                if($this->_disabeonoffline && $offline){
                   $this->_display = false; return;	
               }

               if ($app->isAdmin()){ $this->_display = false; return; }
           } 

        /**
         * Do something onAfterInitialise
         */
        function onAfterInitialise()
        {
            if(!$this->_display) return;

        }

        /**
         * onAfterRoute Generats link of the page to be shared/votted 
         */
        function onAfterRoute()
        {
            //var_dump($this->_display);
            if(!$this->_display) return;        
            
            $app                   = JFactory::getApplication('site');
            $menu                  = $app->getMenu();
            $activemenu            = ($menu->getActive()) ? $menu->getActive() : $menu->getDefault();
            $defaultmenu           = $menu->getDefault();

            //pick current item id
            if($this->_curitemid == ''){                     
                if(isset ($activemenu->id)){
                    $this->_curitemid = $activemenu->id;
                }                                            
            } 

            //include or exclude for item id
            if($this->_skipitemid != '' && $this->_curitemid != '') {
                $exc_itemids = explode (",", $this->_skipitemid);        
                if($this->_itemidfiltertype && !in_array($this->_curitemid, $exc_itemids)){        
                    $this->_display = false; return;
                }
                else if(!$this->_itemidfiltertype && in_array($this->_curitemid, $exc_itemids)){
                 $this->_display = false; return;	
             }        
         }  

            //disable in popup, feed or ajax request. as linkedin or some widget may not work in ajax mode where need to load external js or here need some expert excuse
         if(($this->_popup && (JRequest::getCmd('tmpl') == 'component'))||($this->_rss && (JRequest::getCmd('format') == 'feed'))||($this->_ajax && (JRequest::getCmd('format') == 'raw'))){	    
            $this->_display = false; return;	    
        }

        if(isset ($activemenu->id) && isset ($defaultmenu->id) ){
            $active_id  = $activemenu->id;
            $default_id = $defaultmenu->id;
            if (($active_id == $default_id)&& !$this->_skipfront) {
                $this->_display = false; return;
            }
        }  
        
    }

        /**
         *  onAfterDispatch dispatches the button and load javascripts for some social shares 
         */
        function onAfterDispatch()
        {                       
         if(!$this->_display) return;
         
         $doc	= JFactory::getDocument();
         $doctype	= $doc->getType();
            // Only render for HTML output
         if ( $doctype !== 'html' ){$this->_display = false;	return;}   

            //adding meta tag
         if($this->_display) { $doc->setMetaData('pinterest', 'nopin'); }
         
         

     }
        /**
         *onAfterRender renders single button
         */

        function onAfterRender()
        {
          if(!$this->_display) return;     
      }
      
}//end of class plgSystemPinterestpinningblocker
?>