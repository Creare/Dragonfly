<?php
class Creare_CreareSeoCore_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function getDiscontinuedProductUrl($product)
    {
        $categories = false;
        $homepage = false;
        $products = true;

        // check to see if we want to redirect to a product / category / homepage
        if($categories){
            $cats = $product->getCategoryIds();
            if (is_array($cats) && count($cats) > 1) {
                $cat = Mage::getModel('catalog/category')->load( $cats[0] ); 
                return $cat->getUrlPath();
            } else {
                $cat = Mage::getModel('catalog/category')->load( $cats ); 
                return $cat->getUrlPath();
            }
        }

        if($homepage){
            return Mage::getBaseUrl();
        }

        if($products){
            $related = Mage::getModel('catalog/product')->load(165);
            return $related->getProductUrl();
        }

        return false;

    }

    public function getConfigPath($observer)
    {
        return Mage::app()->getRequest()->getControllerName().'_'.Mage::app()->getRequest()->getParam('section');
    }
    
    
    /* 
     * On controller_action_predispatch called by saveConfigOnConfigLoad()
     */
    
    public function saveFileContentToConfig($file, $field)
    {
        $adminsession = Mage::getSingleton('adminhtml/session');
        $io = new Varien_Io_File();
        $io->open(array('path' => Mage::getBaseDir()));
        
        if ($io->fileExists($file))
        {
            try
            {
                $contents = $io->read($file);
                Mage::getModel('core/config')->saveConfig('creare'.$field.'/files/'.$field, $contents);
                
            } catch(Mage_Core_Exception $e)
            {
                $adminsession->addError($e->getMessage());
            }
        } else {
            $adminsession->addError($file." does not exist. Please create this file on your domain root to use this feature.");
        }
            
        $io->streamClose();
    }
    
    /* 
     * On admin_system_config_changed_section_ called by writeToFileOnConfigSave()
     */
    
    public function writeFile($file, $post, $field)
    {
        $adminsession = Mage::getSingleton('adminhtml/session');
        $io = new Varien_Io_File();
        $io->open(array('path' => Mage::getBaseDir()));
        
        if ($io->fileExists($file))
        {
            if ($io->isWriteable($file))
            {
                try
                {
                    $io->streamOpen($file);
                    $io->streamWrite($post);

                } catch(Mage_Core_Exception $e)
                {
                    $adminsession->addError($e->getMessage());
                }
            } else {
            
                $adminsession->addError($file." is not writable. Change permissions to 644 to use this feature.");
            
            }
        } else {
            
            $adminsession->addError($file." does not exist. The file was not saved.");
        }
            
        $io->streamClose();
    }
    
    public function robotstxt()
    {
        return 'robots.txt';
    }
    
    public function htaccess()
    {
        return '.htaccess';
    }
}