<?php

class VES_VendorsImage_Block_Cms_Wysiwyg_Images_Tree extends Mage_Adminhtml_Block_Template
{

    /**
     * Json tree builder
     *
     * @return string
     */
    public function getTreeJson()
    {
        $helper = Mage::helper('vendorsimage/wysiwyg_images');
        $storageRoot = $helper->getStorageRoot();
        $collection = Mage::registry('storage')->getDirsCollection($helper->getCurrentPath());
        $jsonArray = array();
        foreach ($collection as $item) {
            $jsonArray[] = array(
                'text'  => $helper->getShortFilename($item->getBasename(), 20),
                'id'    => $helper->convertPathToId($item->getFilename()),
                'cls'   => 'folder'
            );
        }
        return Zend_Json::encode($jsonArray);
    }

    /**
     * Json source URL
     *
     * @return string
     */
    public function getTreeLoaderUrl()
    {
        return $this->getUrl('*/*/treeJson');
    }

    /**
     * Root node name of tree
     *
     * @return string
     */
    public function getRootNodeName()
    {
        return $this->helper('cms')->__('Storage Root');
    }

    /**
     * Return tree node full path based on current path
     *
     * @return string
     */
    public function getTreeCurrentPath()
    {
        $treePath = '/root';
 
        if ($path = Mage::registry('storage')->getSession()->getCurrentPath()) {
            $helper = Mage::helper('vendorsimage/wysiwyg_images');
            $path = str_replace($helper->getStorageRoot(), '', $path);
            $relative = '';
            foreach (explode(DS, $path) as $dirName) {
                if ($dirName) {
                    $relative .= DS . $dirName;
                    $treePath .= '/' . $helper->idEncode($relative);
                }
            }
        }
        return $treePath;
    }
}
