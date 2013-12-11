<?php
class Creare_CreareSeoCore_Helper_Meta extends Mage_Core_Helper_Abstract
{
    public function getDefaultTitle($pagetype)
    {
        $title = $this->config($pagetype.'_title');
        return $this->shortcode($title);
    }
    
    public function getDefaultMetaDescription($pagetype)
    {
        $metadesc = $this->config($pagetype.'_metadesc');
        return $this->shortcode($metadesc);
    }
    
    public function getPageType()
    {
        $registry = new Varien_Object;
        
        if (Mage::registry('current_product'))
        {
            $registry->code = 'product';
            $registry->model = Mage::registry('current_product');
            
            return $registry;
            
        } elseif (Mage::registry('current_category'))
        {
            $registry->code = 'category';
            $registry->model = Mage::registry('current_category');
            
            return $registry;
            
        } elseif (Mage::registry('current_cms_page'))
        {
            $registry->code = 'cms_page';
            $registry->model = Mage::registry('current_cms_page');
            
            return $registry;
            
        } else {
            return false;
            
        }
    }
    
    public function config($path)
    {
        return Mage::getStoreConfig('creareseocore/metadata/'.$path);
    }
    
    public function shortcode($string)
    {
        $pagetype = $this->getPageType();
        
        preg_match_all("/\[(.*?)\]/", $string, $matches);

            for($i = 0; $i < count($matches[1]); $i++)
            {
                $tag = $matches[1][$i];
                
                if ($tag == "store")
                {
                    $string = str_replace($matches[0][$i], Mage::app()->getStore()->getName(), $string);
                } else {
                
                switch ($pagetype->code)
                {
                    case 'product' :
                        $attribute = $this->productAttribute(Mage::registry('current_product'), $tag);
                    break;
                
                    case 'category' :
                        $attribute = $this->attribute(Mage::registry('current_category'), $tag);
                    break;
                
                }
                $string = str_replace($matches[0][$i], $attribute, $string);
                }
            }
            
            return $string;
     }
     
     public function productAttribute($product, $attribute)
     {
         if ($product->getData($attribute))
         {
            return $product->getResource()->getAttribute($attribute)->getFrontend()->getValue($product);
         }

     }
     
     public function attribute($model, $attribute)
     {
         if ($model->getData($attribute))
         {
            return $model->getData($attribute);
         }
     }
}
