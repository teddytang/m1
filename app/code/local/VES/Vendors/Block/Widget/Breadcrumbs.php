<?php
/**
 * Vendors page breadcrumbs
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_Vendors_Block_Widget_Breadcrumbs extends Mage_Adminhtml_Block_Template
{
	/**
     * breadcrumbs links
     *
     * @var array
     */
    protected $_links = array();
    public function __construct()
    {
        $this->setTemplate('ves_vendors/widget/breadcrumbs.phtml');
        $this->addLink(Mage::helper('vendors')->__('Home'), Mage::helper('vendors')->__('Home'), Mage::helper('vendors')->getDashboardUrl(),'home');
    }
	public function addLink($label, $title=null, $url=null, $class=null)
    {
        if (empty($title)) {
            $title = $label;
        }
        $this->_links[] = array(
            'label' => $label,
            'title' => $title,
            'url'   => $url,
        	'class' => $class,
        );
        return $this;
    }
	protected function _beforeToHtml()
    {
        // TODO - Moved to Beta 2, no breadcrumbs displaying in Beta 1
        $this->assign('links', $this->_links);
        return parent::_beforeToHtml();
    }
}
