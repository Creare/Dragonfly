<?php

class Creare_CreareSeoCore_Block_Page_Html_Head extends Mage_Page_Block_Html_Head
{
    public function getTitle()
    {
        if (empty($this->_data['title'])) {
            $this->_data['title'] = $this->getDefaultTitle();
        }
        return htmlspecialchars(html_entity_decode(trim($this->_data['title']), ENT_QUOTES, 'UTF-8'));
    }
    
    
    public function getDescription()
    {
        if (empty($this->_data['description'])) {
            $this->_data['description'] = Mage::getStoreConfig('design/head/default_description');
        }
        return $this->_data['description'];
    }
    
    public function metaHelper()
    {
        return Mage::helper('creareseocore/meta');
    }
}