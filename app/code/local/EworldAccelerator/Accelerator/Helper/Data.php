<?php

class EworldAccelerator_Accelerator_Helper_Data extends Mage_Core_Helper_Abstract {
	/** @var string $directory */
	private $directory;
	/** @var EworldAcceleratorAPI $api */
	private $api;

	function __construct() {
		$this->directory = Mage::getStoreConfig('admin/eacc_group/eacc_dir');
	}

	/**
	 * @return bool
	 */
	private function getAPI() {
		if (is_object($this->api)) {
			return true;
		}
		else {
			if (file_exists($this->directory) && file_exists($this->directory . 'run_cache.php')) {
				if (defined('EWORLD_ACCELERATOR_DIR') && isset($GLOBALS['eworldAcceleratorCache'])) {
					$this->api = new EworldAcceleratorAPI(EWORLD_ACCELERATOR_DIR, $GLOBALS['eworldAcceleratorCache']);
				}
				else {
					require_once $this->directory . 'inc/EworldAcceleratorAPI.php';

					$this->api = new EworldAcceleratorAPI($this->directory);
				}

				return true;
			}
		}
		return false;
	}

	/**
	 * @param string $permalink
	 * @return bool
	 */
	public function deleteURL($permalink) {
		if ($this->getAPI()) {
			$this->api->deleteURL($permalink);
			return true;
		}
		return false;
	}

	/**
	 * @return bool
	 */
	public function deleteAllCache() {
		if ($this->getAPI()) {
			$this->api->deleteAllCachedFiles();
			return true;
		}
		return false;
	}

	/**
	 * @return bool
	 */
	public function garbageCollector() {
		if ($this->getAPI()) {
			$this->api->gc();
			return true;
		}
		return false;
	}

	/**
	 * @return bool
	 */
	public function purgeCDN() {
		if ($this->getAPI()) {
			$this->api->cdnPurge();
			return true;
		}
		return false;
	}

	/**
	 * @return bool
	 */
	public function isCdnActive() {
		if ($this->getAPI()) {
			return $this->api->isCdnActive();
		}
		return false;
	}

	/**
	 * @return bool
	 */
	public function getVersion() {
		if ($this->getAPI()) {
			return $this->api->getVersion();
		}
		return false;
	}

	/**
	 * @return bool
	 */
	public function isThereUpdate() {
		if ($this->getAPI()) {
			return $this->api->isThereSystemUpdate();
		}
		return false;
	}

	/**
	 * @return string
	 */
	public function getDashboardURL() {
		if ($this->getAPI()) {
			return $this->api->getDashboardURL();
		}
		return '';
	}

	/**
	 * @return string
	 */
	public function getConfigurationURL() {
		if ($this->getAPI()) {
			return $this->api->getConfigurationURL();
		}
		return '';
	}

	/**
	 * @return string
	 */
	public function getSystemUpdateLink() {
		if ($this->getAPI()) {
			return $this->api->getSystemUpdateLink();
		}
		return '';
	}
}