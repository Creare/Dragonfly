<?php
class Creare_CreareSeoCore_Helper_Meta extends Mage_Core_Helper_Abstract
{
    public function getDefaultTitle()
    {
        if ($this->getPageType())
        {
            $title = $this->config($this->getPageType().'_title');
            return $this->shortcodeString($title);
        }
    }
    
    public function getPageType()
    {
        if (Mage::registry('current_product'))
        {
            return 'product';
        } elseif (Mage::registry('current_category'))
        {
            return 'category';
        } elseif (Mage::registry('current_cms_page'))
        {
            return 'cms_page';
        } else {
            return false;
        }
    }
    
    private function config($path)
    {
        return Mage::getStoreConfig('creareseocore/metadata/'.$path);
    }
    
     function shortcodeString($string)
     {
            preg_match_all("/\[(.*?)\]/", $string, $matches);

            for($i = 0; $i < count($matches[1]); $i++)
            {
                $tag = $matches[1][$i];
                
                if ($tag == "store")
                {
                    $string = str_replace($matches[0][$i], Mage::app()->getStore()->getName(), $string);
                } else {
                
                switch ($this->getPageType())
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
     
     function productAttribute($product, $attribute)
     {
         if ($product->getData($attribute))
         {
            return $product->getResource()->getAttribute($attribute)->getFrontend()->getValue($product);
         }

     }
     
     function attribute($model, $attribute)
     {
         if ($model->getData($attribute))
         {
             
             
            return $product->getResource()->getAttribute($attribute)->getFrontend()->getValue($product);
         }

     }
}
