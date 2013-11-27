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

  public function discontinuedCheck($observer)
  {
    $data = $observer->getEvent()->getAction()->getRequest();
    if($data->getControllerModule() == "Mage_Catalog"){
      $id = $data->getParam('id');
      if($data->getControllerName() == "product"){
        $product = Mage::getModel('catalog/product')->load($id);
        $url = Mage::helper('creareseocore')->getDiscontinuedProductUrl($product);
        if($url){
          Mage::getSingleton('core/session')->addError('Unfortunately the product "'.$product->getName().'" has been discontinued');
          Mage::app()->getFrontController()->getResponse()->setRedirect($url,301);
          Mage::app()->getResponse()->sendResponse();
          exit;
        }
      }
      if($data->getControllerName() == "category"){
        $url = Mage::helper('creareseocore')->getDiscontinuedCategoryUrl($id);
        if($url){
          Mage::getSingleton('core/session')->addError('Unfortunately this category has been discontinued');
          Mage::app()->getFrontController()->getResponse()->setRedirect($url,301);
          Mage::app()->getResponse()->sendResponse();
          exit;
        }
      }
    }
      
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
      if (Mage::getStoreConfig('creareseocore/defaultseo/category_h1'))
      {
        if(Mage::app()->getFrontController()->getAction()->getFullActionName() == 'catalog_category_view')
        {
            $category = $observer->getEvent()->getCategory();

            if ($category->getData('creareseo_heading')) {
                $category->setName($category->getCreareseoHeading()); 
            }
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
    
    if ($path == 'system_config_crearehtaccess')
    {
       $helper->saveFileContentToConfig($helper->htaccess(), 'htaccess');
    }
    if ($path == 'system_config_crearerobots')
    {
        $helper->saveFileContentToConfig($helper->robots(), 'robots');
    }
  }
}