<?php

class Creare_CreareSeoCore_Model_Cleanup extends Mage_Core_Model_Abstract
{
	
	protected $errors = array();
	
	public function docleanup()
	{	
		if(Mage::getStoreConfig('creareseo/cleanup/cachefolder')){
			$this->cleanVar();
			$this->cleanCache();
		}
		
		if(Mage::getStoreConfig('creareseo/cleanup/logdb')){
			$this->cleanDb();
		}
		
		if(Mage::getStoreConfig('creareseo/cleanup/enablecache')){
			$this->enableCache();
		}
		
		if(count($this->errors) > 0){
			$this->sendEmail();
		} else {
			Mage::getConfig()->saveConfig('creareseo/cleanup/success', date('Y-m-d, H:i:s'));
			Mage::getConfig()->reinit();
			Mage::app()->reinitStores();	
		}
		
	}
	
	private function enableCache()
	{
		$db = Mage::getSingleton('core/resource')->getConnection('write');
		$sql = $db->query('SELECT * FROM core_cache_option WHERE value = 0');
		$results = $sql->fetchAll();
		if($results){
			foreach($results as $result){
				$db->query('UPDATE core_cache_option SET value = 1 WHERE code = "'.$result['code'].'"');
			}
		}
	}
	
	private function cleanDb()
	{
		$tables = array(
			'dataflow_batch_export',
			'dataflow_batch_import',
			'log_customer',
			'log_quote',
			'log_summary',
			'log_summary_type',
			'log_url',
			'log_url_info',
			'log_visitor',
			'log_visitor_info',
			'log_visitor_online',
			'report_event'
		);
		
		$db = Mage::getSingleton('core/resource')->getConnection('read');
		$prefix = Mage::getConfig()->getTablePrefix();
		foreach($tables as $table){
			$db->query('TRUNCATE '.$prefix.$table);
		}
		
	}
	
	private function cleanCache()
	{
		$dir = Mage::getBaseDir()."/var/cache/";
		if(is_dir($dir)){
			$this->removeContents($dir);
		}
	}
	
	private function cleanVar()
	{	
		$dir = Mage::getBaseDir().'/var/session/';	
		$this->removeSessionContents($dir);
	}
	
	private function removeContents($dir)
	{
		
		if(!opendir($dir)){
			$this->errors[] = "Cannot open $dir";	
		}
		$mydir = opendir($dir);
		while(false !== ($file = readdir($mydir))) :
		
			if($file != "." && $file != ".."){
				chmod($dir.$file, 0777);
				if(is_dir($dir.$file)) {
					chdir('.');
					$this->removeContents($dir.$file."/");
					
					if(!rmdir($dir.$file)){
						$this->errors[] = "Could not delete folder $dir$file";
					}
				} else {
					chmod($dir.$file, 0777);
					if(!unlink($dir.$file)){
						$this->errors[] = "Could not delete file $dir$file";
					}
				}
			}
			
		endwhile;
		closedir($mydir);	
	}
	
	private function removeSessionContents($dir)
	{
		if(!opendir($dir)){
			$this->errors[] = "Cannot open $dir";	
		}
		$mydir = opendir($dir);
		while(false !== ($file = readdir($mydir))) :
		
			if($file != "." && $file != ".."){
				chmod($dir.$file, 0777);
				if(is_dir($dir.$file)) {
					chdir('.');
					$this->removeContents($dir.$file."/");
					
					if(!rmdir($dir.$file)){
						$this->errors[] = "Could not delete folder $dir$file";
					}
				} else {
					chmod($dir.$file, 0777);
					
					// check to make sure that session file is over a week old
					
					$time = filemtime($dir.$file);
					
					if($time < (time() - (7 * 24 * 60 * 60))){
						if(!unlink($dir.$file)){
							$this->errors[] = "Could not delete file $dir$file";
						}
					}
				}
			}
			
		endwhile;
		closedir($mydir);
	}

	private function sendEmail()
	{
		if($email = Mage::getStoreConfig('cleanup/settings/problems')):
		
			$content = "";
			
			foreach($this->errors as $error){
				$content .= $error."\n";	
			}

			mail($email,'Problems with Cleanup',$content);

		endif;
	}

}