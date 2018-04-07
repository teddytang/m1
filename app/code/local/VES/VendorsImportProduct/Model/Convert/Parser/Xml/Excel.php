<?php

class VES_VendorsImportProduct_Model_Convert_Parser_Xml_Excel extends Mage_Dataflow_Model_Convert_Parser_Xml_Excel
{
	public function getVendor(){
		return Mage::getSingleton('vendors/session')->getVendor();;
	}
	
	public function parse()
    {
        $adapterName   = $this->getVar('adapter', null);
        $adapterMethod = $this->getVar('method', 'saveRow');

        if (!$adapterName || !$adapterMethod) {
            $message = Mage::helper('dataflow')->__('Please declare "adapter" and "method" nodes first.');
            $this->addException($message, Mage_Dataflow_Model_Convert_Exception::FATAL);
            return $this;
        }

        try {
            $adapter = Mage::getModel($adapterName);
        }
        catch (Exception $e) {
            $message = Mage::helper('dataflow')->__('Declared adapter %s was not found.', $adapterName);
            $this->addException($message, Mage_Dataflow_Model_Convert_Exception::FATAL);
            return $this;
        }

        if (!method_exists($adapter, $adapterMethod)) {
            $message = Mage::helper('dataflow')->__('Method "%s" was not defined in adapter %s.', $adapterMethod, $adapterName);
            $this->addException($message, Mage_Dataflow_Model_Convert_Exception::FATAL);
            return $this;
        }

        $batchModel = $this->getBatchModel();
        $batchIoAdapter = $this->getBatchModel()->getIoAdapter();

        if (Mage::app()->getRequest()->getParam('files')) {
                
            $vendor = $this->getVendor();
        	
        	$file = Mage::helper('vendorsimport')->getVendorImportFile(urldecode(Mage::app()->getRequest()->getParam('files')),$vendor);
            $this->_copy($file);
        }

        $batchIoAdapter->open(false);

        $isFieldNames = $this->getVar('fieldnames', '') == 'true' ? true : false;
        if (!$isFieldNames && is_array($this->getVar('map'))) {
            $this->_parseFieldNames = $this->getVar('map');
        }

        $worksheet = $this->getVar('single_sheet', '');

        $xmlString = $xmlRowString = '';
        $countRows = 0;
        $isWorksheet = $isRow = false;
        while (($xmlOriginalString = $batchIoAdapter->read()) !== false) {
            $xmlString .= $xmlOriginalString;
            if (!$isWorksheet) {
                $strposS = strpos($xmlString, '<Worksheet');
                $substrL = 10;
                //fix for OpenOffice
                if ($strposS === false) {
                    $strposS = strpos($xmlString, '<ss:Worksheet');
                    $substrL = 13;
                }
                if ($strposS === false) {
                    $xmlString = substr($xmlString, -13);
                    continue;
                }

                $xmlTmpString = substr($xmlString, $strposS);
                $strposF = strpos($xmlTmpString, '>');

                if ($strposF === false) {
                    $xmlString = $xmlTmpString;
                    continue;
                }

                if (!$worksheet) {
                    $xmlString = substr($xmlTmpString, $strposF);
                    $isWorksheet = true;
                    continue;
                }
                else {
                    if (preg_match('/ss:Name=\"'.preg_quote($worksheet).'\"/siU', substr($xmlTmpString, 0, $strposF))) {
                        $xmlString = substr($xmlTmpString, $strposF);
                        $isWorksheet = true;
                        continue;
                    }
                    else {
                        $xmlString = '';
                        continue;
                    }
                }
            }
            else {
                $xmlString = $this->_parseXmlRow($xmlString);

                $strposS = strpos($xmlString, '</Worksheet>');
                $substrL = 12;
                //fix for OpenOffice
                if ($strposS === false) {
                    $strposS = strpos($xmlString, '</ss:Worksheet>');
                    $substrL = 15;
                }
                if ($strposS !== false) {
                    $xmlString = substr($xmlString, $strposS + $substrL);
                    $isWorksheet = false;

                    continue;
                }
            }
        }

        $this->addException(Mage::helper('dataflow')->__('Found %d rows.', $this->_countRows));
        $this->addException(Mage::helper('dataflow')->__('Starting %s :: %s', $adapterName, $adapterMethod));

        $batchModel->setParams($this->getVars())
            ->setAdapter($adapterName)
            ->save();

//        $adapter->$adapterMethod();

        return $this;

        $dom = new DOMDocument();
//        $dom->loadXML($this->getData());
        if (Mage::app()->getRequest()->getParam('files')) {
            $path = Mage::app()->getConfig()->getTempVarDir().'/import/';
            $file = $path.urldecode(Mage::app()->getRequest()->getParam('files'));
            if (file_exists($file)) {
                $dom->load($file);
            }
        } else {

            $this->validateDataString();
            $dom->loadXML($this->getData());
        }

        $worksheets = $dom->getElementsByTagName('Worksheet');
        if ($this->getVar('adapter') && $this->getVar('method')) {
            $adapter = Mage::getModel($this->getVar('adapter'));
        }
        foreach ($worksheets as $worksheet) {
            $wsName = $worksheet->getAttribute('ss:Name');
            $rows = $worksheet->getElementsByTagName('Row');
            $firstRow = true;
            $fieldNames = array();
            $wsData = array();
            $i = 0;
            foreach ($rows as $rowSet) {
                $index = 1;
                $cells = $rowSet->getElementsByTagName('Cell');
                $rowData = array();
                foreach ($cells as $cell) {
                    $value = $cell->getElementsByTagName('Data')->item(0)->nodeValue;
                    $ind = $cell->getAttribute('ss:Index');
                    if (!is_null($ind) && $ind>0) {
                        $index = $ind;
                    }
                    if ($firstRow && !$this->getVar('fieldnames')) {
                        $fieldNames[$index] = 'column'.$index;
                    }
                    if ($firstRow && $this->getVar('fieldnames')) {
                        $fieldNames[$index] = $value;
                    } else {
                        $rowData[$fieldNames[$index]] = $value;
                    }
                    $index++;
                }
                $row = $rowData;
                if ($row) {
                    $loadMethod = $this->getVar('method');
                    $adapter->$loadMethod(compact('i', 'row'));
                }
                $i++;

                $firstRow = false;
                if (!empty($rowData)) {
                    $wsData[] = $rowData;
                }
            }
            $data[$wsName] = $wsData;
            $this->addException('Found worksheet "'.$wsName.'" with '.sizeof($wsData).' row(s)');
        }
        if ($wsName = $this->getVar('single_sheet')) {
            if (isset($data[$wsName])) {
                $data = $data[$wsName];
            } else {
                reset($data);
                $data = current($data);
            }
        }
        $this->setData($data);
        return $this;
    }
}