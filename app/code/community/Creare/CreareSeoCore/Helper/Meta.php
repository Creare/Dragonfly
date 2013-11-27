<?php
class Creare_CreareSeoCore_Helper_Meta extends Mage_Core_Helper_Abstract
{
    public function show()
    {
        echo "HELLO";
    }
    
    public function getPageType()
    {
        if (Mage::registry('current_product'))
        {
            
        }
    }
}
