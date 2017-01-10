<?php

class EworldAccelerator_Accelerator_Model_Observer {

	/** @var EworldAccelerator_Accelerator_Model_Observer */
	private static $_instance;

	public static function getInstance() {
		if (is_null(self::$_instance)) {
			self::$_instance = new EworldAccelerator_Accelerator_Model_Observer();
		}
		return self::$_instance;
	}

	public static function cmsSave(Varien_Event_Observer $observer) {
		/** @var Mage_Cms_Model_Page $cms */
		$cms = $observer->getData('page');
		$permalink = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).$cms->getIdentifier();
		/** @var EworldAccelerator_Accelerator_Helper_Data $helper */
		$helper = Mage::helper('accelerator');
		if ($helper->deleteURL($permalink)) {
			Mage::getSingleton('core/session')->addSuccess('Cache file has been deleted');
		}
	}

	public static function productSave(Varien_Event_Observer $observer) {
		/** @var Mage_Catalog_Model_Product $product */
		$product = $observer->getData('product');

		self::deleteCacheForProduct($product);
	}

	public static function categorySave(Varien_Event_Observer $observer) {
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

	public static function orderPaid(Varien_Event_Observer $observer) {
		/** @var Mage_Sales_Model_Order $order */
		$order = $observer->getEvent()->getInvoice()->getOrder();
		$items = $order->getAllItems();
		foreach ($items as $currentItem) {
			/** @var Mage_Sales_Model_Order_Item $currentItem */
			$stocklevel = self::getProductStock($currentItem->getProduct());
			Mage::log('qty ordered='.$currentItem->getQtyInvoiced());
			Mage::log('stock='.$stocklevel);
			if ($currentItem->getQtyInvoiced() >= $stocklevel || $stocklevel == 0) {
				self::deleteCacheForProduct($currentItem->getProduct());
				Mage::log('Trigger orderPaid => cache deleted for product '.$currentItem->getProduct()->getSku());
			}
		}
	}

	public static function orderCanceled(Varien_Event_Observer $observer) {
		/** @var Mage_Sales_Model_Order_Item $item */
		$item = $observer->getEvent()->getItem();

		$stocklevel = self::getProductStock($item->getProduct());
		Mage::log('qty canceled='.$item->getQtyCanceled());
		Mage::log('stock='.$stocklevel);

		if ($stocklevel <= 1 || $item->getQtyCanceled() >= $stocklevel) {
			self::deleteCacheForProduct($item->getProduct());
			Mage::log('Trigger orderCanceled => cache deleted for product '.$item->getProduct()->getSku());
		}
	}

	private static function getProductStock(Mage_Catalog_Model_Product $product) {
		$stocklevel = (int) Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();
		return $stocklevel;
	}

	private static function deleteCacheForProduct(Mage_Catalog_Model_Product $product) {
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

}
