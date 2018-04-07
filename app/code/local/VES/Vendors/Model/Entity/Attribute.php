<?php
class VES_Vendors_Model_Entity_Attribute extends Mage_Eav_Model_Entity_Attribute
{
    /**
     * Detect backend storage type using frontend input type
     *
     * @return string backend_type field value
     * @param string $type frontend_input field value
     */
    public function getBackendTypeByInput($type)
    {
        $field = null;
        switch ($type) {
            case 'text':
            case 'gallery':
            case 'media_image':
            case 'multiselect':
            case 'file':
                $field = 'varchar';
                break;

            case 'image':
            case 'textarea':
                $field = 'text';
                break;

            case 'date':
                $field = 'datetime';
                break;

            case 'select':
            case 'boolean':
                $field = 'int';
                break;

            case 'price':
                $field = 'decimal';
                break;
        }

        return $field;
    }
    
    /**
     * Prepare data for save
     *
     * @return Mage_Eav_Model_Entity_Attribute
     * @throws Mage_Eav_Exception
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        
        if ($this->getFrontendInput() == 'file') {
            if (!$this->getBackendModel()) {
                $this->setBackendModel('vendors/vendor_attribute_backend_file');
            }
        }
        
        return $this;
    }
}
