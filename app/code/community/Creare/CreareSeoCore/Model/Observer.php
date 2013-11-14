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
        
        protected function writeFile($observer)
        {
           //$this->getRequest()->getPost('config_state')
        }
}