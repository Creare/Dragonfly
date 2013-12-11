<?php

class Creare_CreareSeoCore_Block_Page_Html_Head extends Mage_Page_Block_Html_Head
{
    public function getTitle()
    {
        $pagetype = $this->metaHelper()->getPageType();
        
        if ($pagetype && $pagetype != 'cms_page')
        {
            if (!$pagetype->model->getMetaTitle())
            {
                $this->setConfigTitle($pagetype->code);
            }
        }
        
        if (empty($this->_data['title'])) {
            $this->_data['title'] = $this->getDefaultTitle();
        }
        
        return htmlspecialchars(html_entity_decode(trim($this->_data['title']), ENT_QUOTES, 'UTF-8'));
    }
    
    public function setConfigTitle($pagetype)
    {
        if ($this->metaHelper()->config($pagetype.'_title_enabled'))
        {
            $this->_data['title'] = $this->metaHelper()->getDefaultTitle($pagetype);
        }
    }
    
    public function setConfigMetaDescription($pagetype)
    {
        if ($this->metaHelper()->config($pagetype.'_metadesc_enabled'))
        {
            $this->_data['description'] = $this->metaHelper()->getDefaultMetaDescription($pagetype);
        }
    }
    
    
    public function getDescription()
    {
        $pagetype = $this->metaHelper()->getPageType();
        
        if ($pagetype)
        {
            if (!$pagetype->model->getMetaDescription())
            {
                $this->setConfigMetaDescription($pagetype->code);
            }
        }
        
        if (empty($this->_data['description'])) {
            $this->_data['description'] = "";
        }
        return $this->_data['description'];
    }
    
    public function metaHelper()
    {
        return Mage::helper('creareseocore/meta');
    }
}