<?php
/**
 * Vendor form element
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_Vendors_Block_Form_Element_File extends Varien_Data_Form_Element_Image
{
/**
     * Get image preview url
     *
     * @return string
     */
    protected function _getUrl()
    {
        return Mage::getDesign()->getSkinUrl('ves_vendors/images/fam_page_white.gif');
    }
    
    /**
     * Get the url to imported file.
     * @return string
     */
    public function getFileUrl(){
        return Mage::getBaseUrl('media').$this->getValue();
    }
    /**
     * Return element html code
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = '';
    
        if ((string)$this->getValue()) {
            $url = $this->_getUrl();
    
            if( !preg_match("/^http\:\/\/|https\:\/\//", $url) ) {
                $url = Mage::getBaseUrl('media') . $url;
            }
    
            $html = '<a href="' . $this->getFileUrl() . '"'
                . ' target="_blank">'
                    . '<img src="' . $url . '" id="' . $this->getHtmlId() . '_image" title="' . $this->getValue() . '"'
                        . ' alt="' . $this->getValue() . '" height="22" width="22" class="small-image-preview v-middle" />'
                            . '</a> ';
        }
        $this->setClass('input-file');
        $html .= Varien_Data_Form_Element_Abstract::getElementHtml();
        $html .= $this->_getDeleteCheckbox();
    
        return $html;
    }
    
    /**
     * Return html code of delete checkbox element
     *
     * @return string
     */
    protected function _getDeleteCheckbox()
    {
        $html = '';
        if ($this->getValue()) {
            $label = Mage::helper('core')->__('Delete File');
            $html .= '<span class="delete-image">';
            $html .= '<input type="checkbox"'
                . ' name="' . parent::getName() . '[delete]" value="1" class="checkbox"'
                    . ' id="' . $this->getHtmlId() . '_delete"' . ($this->getDisabled() ? ' disabled="disabled"': '')
                    . '/>';
            $html .= '<label for="' . $this->getHtmlId() . '_delete"'
                . ($this->getDisabled() ? ' class="disabled"' : '') . '> ' . $label . '</label>';
            $html .= $this->_getHiddenInput();
            $html .= '</span>';
        }
    
        return $html;
    }
    
    public function getHtmlId()
    {
        if($this->getForm()) return $this->getForm()->getHtmlIdPrefix() . $this->getData('html_id') . $this->getForm()->getHtmlIdSuffix();
        return $this->getData('id');
    }
}
