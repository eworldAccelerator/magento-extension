<?php

class EworldAccelerator_Accelerator_Model_Observer {

	public function CmsSave(Varien_Event_Observer $observer) {
		/** @var Mage_Cms_Model_Page $cms */
		$cms = $observer->getData('page');
		$permalink = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).$cms->getIdentifier();
		/** @var EworldAccelerator_Accelerator_Helper_Data $helper */
		$helper = Mage::helper('accelerator');
		if ($helper->deleteURL($permalink)) {
			Mage::getSingleton('core/session')->addSuccess('Cache file has been deleted');
		}
	}

	public function ProductSave(Varien_Event_Observer $observer) {
		/** @var Mage_Catalog_Model_Product $product */
		$product = $observer->getData('product');
		$categoryIds = $product->getCategoryIds();
		/** @var EworldAccelerator_Accelerator_Helper_Data $helper */
		$helper = Mage::helper('accelerator');
		$permalink = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).$product->getUrlPath();
		if ($helper->deleteURL($permalink)) {
			Mage::getSingleton('core/session')->addSuccess('Cache file has been deleted for URL '.$permalink);
		}
		foreach ($categoryIds as $currentCategoryId) {
			$category = Mage::getModel('catalog/category')->load($currentCategoryId);
			// product
			$permalink = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).$product->getUrlPath($category);
			if ($helper->deleteURL($permalink)) {
				Mage::getSingleton('core/session')->addSuccess('Cache file has been deleted for URL '.$permalink);
			}
			//category
			$permalink = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).$category->getUrlPath();
			if ($helper->deleteURL($permalink)) {
				Mage::getSingleton('core/session')->addSuccess('Cache file has been deleted for URL '.$permalink);
			}
		}
	}

	public function CategorySave(Varien_Event_Observer $observer) {
		/** @var Mage_Catalog_Model_Category $category */
		$category = $observer->getData('category');
		$permalink = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).$category->getUrlPath();
		/** @var EworldAccelerator_Accelerator_Helper_Data $helper */
		$helper = Mage::helper('accelerator');
		if ($helper->deleteURL($permalink)) {
			Mage::getSingleton('core/session')->addSuccess('Cache file has been deleted');
			Mage::getSingleton('core/session')->addSuccess('If your modification modifies the entire website, we suggest you to delete all the cache in <i>Eworld Accelerator</i>&nbsp;&gt;&nbsp;<i>Manage Cache</i> in the admin menu');
		}
	}

}
