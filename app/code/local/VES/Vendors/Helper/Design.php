<?php

class VES_Vendors_Helper_Design extends Mage_Core_Helper_Abstract
{
	public function getConfig($configId){
		return Mage::getStoreConfig('vendors/config/'.$configId);
	}
	
	public function getLighterColor($color){
		if(!preg_match('/^#?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i', $color, $parts)) return 'FFFFFF';
		$out = ""; // Prepare to fill with the results
		for($i = 1; $i <= 3; $i++) {
  			$parts[$i] = hexdec($parts[$i]);
  			$parts[$i] = round($parts[$i] * 1.7);
  			if($parts[$i] > 255) $parts[$i] = 255;
  			
  			$out .= str_pad(dechex($parts[$i]), 2, '0', STR_PAD_LEFT);
		}
		
		return $out;
	}
}