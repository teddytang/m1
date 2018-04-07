<?php

class VES_VendorsConfig_Block_Config_Edit_Tabs extends Mage_Adminhtml_Block_Widget
{
	/**
     * tabs structure
     *
     * @var array
     */
	protected $_tabs = array();
	
	
	/**
     * Active tab key
     *
     * @var string
     */
    protected $_activeTab = null;
    
  	public function __construct()
  	{
		parent::__construct();
		$this->setId('vendorsconfiguration_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('vendorsconfig')->__('Configuration'));
		$this->setTemplate('ves_vendorsconfig/widget/tabs.phtml');
  	}
  	
  	/**
  	 * Get all sections
  	 */
  	public function getSections(){
  		return Mage::helper('vendorsconfig')->getConfig()->getSections();
  	}
  	/**
     * Add new tab after another
     *
     * @param   string $tabId new tab Id
     * @param   array|Varien_Object $tab
     * @param   string $afterTabId
     * @return  Mage_Adminhtml_Block_Widget_Tabs
     */
    public function addTabAfter($tabId, $tab, $afterTabId)
    {
        $this->addTab($tabId, $tab);
        $this->_tabs[$tabId]->setAfter($afterTabId);
    }

    /**
     * Add new tab
     *
     * @param   string $tabId
     * @param   array|Varien_Object $tab
     * @return  Mage_Adminhtml_Block_Widget_Tabs
     */
    public function addTab($tabId, $tab)
    {
        if (is_array($tab)) {
            $this->_tabs[$tabId] = new Varien_Object($tab);
        }
        elseif ($tab instanceof Varien_Object) {
            $this->_tabs[$tabId] = $tab;
            if (!$this->_tabs[$tabId]->hasTabId()) {
                $this->_tabs[$tabId]->setTabId($tabId);
            }
        }
        elseif (is_string($tab)) {
            if (strpos($tab, '/')) {
                $this->_tabs[$tabId] = $this->getLayout()->createBlock($tab);
            }
            elseif ($this->getChild($tab)) {
                $this->_tabs[$tabId] = $this->getChild($tab);
            }
            else {
                $this->_tabs[$tabId] = null;
            }

            if (!($this->_tabs[$tabId] instanceof Mage_Adminhtml_Block_Widget_Tab_Interface)) {
                throw new Exception(Mage::helper('adminhtml')->__('Wrong tab configuration.'));
            }
        }
        else {
            throw new Exception(Mage::helper('adminhtml')->__('Wrong tab configuration.'));
        }

        if (is_null($this->_tabs[$tabId]->getUrl())) {
            $this->_tabs[$tabId]->setUrl('#');
        }

        if (!$this->_tabs[$tabId]->getTitle()) {
            $this->_tabs[$tabId]->setTitle($this->_tabs[$tabId]->getLabel());
        }

        $this->_tabs[$tabId]->setId($tabId);
        $this->_tabs[$tabId]->setTabId($tabId);

        if (is_null($this->_activeTab)) $this->_activeTab = $tabId;
        if (true === $this->_tabs[$tabId]->getActive()) $this->setActiveTab($tabId);

        return $this;
    }
  	
  	
	/**
  	 * Sort element by sort_order
  	 */
  	protected function _sortElements($sections){
	  	$sortedSections = array();
	  	$previousOrder = 0;
	  	foreach($sections as $key=>$section){
	  		$index = isset($section['sort_order']) && $section['sort_order']?$section['sort_order']:++$previousOrder;
	  		$section['element_id'] = $key;
	  		if(!isset($sortedSections[$index])){
	  			$sortedSections[$index] = $section;
	  		}else{
	  			while(isset($sortedSections[$index])){
	  				$index++;
	  			}
	  			$sortedSections[$index] = $section;
	  		}
	  		$previousOrder = $index;
	  	}
	  	ksort($sortedSections);
	  	return $sortedSections;
  	}
  	
	public function getActiveTabId()
    {
        return $this->getTabId($this->_tabs[$this->_activeTab]);
    }

    /**
     * Set Active Tab
     * Tab has to be not hidden and can show
     *
     * @param string $tabId
     * @return Mage_Adminhtml_Block_Widget_Tabs
     */
    public function setActiveTab($tabId)
    {
        $this->_setActiveTab($tabId);
        return $this;
    }
	public function canShowTab($tab)
    {
        return true;
    }
    /**
     * Set Active Tab
     *
     * @param string $tabId
     * @return Mage_Adminhtml_Block_Widget_Tabs
     */
    protected function _setActiveTab($tabId)
    {
        foreach ($this->_tabs as $id => $tab) {
            if ($tab->getTabId() == $tabId) {
                $this->_activeTab = $id;
                $tab->setActive(true);
                return $this;
            }
        }
        return $this;
    }
  	protected function _prepareTabs()
  	{
		$sections = $this->_sortElements($this->getSections());
		foreach($sections as $section){
  	  		$key = $section['element_id'];
  	  		$helper = Mage::helper('vendorsconfig');
  	  		if(isset($section['@'])){
  	  			if(isset($section['@']['module'])){
  	  				$helper = Mage::helper($section['@']['module']);
  	  			}
  	  		}
  	  		$this->addTab($key, array(
	          'label'     	=> $helper->__($section['label']),
	          'title'     	=> $helper->__($section['label']),
	          'url'   		=> Mage::getUrl('vendors/config/index',array('section'=>$key)),
	      	));
		}
		return parent::_beforeToHtml();
  	}
  	
	protected function _beforeToHtml()
    {
    	$this->_prepareTabs();
    	
        if ($activeTab = $this->getRequest()->getParam('section')) {
            $this->setActiveTab($activeTab);
        } elseif ($activeTabId = Mage::getSingleton('admin/session')->getActiveTabId()) {
            $this->_setActiveTab($activeTabId);
        }else{
        	$firstTabId = key($this->_tabs);
        	$this->_setActiveTab($firstTabId);
        }

		
        $_new = array();
        foreach( $this->_tabs  as $key => $tab ) {
            foreach( $this->_tabs  as $k => $t ) {
                if( $t->getAfter() == $key ) {
                    $_new[$key] = $tab;
                    $_new[$k] = $t;
                } else {
                    if( !$tab->getAfter() || !in_array($tab->getAfter(), array_keys($this->_tabs)) ) {
                        $_new[$key] = $tab;
                    }
                }
            }
        }

        $this->_tabs = $_new;
        unset($_new);

        $this->assign('tabs', $this->_tabs);
        return parent::_beforeToHtml();
    }
}