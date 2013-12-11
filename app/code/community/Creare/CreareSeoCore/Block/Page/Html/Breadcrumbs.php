<?php

class Creare_CreareSeoCore_Block_Page_Html_Breadcrumbs extends Mage_Page_Block_Html_Breadcrumbs
{

    function __construct()
    {
        parent::__construct();
        $this->setTemplate('creareseo/page/html/breadcrumbs.phtml');
    }

}
