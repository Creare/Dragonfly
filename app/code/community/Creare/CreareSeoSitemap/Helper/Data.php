<?php

class Creare_CreareSeoSitemap_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function showCMS()
	{
		return Mage::getStoreConfig('creareseo/creareseositemap/showcms');
	}

	public function showCategories()
	{
		return Mage::getStoreConfig('creareseo/creareseositemap/showcategories');
	}

	public function showXMLSitemap()
	{
		return Mage::getStoreConfig('creareseo/creareseositemap/showxml');
	}

	public function showAccount()
	{
		return Mage::getStoreConfig('creareseo/creareseositemap/showaccount');
	}

	public function showContact()
	{
		return Mage::getStoreConfig('creareseo/creareseositemap/showcontact');
	}
}