<?php
class VES_Vendors_Helper_Catalog_Product_Configuration extends Mage_Core_Helper_Abstract
{
	public function getOptionById($optionId){
		$option = Mage::getResourceModel('eav/entity_attribute_option_collection')
		->setPositionOrder('asc')
		->addFieldToFilter('tdv.option_id',$optionId)
		->setStoreFilter()
		->load()
		->getFirstItem();
		return $option;
	}
	/**
	 * Retrieves product configuration options
	 *
	 * @param Mage_Catalog_Model_Product_Configuration_Item_Interface $item
	 * @return array
	 */
	public function getCustomOptions(VES_Vendors_Model_Item $item,Mage_Catalog_Model_Product $product)
	{
		switch($product->getTypeId()){
			case 'configurable':
				return $this->getConfigurableOptions($item);
			case 'simple':
				return $this->getSimpleOptions($item,$product);
			case 'bundle':
				return $this->getBundleProductOptions($item,$product);
		}
	}
	
	public function getBundleProductOptions($item,$product){
		return array_merge(
            $this->getBundleOptions($item,$product),
            $this->getSimpleOptions($item,$product)
        );
	}
	/**
     * Get selection quantity
     *
     * @param Mage_Catalog_Model_Product $product
     * @param int $selectionId
     * @return decimal
     */
    public function getSelectionQty($product, $selectionId)
    {
        $selectionQty = $product->load($product->getId())->getCustomOption('selection_qty_' . $selectionId);
        if ($selectionQty) {
            return $selectionQty->getValue();
        }
        return 0;
    }
	public function getBundleOptions($item,$product){
		$additionData = unserialize($item->getAdditionData());
		$options = array();

        /**
         * @var Mage_Bundle_Model_Product_Type
         */
        $typeInstance = $product->getTypeInstance(true);

        // get bundle options
        $bundleOptionData = $additionData['bundle_option'];
        $bundleOptionsIds = array_keys($bundleOptionData);
        if ($bundleOptionsIds) {
            /**
            * @var Mage_Bundle_Model_Mysql4_Option_Collection
            */
            $optionsCollection = $typeInstance->getOptionsByIds($bundleOptionsIds, $product);
            // get and add bundle selections collection
            $selectionsQuoteItemOption = array_values($bundleOptionData);
			
            $selectionsCollection = $typeInstance->getSelectionsByIds(
               	$selectionsQuoteItemOption,
                $product
            );

            $bundleOptions = $optionsCollection->appendSelections($selectionsCollection, true);
            foreach ($bundleOptions as $bundleOption) {
                if ($bundleOption->getSelections()) {
                    $option = array(
                        'label' => $bundleOption->getTitle(),
                        'value' => array()
                    );

                    $bundleSelections = $bundleOption->getSelections();

                    foreach ($bundleSelections as $bundleSelection) {
                        
                    	$option['value'][] =$this->escapeHtml($bundleSelection->getName());
                                
                    }

                    if ($option['value']) {
                        $options[] = $option;
                    }
                }
            }
        }

        return $options;
	}
	public function getConfigurableOptions($item){
		$options = array();
		$additionData = unserialize($item->getAdditionData());
		$superAttributes	= $additionData['super_attribute'];
		if ($superAttributes) {
			$options = array();
			foreach ($superAttributes as $attributeId=>$optionId) {
				$attribute = Mage::getModel('eav/entity_attribute')->load($attributeId);

				$option = $this->getOptionById($optionId);
				$options[] = array(
						'label' => $attribute->getFrontendLabel(),
						'value' => $option->getDefaultValue(),
						'print_value' => $option->getDefaultValue(),
						'option_id' => $option->getId(),
						'option_type' => $option->getType(),
				);
			}
		}
		return $options;
	}
	
	public function getSimpleOptions($item,$product)
    {
        $options = array();
        $additionData = unserialize($item->getAdditionData());
        $optionData = $additionData['options'];
        $optionIds = array_keys($optionData);
        if ($optionIds) {
            $options = array();
            foreach ($optionIds as $optionId) {
                $option = $product->getOptionById($optionId);
                if ($option) {
                    $itemOption = $item->getOptionByCode('option_' . $option->getId());
                    $group = $option->groupFactory($option->getType())
                        ->setOption($option)
                        ->setConfigurationItem($item)
                        ->setConfigurationItemOption($itemOption);


                    $options[] = array(
                        'label' => $option->getTitle(),
                        'value' => $group->getFormattedOptionValue($optionData[$optionId]),
                        'print_value' => $group->getPrintableOptionValue($optionData[$optionId]),
                        'option_id' => $option->getId(),
                        'option_type' => $option->getType(),
                        'custom_view' => $group->isCustomizedView()
                    );
                }
            }
        }

        $addOptions = $item->getOptionByCode('additional_options');
        if ($addOptions) {
            $options = array_merge($options, unserialize($addOptions->getValue()));
        }

        return $options;
    }
}
