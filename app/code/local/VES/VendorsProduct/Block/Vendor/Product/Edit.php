<?php
/**
 * Customer edit block
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsProduct_Block_Vendor_Product_Edit extends Mage_Adminhtml_Block_Template
{
    protected function _prepareLayout()
    {
    	parent::_prepareLayout();
    	/*Add class for save and continue edit button and reset button*/
        $editBlock = $this->getLayout()->getBlock('product_edit');
        if($editBlock){
        	$editBlock->setTemplate('ves_vendorsproduct/product/edit.phtml');
        	if($saveAndEditBtn = $editBlock->getChild('save_and_edit_button'))
				$saveAndEditBtn->setData('class','scalable save-and-continue');
        	if($resetBtn = $editBlock->getChild('reset_button'))
				$resetBtn->setData('class','scalable reset');
        	if(Mage::registry('product')->getId()){
        		
        		/*------------Remove duplicate button------------------------*/
        		/*$editBlock->unsetChild('duplicate_button');*/
        		
	        	$editBlock->setChild('submit_button',
	                $this->getLayout()->createBlock('adminhtml/widget_button')
	                    ->setData(array(
	                        'label'     => Mage::helper('vendorsproduct')->__('Submit For Approval'),
	                        'onclick'   => 'setLocation(\''.$this->getUrl('vendors/catalog_product/submitforapproval',array('id'=>$editBlock->getProduct()->getId())).'\')',
	                        'class' => 'add submit'
	                    ))
	            );
				
				if (!$this->getRequest()->getParam('popup')) {
						//echo Mage::app()->getStore()->getId();exit;
						$editBlock->getChild('back_button')->setData('onclick','setLocation(\''.$this->getUrl('*/*/').'\')');
				}
        	}
        }
    	
        return $this;
    }
	/**
     * Translate html content
     *
     * @param string $html
     * @return string
     */
    protected function _translateHtml($html)
    {
        Mage::getSingleton('core/translate_inline')->processResponseBody($html);
        return $html;
    }
}
