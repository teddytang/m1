<?php

class VES_Vendors_CssController extends Mage_Core_Controller_Front_Action
{
	/**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        // a brute-force protection here would be nice
        parent::preDispatch();
		
        if(!Mage::registry('isSecureArea')){
        	Mage::register('isSecureArea',true);
        }
        /*Set to adminhtml area and default package.*/
    	Mage::getSingleton('core/design_package')->setArea('adminhtml')->setPackageName(Mage_Core_Model_Design_Package::DEFAULT_PACKAGE)->setTheme(Mage_Core_Model_Design_Package::DEFAULT_THEME);
    }
    
	public function indexAction(){
		$this->getResponse()->setHeader('Content-type', 'text/css'); 
    	$this->getResponse()->setBody($this->getLayout()->createBlock('core/template','css.block',array('template'=>'ves_vendors/css.phtml'))->toHtml());
	}	
}