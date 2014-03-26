<?php

class Creare_CreareSeoCore_Model_Observer extends Mage_Core_Model_Abstract {
    /* Our function to change the META robots tag on Parameter based category pages */

    public function changeRobots($observer) {
        if (Mage::getStoreConfig('creareseocore/defaultseo/noindexparams')) {
            if ($observer->getEvent()->getAction()->getFullActionName() == 'catalog_category_view') {
                $uri = $observer->getEvent()->getAction()->getRequest()->getRequestUri();
                if (stristr($uri, "?")):
                    $layout = $observer->getEvent()->getLayout();
                    $product_info = $layout->getBlock('head');
                    $layout->getUpdate()->addUpdate('<reference name="head"><action method="setRobots"><value>noindex,follow</value></action></reference>');
                    $layout->generateXml();
                endif;
            }
        }
        return $this;
    }

    public function discontinuedCheck($observer) {
        $data = $observer->getEvent()->getAction()->getRequest();
        if ($data->getControllerModule() == "Mage_Catalog") {
            $id = $data->getParam('id');
            if ($data->getControllerName() == "product") {
                $product = Mage::getModel('catalog/product')->load($id);
                $url = Mage::helper('creareseocore')->getDiscontinuedProductUrl($product);
                if ($url) {
                    Mage::getSingleton('core/session')->addError('Unfortunately the product "' . $product->getName() . '" has been discontinued');
                    Mage::app()->getFrontController()->getResponse()->setRedirect($url, 301);
                    Mage::app()->getResponse()->sendResponse();
                    exit;
                }
            }
            if ($data->getControllerName() == "category") {
                $id = $data->getParam('id');
                $category = Mage::getModel('catalog/category')->load($id);
                $url = Mage::helper('creareseocore')->getDiscontinuedCategoryUrl($category);
                if ($url) {
                    Mage::getSingleton('core/session')->addError('Unfortunately the category "'.$category->getName().'" has been discontinued');
                    Mage::app()->getFrontController()->getResponse()->setRedirect($url, 301);
                    Mage::app()->getResponse()->sendResponse();
                    exit;
                }
            }
        }
    }

    /* The function to remove the meta keywords tag */

    public function applyTag($observer) {
        if (Mage::getStoreConfig('creareseocore/defaultseo/metakw')) {
            $body = $observer->getResponse()->getBody();
            if (strpos(strToLower($body), 'meta name="keywords"') !== false) {
                $body = preg_replace('{(<meta name="keywords"[^>]*?>\n)}i', '', $body);
                
            }
            if (strpos(strToLower($body), 'meta name="description" content=""') !== false) {
                $body = preg_replace('{(<meta name="description"[^>]*?>\n)}i', '', $body);
            }
            
            $observer->getResponse()->setBody($body);
        }
    }

    /* Replaces category name with heading on category pages */

    public function seoHeading($observer) {
        
        if (Mage::app()->getFrontController()->getAction()->getFullActionName() == 'catalog_category_view')
        {
            $category = $observer->getEvent()->getCategory();
            $category->setOriginalName($category->getName());

            if (Mage::getStoreConfig('creareseocore/defaultseo/category_h1'))
            {
                if ($category->getData('creareseo_heading'))
                {
                    $category->setName($category->getCreareseoHeading());
                }
            }
        }
    }

    /*
     * On admin_system_config_changed_section_{crearerobots/crearehtaccess}
     * Takes the file, post data and the configuration field and 
     * writes the post data to the file.
     */

    public function writeToFileOnConfigSave($observer) {

        $helper = Mage::helper('creareseocore');
        $post = Mage::app()->getRequest()->getPost();
        $robots_post = $post['groups']['files']['fields']['robots']['value'];
        $htaccess_post = $post['groups']['files']['fields']['htaccess']['value'];

        if ($robots_post) {
            $helper->writeFile($helper->robotstxt(), $robots_post, 'robots');
        }

        if ($htaccess_post) {
            $helper->writeFile($helper->htaccess(), $htaccess_post, 'htaccess');
        }
    }

    /*
     * On controller_action_predispatch
     * Takes the file and the configuration field and saves the
     * current file data to the database before the field is loaded
     */

    public function saveConfigOnConfigLoad($observer) {
        $helper = Mage::helper('creareseocore');
        $path = $helper->getConfigPath();
        if ($path == 'system_config_crearehtaccess') {
            $helper->saveFileContentToConfig($helper->htaccess(), 'htaccess');
        }
        if ($path == 'system_config_crearerobots') {
            $helper->saveFileContentToConfig($helper->robotstxt(), 'robots');
        }
    }

    public function productCheck(Varien_Event_Observer $observer) {
        if(Mage::app()->getRequest()->getControllerName() == "catalog_product" && Mage::app()->getRequest()->getActionName() == "validate"){
            $attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product','name');
            if ($attributeId) {
                $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
                if(Mage::getStoreConfig('creareseocore/validate/name')){
                    $attribute->setIsUnique(1)->save();
                } else {
                    $attribute->setIsUnique(0)->save();
                }
            }
            $attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product','description');
            if ($attributeId) {
                $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
                if(Mage::getStoreConfig('creareseocore/validate/description')){
                    $attribute->setIsUnique(1)->save();
                } else {
                    $attribute->setIsUnique(0)->save();
                }
            }
            $attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product','short_description');
            if ($attributeId) {
                $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
                if(Mage::getStoreConfig('creareseocore/validate/short_description')){
                    $attribute->setIsUnique(1)->save();
                } else {
                    $attribute->setIsUnique(0)->save();
                }
            }
        }
    }
    
    public function forceProductCanonical(Varien_Event_Observer $observer)
    {
        if (Mage::getStoreConfig('catalog/seo/product_canonical_tag') && !Mage::getStoreConfig('product_use_categories'))
        {
            if (Mage::getStoreConfig('creareseocore/defaultseo/forcecanonical')) {
                $product = $observer->getEvent()->getProduct();
                $url = $product->getUrlModel()->getUrl($product, array('_ignore_category'=>true));
                if(Mage::helper('core/url')->getCurrentUrl() != $url){
                    Mage::app()->getFrontController()->getResponse()->setRedirect($url,301);
                    Mage::app()->getResponse()->sendResponse();
                }
            }
        }
    }
    
    public function contactsMetaData(Varien_Event_Observer $observer)
    {
        if ($observer->getEvent()->getAction()->getRequest()->getRouteName() == "contacts")
        {
            if (Mage::helper('creareseocore/meta')->config('contacts_title'))
            {
                $observer->getEvent()->getLayout()->getBlock('head')->setTitle(Mage::helper('creareseocore/meta')->config('contacts_title'));
            }
            
            if (Mage::helper('creareseocore/meta')->config('contacts_metadesc'))
            {
                $observer->getEvent()->getLayout()->getBlock('head')->setDescription(Mage::helper('creareseocore/meta')->config('contacts_metadesc'));
            }
        }
            
    }

}
