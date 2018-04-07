<?php
/**
 * Base vendor controller
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_Vendors_Controller_Action extends Mage_Core_Controller_Front_Action
{
    /**
     * Used module name in current adminhtml controller
     */
    protected $_usedModuleName = 'vendors';

	/**
     * Retrieve vendor session model object
     *
     * @return VES_Vendors_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('vendors/session');
    }
    
	protected function _isAllowed()
    {
        return true;
    }
    
	/**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        /*Return 404 error if the extension is not activated.*/
        if(!Mage::helper('vendors')->moduleEnabled()){
            $this->_forward('no-route');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return $this;
        }
        
        // a brute-force protection here would be nice
        parent::preDispatch();
		Mage::dispatchEvent('vendors_controller_pre_dispatch_before',array('action'=>$this));
        if (!$this->getRequest()->isDispatched()) {
            return;
        }

        $action = $this->getRequest()->getActionName();
        $openActions = array(
        	
        );
        $controller = $this->getRequest()->getControllerName();
        $pattern = '/^(' . implode('|', $openActions) . ')/i';

        if (!preg_match($pattern, $action) || $controller!='index') {
            if (!$this->_getSession()->authenticate($this)) {
                $this->setFlag('', 'no-dispatch', true);
            }
        } else {
            $this->_getSession()->setNoReferer(true);
        }
        
    	if ($this->_getSession()->isLoggedIn() && $this->getRequest()->isDispatched()
            && $this->getRequest()->getActionName() !== 'no-route'
            && !$this->_isAllowed()) {
            $this->_forward('no-route');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return $this;
        }
        if(!Mage::registry('isSecureArea')){
        	Mage::register('isSecureArea',true);
        }
        /*Set to adminhtml area and default package.*/
    	Mage::getSingleton('core/design_package')->setArea('adminhtml')->setPackageName(Mage_Core_Model_Design_Package::DEFAULT_PACKAGE)->setTheme(Mage_Core_Model_Design_Package::DEFAULT_THEME);
        //Mage::app()->setCurrentStore('admin');
		if(!$this->_getSession()->getLocale()){
        	$this->_getSession()->setLocale(Mage::app()->getLocale()->getLocaleCode());
        }
        Mage::app()->getTranslator()->setLocale($this->_getSession()->getLocale())->init('adminhtml');
		
        Mage::dispatchEvent('vendors_controller_pre_dispatch',array('action'=>$this));
    }
	/**
     * Define active menu item in menu block
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _setActiveMenu($menuPath)
    {
        $this->getLayout()->getBlock('vendor.menu')->setActive($menuPath);
        return $this;
    }
	/**
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _addBreadcrumb($label, $title, $link=null,$class=null)
    {
        $this->getLayout()->getBlock('vendor.breadcrumbs')->addLink($label, $title, $link,$class);
        return $this;
    }
	/**
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _addContent(Mage_Core_Block_Abstract $block)
    {
        $this->getLayout()->getBlock('content')->append($block);
        return $this;
    }

    protected function _addLeft(Mage_Core_Block_Abstract $block)
    {
        $this->getLayout()->getBlock('left')->append($block);
        return $this;
    }
    /**
     * Set Page Title
     * @param string $pageTitle
     */
    protected function _setPageTitle($pageTitle){
    	$headBlock = $this->getLayout()->getBlock('head');
    	$headBlock->setTitle($pageTitle.' / '.Mage::helper('vendors')->__('Vendor Cpanel'));
    	return $this;
    }
    /**
     * Add an extra title to the end or one from the end, or remove all
     *
     * Usage examples:
     * $this->_title('foo')->_title('bar');
     * => bar / foo / <default title>
     *
     * $this->_title()->_title('foo')->_title('bar');
     * => bar / foo
     *
     * $this->_title('foo')->_title(false)->_title('bar');
     * bar / <default title>
     *
     * @see self::_renderTitles()
     * @param string|false|-1|null $text
     * @param bool $resetIfExists
     * @return Mage_Core_Controller_Varien_Action
     */
    protected function _title($text = null, $resetIfExists = true){
    	return parent::_title($text, $resetIfExists);
    }
    
	public function loadLayout($ids=null, $generateBlocks=true, $generateXml=true)
    {
        parent::loadLayout($ids, $generateBlocks, $generateXml);
        $this->_initLayoutMessages('vendors/session');
        return $this;
    }

    /**
     * Set currently used module name
     *
     * @param string $moduleName
     */
    public function setUsedModuleName($moduleName)
    {
        $this->_usedModuleName = $moduleName;
        return $this;
    }
}
