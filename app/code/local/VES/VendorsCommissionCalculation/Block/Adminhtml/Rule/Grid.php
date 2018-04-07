<?php



class VES_VendorsCommissionCalculation_Block_Adminhtml_Rule_Grid extends Mage_Adminhtml_Block_Widget_Grid

{

  public function __construct()

  {

      parent::__construct();

      $this->setId('vendorsCommissionRuleGrid');

      $this->setDefaultSort('rule_id');

      $this->setDefaultDir('ASC');

      $this->setSaveParametersInSession(true);

  }



  protected function _prepareCollection()

  {

      $collection = Mage::getModel('vendorscommission/rule')->getCollection();

      $this->setCollection($collection);

      return parent::_prepareCollection();

  }



  protected function _prepareColumns()

  {

      $this->addColumn('rule_id', array(

            'header'    => Mage::helper('catalogrule')->__('ID'),

            'align'     =>'right',

            'width'     => '50px',

            'index'     => 'rule_id',

        ));



        $this->addColumn('name', array(

            'header'    => Mage::helper('catalogrule')->__('Rule Name'),

            'align'     =>'left',

            'index'     => 'name',

        ));



        $this->addColumn('from_date', array(

            'header'    => Mage::helper('catalogrule')->__('Date Start'),

            'align'     => 'left',

            'width'     => '120px',

            'type'      => 'date',

            'index'     => 'from_date',

        ));



        $this->addColumn('to_date', array(

            'header'    => Mage::helper('catalogrule')->__('Date Expire'),

            'align'     => 'left',

            'width'     => '120px',

            'type'      => 'date',

            'default'   => '--',

            'index'     => 'to_date',

        ));

        $this->addColumn('commission_by', array(

            'header'    => Mage::helper('catalogrule')->__('Commission By'),

            'align'     => 'left',

            'width'     => '150px',

            'index'     => 'commission_by',

            'type'      => 'options',

            'options'   => array(

                'by_fixed' => Mage::helper('vendorscommission')->__('Fixed Amount'),

                'by_percent' => Mage::helper('vendorscommission')->__('Percent Of Product Price'),

            ),

        ));

        $this->addColumn('commission_action', array(

            'header'    => Mage::helper('catalogrule')->__('Calculate Commission Based On'),

            'align'     => 'left',

            'width'     => '230px',

            'index'     => 'commission_action',

            'type'      => 'options',

            'options'   => array(

                'by_price_incl_tax' => Mage::helper('vendorscommission')->__('Product Price (Incl. Tax)'),

                'by_price_excl_tax' => Mage::helper('vendorscommission')->__('Product Price (Excl. Tax)'),

                'by_price_after_discount_incl_tax' => Mage::helper('vendorscommission')->__('Product Price After Discount (Incl. Tax)'),

                'by_price_after_discount_excl_tax' => Mage::helper('vendorscommission')->__('Product Price After Discount (Excl. Tax)'),

            ),

        ));

        $this->addColumn('commission_amount', array(

            'header'    => Mage::helper('catalogrule')->__('Commission'),

            'align'     =>'left',

            'index'     => 'commission_amount',

            'width'     => '40px',

        ));

        $this->addColumn('is_active', array(

            'header'    => Mage::helper('catalogrule')->__('Status'),

            'align'     => 'left',

            'width'     => '40px',

            'index'     => 'is_active',

            'type'      => 'options',

            'options'   => array(

                1 => Mage::helper('catalogrule')->__('Active'),

                0 => Mage::helper('catalogrule')->__('Inactive')

            ),

        ));



        if (!Mage::app()->isSingleStoreMode()) {

            $this->addColumn('rule_website', array(

                'header'    => Mage::helper('catalogrule')->__('Website'),

                'align'     =>'left',

                'index'     => 'website_ids',

                'type'      => 'options',

                'sortable'  => false,

                'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(),

                'width'     => 150,

            ));

        }



        parent::_prepareColumns();

        return $this;

  }



    protected function _prepareMassaction()

    {

        $this->setMassactionIdField('vendors_id');

        $this->getMassactionBlock()->setFormFieldName('vendors');



        $this->getMassactionBlock()->addItem('delete', array(

             'label'    => Mage::helper('vendors')->__('Delete'),

             'url'      => $this->getUrl('*/*/massDelete'),

             'confirm'  => Mage::helper('vendors')->__('Are you sure?')

        ));



        $statuses = Mage::getSingleton('vendorscommission/source_status')->getOptionArray();



        //array_unshift($statuses, array('label'=>'', 'value'=>''));

        $this->getMassactionBlock()->addItem('status', array(

             'label'=> Mage::helper('vendors')->__('Change status'),

             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),

             'additional' => array(

                    'visibility' => array(

                         'name' => 'status',

                         'type' => 'select',

                         'class' => 'required-entry',

                         'label' => Mage::helper('vendors')->__('Status'),

                         'values' => $statuses

                     )

             )

        ));

        return $this;

    }



  public function getRowUrl($row)

  {

      return $this->getUrl('*/*/edit', array('id' => $row->getId()));

  }



}