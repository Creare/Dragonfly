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
     * Takes the file and the configuration field and saves the
     * current file data to the database before the field is loaded
     */
    
    public function saveFileContentToConfig($file, $field)
    {
        echo "HELLO";
        die();
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
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        } else {
            Mage::getSingleton('adminhtml/session')->addError($file." does not exist");
        }
            
        $io->streamClose();
    }
    
    /* 
     * Takes the file, post data and the configuration field and 
     * writes the post data to the file.
     */
    
    public function writeFile($file, $post, $field)
    {
        /*if (Mage::getStoreConfig('creare'.$field.'/files/'.$field) == $post)
        {
            return false;
        }*/
        
        $io = new Varien_Io_File();
        $io->open(array('path' => Mage::getBaseDir()));
        
        if ($io->fileExists($file) && $io->isWriteable($file))
        {
            try
            {
                $io->streamOpen($file);
                $io->streamWrite($post);
                
            } catch(Mage_Core_Exception $e)
            {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
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