<?php

/**
 * Vendor toplinks block
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <core@magentocommerce.com>
 */
class VES_Vendors_Block_Page_Template_Links extends Mage_Page_Block_Template_Links
{
    protected $_cacheKeyInfo = null;
	public function __construct()
    {
        parent::__construct();
        $this->setTemplate('ves_vendors/page/template/links.phtml');
    }
	/**
     * Get cache key informative items
     * Provide string array key to share specific info item with FPC placeholder
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        if (is_null($this->_cacheKeyInfo)) {
            $links = array();
            if (!empty($this->_links)) {
                foreach ($this->_links as $position => $link) {
                    if ($link instanceof Varien_Object) {
                        $links[$position] = $link->getData();
                    }
                }
            }
            $this->_cacheKeyInfo = parent::getCacheKeyInfo() + array(
                'links' => base64_encode(serialize($links)),
                'name' => $this->getNameInLayout()
            );
        }

        return $this->_cacheKeyInfo;
    }
	/**
     * Add link to the list
     *
     * @param string $label
     * @param string $url
     * @param string $title
     * @param boolean $prepare
     * @param array $urlParams
     * @param int $position
     * @param string|array $liParams
     * @param string|array $aParams
     * @param string $beforeText
     * @param string $afterText
     * @return Mage_Page_Block_Template_Links
     */
    public function addLink($label, $url='', $title='', $prepare=false, $urlParams=array(),
        $position=null, $liParams=null, $aParams=null, $beforeText='', $afterText='', $aBeforeText='', $aAfterText='')
    {
        if (is_null($label) || false===$label) {
            return $this;
        }
        $link = new Varien_Object(array(
            'label'         => $label,
            'url'           => ($prepare ? $this->getUrl($url, (is_array($urlParams) ? $urlParams : array())) : $url),
            'title'         => $title,
            'li_params'     => $this->_prepareParams($liParams),
            'a_params'      => $this->_prepareParams($aParams),
            'before_text'   => $beforeText,
            'after_text'    => $afterText,
        	'a_before_text'   => $aBeforeText,
            'a_after_text'    => $aAfterText,
        ));

        $this->_links[$this->_getNewPosition($position)] = $link;
        if (intval($position) > 0) {
             ksort($this->_links);
        }

        return $this;
    }
}
