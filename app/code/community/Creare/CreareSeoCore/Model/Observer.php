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
        
        /* Modify XML Sitemap data before save */
        public function applySitemapChanges($observer)
        {
            echo "TEST";
            Mage::log('My log entry', null, 'xmlsitemap.log');
        }

}