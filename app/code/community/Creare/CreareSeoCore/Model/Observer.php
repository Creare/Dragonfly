<?php

class Creare_CreareSeoCore_Model_Observer extends Mage_Core_Model_Abstract
{

	/* Our function to change the META robots tag on Parameter based category pages */
	public function changeRobots($observer)
	{
		if(Mage::getStoreConfig('creareseocore/defaultseo/noindexparams')){
			if($observer->getEvent()->getAction()->getFullActionName() == 'catalog_category_view')
			{
				$uri = $observer->getEvent()->getAction()->getRequest()->getRequestUri();
				if(stristr($uri,"?")): 
					$layout       = $observer->getEvent()->getLayout();
					$product_info = $layout->getBlock('head');
					$layout->getUpdate()->addUpdate('<reference name="head"><action method="setRobots"><value>noindex,follow</value></action></reference>');
					$layout->generateXml();
				endif;
			}
		}
		return $this;
	}

	/* The function to remove the meta keywords tag */
	public function applyTag($observer)
	{
		if(Mage::getStoreConfig('creareseocore/defaultseo/metakw')){
			$body = $observer->getResponse()->getBody();
			if(strpos(strToLower($body), 'meta name="keywords"') !== false)
			{			
				$body = preg_replace('{(<meta name="keywords"[^>]*?>)}i','',$body);
				$observer->getResponse()->setBody($body);
			}
		}
	}
        
        /* Replaces category name with heading on category pages */
        public function seoHeading($observer)
        {
            if(Mage::app()->getFrontController()->getAction()->getFullActionName() == 'catalog_category_view')
            {
                $category = $observer->getEvent()->getCategory();

                if ($category->getData('creareseo_heading')) {
                    $category->setName($category->getCreareseoHeading()); 
                }
            }
        }
        
        public function writeToFileOnConfigSave($observer)
        {
            
           $helper = Mage::helper('creareseocore');
           $post = Mage::app()->getRequest()->getPost();
           $robots_post = $post['groups']['files']['fields']['robots']['value'];
           $htaccess_post = $post['groups']['files']['fields']['htaccess']['value'];
           
           if ($robots_post)
           {
               //$helper->writeFile($helper->robotstxt(), $robots_post, 'robots');
           }
           
           if ($htaccess_post)
           {
               $helper->writeFile($helper->htaccess(), $htaccess_post, 'htaccess');
           }
           
        }
        
        public function saveConfigOnConfigLoad($observer)
        {
          $helper = Mage::helper('creareseocore');
          $path = $helper->getConfigPath();
          
          if ($path == 'system_config_creareseocore')
          {
             $helper->saveFileContentToConfig($helper->robotstxt(), 'robots');
             $helper->saveFileContentToConfig($helper->htaccess(), 'htaccess');
          }
        }
}