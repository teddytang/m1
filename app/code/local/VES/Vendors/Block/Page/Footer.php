<?php

/**
 * Vendor header block
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <core@magentocommerce.com>
 */
class VES_Vendors_Block_Page_Footer extends Mage_Adminhtml_Block_Page_Footer
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('ves_vendors/page/footer.phtml');
    }
    
	public function getChangeLocaleUrl()
    {
        return $this->getUrl('vendors/index/changeLocale');
    }
    
    public function canShowLanguageSwitcher(){
    	return Mage::getStoreConfig('vendors/config/footer_show_language_switcher');
    }
	public function getLanguageSelect()
    {    	
        $localeCode  = Mage::getSingleton('vendors/session')->getLocale();
        $locale = Mage::getModel('core/locale')->setLocaleCode($localeCode);
        $cacheId = self::LOCALE_CACHE_KEY . $localeCode;
        $html    = Mage::app()->loadCache($cacheId);

        if (!$html) {
            $html = $this->getLayout()->createBlock('adminhtml/html_select')
                ->setName('locale')
                ->setId('interface_locale')
                ->setTitle(Mage::helper('page')->__('Interface Language'))
                ->setExtraParams('style="width:200px"')
                ->setValue($locale->getLocaleCode())
                ->setOptions($locale->getTranslatedOptionLocales())
                ->getHtml();
            Mage::app()->saveCache($html, $cacheId, array(self::LOCALE_CACHE_TAG), self::LOCALE_CACHE_LIFETIME);
        }

        return $html;
    }
    
}
