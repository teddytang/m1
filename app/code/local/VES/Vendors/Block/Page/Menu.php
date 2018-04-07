<?php
/**
 * Vendor menu block
 *
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <core@magentocommerce.com>
 */
class VES_Vendors_Block_Page_Menu extends Mage_Adminhtml_Block_Page_Menu
{
    const CACHE_TAGS = 'VENDOR_MAINMENU';

    /**
     * Adminhtml URL instance
     *
     * @var Mage_Adminhtml_Model_Url
     */
    protected $_url;

    /**
     * Initialize template and cache settings
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ves_vendors/page/menu.phtml');
        $this->_url = Mage::getModel('core/url');
        $this->setCacheTags(array(self::CACHE_TAGS));
    }


    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $cacheKeyInfo = array(
            'vendor_top_nav',
            $this->getActive(),
            Mage::getSingleton('vendors/session')->getVendor()->getId(),
            Mage::app()->getLocale()->getLocaleCode()
        );
        // Add additional key parameters if needed
        $additionalCacheKeyInfo = $this->getAdditionalCacheKeyInfo();
        if (is_array($additionalCacheKeyInfo) && !empty($additionalCacheKeyInfo)) {
            $cacheKeyInfo = array_merge($cacheKeyInfo, $additionalCacheKeyInfo);
        }
        return $cacheKeyInfo;
    }

    /**
     * Retrieve Title value for menu node
     *
     * @param Varien_Simplexml_Element $child
     * @return string
     */
    protected function _getHelperValue(Varien_Simplexml_Element $child)
    {
        $helperName         = 'vendors';
        $titleNodeName      = 'title';
        $childAttributes    = $child->attributes();
        if (isset($childAttributes['module'])) {
            $helperName     = (string)$childAttributes['module'];
        }
        return Mage::helper($helperName)->__((string)$child->$titleNodeName);
    }

    /**
     * Recursive Build Menu array
     *
     * @param Varien_Simplexml_Element $parent
     * @param string $path
     * @param int $level
     * @return array
     */
    protected function _buildMenuArray(Varien_Simplexml_Element $parent=null, $path='', $level=0)
    {
        if (is_null($parent)) {
            $parent = Mage::getConfig()->getNode('vendors/menu');
        }

        $parentArr = array();
        $sortOrder = 0;
        foreach ($parent->children() as $childName => $child) {
            if (1 == $child->disabled) {
                continue;
            }
            
            $aclResource = 'vendors/' . ($child->resource ? (string)$child->resource : $path . $childName);
            if (!$this->_checkAcl($aclResource)) {
                continue;
            }

            if ($child->depends && !$this->_checkDepends($child->depends)) {
                continue;
            }

            $menuArr = array();

            $menuArr['label'] = $this->_getHelperValue($child);
			$menuArr['frontend_class'] = $child->frontend_class;
            $menuArr['sort_order'] = $child->sort_order ? (int)$child->sort_order : $sortOrder;

            if ($child->action) {
                $menuArr['url'] = $this->_url->getUrl((string)$child->action);
            } else {
                $menuArr['url'] = '#';
                $menuArr['click'] = 'return false';
            }

            $menuArr['active'] = ($this->getActive()==$path.$childName)
                || (strpos($this->getActive(), $path.$childName.'/')===0);

            $menuArr['level'] = $level;

            if ($child->children) {
                $menuArr['children'] = $this->_buildMenuArray($child->children, $path.$childName.'/', $level+1);
            }
            $parentArr[$childName] = $menuArr;

            $sortOrder++;
        }

        uasort($parentArr, array($this, '_sortMenu'));

        while (list($key, $value) = each($parentArr)) {
            $last = $key;
        }
        if (isset($last)) {
            $parentArr[$last]['last'] = true;
        }

        return $parentArr;
    }

    /*protected function _checkAcl(Varien_Simplexml_Element $acl)
    {
        return true;
        $resource = (string)$acl->resource;
        $privilege = (string)$acl->privilege;
        return Mage::getSingleton('admin/session')->isAllowed($resource, $privilege);
    }*/

    /**
     * Check is Allow menu item for vendor user
     *
     * @param string $resource
     * @return bool
     */
    protected function _checkAcl($resource)
    {
    	/* Disable ACL for now */
        /*
    	try {
            $res =  Mage::getSingleton('admin/session')->isAllowed($resource);
        } catch (Exception $e) {
            return false;
        }
        */
    	$result = new Varien_Object(array('is_allowed'=>true));
    	Mage::dispatchEvent('ves_vendor_menu_check_acl',array('resource'=>$resource,'result'=>$result));
    	
        return $result->getIsAllowed();
    }
    
	/**
     * Get menu level HTML code
     *
     * @param array $menu
     * @param int $level
     * @return string
     */
    public function getMenuLevel($menu, $level = 0)
    {
        $html = '<ul ' . (!$level ? 'id="nav"' : '') . '>' . PHP_EOL;
        foreach ($menu as $item) {
            $html .= '<li ' . (!empty($item['children']) ? 'onmouseover="Element.addClassName(this,\'over\')" '
                . 'onmouseout="Element.removeClassName(this,\'over\')"' : '') . ' class="'
                . (!empty($item['frontend_class'])?$item['frontend_class']:'').' '
                . (!$level && !empty($item['active']) ? ' active' : '') . ' '
                . (!empty($item['children']) ? ' parent' : '')
                . (!empty($level) && !empty($item['last']) ? ' last' : '')
                . ' level' . $level . '"> <a href="' . $item['url'] . '" '
                . (!empty($item['title']) ? 'title="' . $item['title'] . '"' : '') . ' '
                . (!empty($item['click']) ? 'onclick="' . $item['click'] . '"' : '') . ' class="'
                . ($level === 0 && !empty($item['active']) ? 'active' : '') . '"><span>'
                . $this->escapeHtml($item['label']) . '</span></a>' . PHP_EOL;

            if (!empty($item['children'])) {
                $html .= $this->getMenuLevel($item['children'], $level + 1);
            }
            $html .= '</li>' . PHP_EOL;
        }
        $html .= '</ul>' . PHP_EOL;

        return $html;
    }
}
