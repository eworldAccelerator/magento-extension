<?php

class EworldAccelerator_Accelerator_Block_Adminhtml_Acceleratorbackend extends Mage_Adminhtml_Block_Template {
	/** @var EworldAccelerator_Accelerator_Helper_Data $helper */
	private $helper;
	/** @var string $eaccDirectory */
	protected $eaccDirectory;
	/** @var string $eaccVersion */
	protected $eaccVersion;
	/** @var bool $eaccIsCdnActive */
	protected $eaccIsCdnActive;
	/** @var bool $eaccIsThereUpdate */
	protected $eaccIsThereUpdate;
	/** @var string $dashboardURL */
	protected $dashboardURL;
	/** @var string $configurationURL */
	protected $configurationURL;
	/** @var string $updateURL */
	protected $updateURL;

	public function __construct() {
		$this->helper = Mage::helper('accelerator');

		if (isset($_POST['submitDeleteAll']) && $_POST['submitDeleteAll'] == 1) {
			if ($this->helper->deleteAllCache()) {
				Mage::getSingleton('core/session')->addSuccess('All cache files have been deleted');
			}
			else {
				Mage::getSingleton('core/session')->addError('An error occurs during all cache files deletion');
			}
			$this->redirectAfterPost();
		}
		if (isset($_POST['submitGC']) && $_POST['submitGC'] == 1) {
			if ($this->helper->garbageCollector()) {
				Mage::getSingleton('core/session')->addSuccess('All expired cache files have been deleted');
			}
			else {
				Mage::getSingleton('core/session')->addError('An error occurs during all expired cache files deletion');
			}
			$this->redirectAfterPost();
		}
		if (isset($_POST['submitPurgeCDN']) && $_POST['submitPurgeCDN'] == 1) {
			if ($this->helper->isCdnActive()) {
				if ($this->helper->purgeCDN()) {
					Mage::getSingleton('core/session')->addSuccess('Purge task has been sent to all CDN servers');
				}
				else {
					Mage::getSingleton('core/session')->addError('An error occurs during CDN purge');
				}
			}
			else {
				Mage::getSingleton('core/session')->addError('CDN is not active for this domain');
			}
			$this->redirectAfterPost();
		}
		// Assign variables to phtml
		$this->eaccDirectory = Mage::getStoreConfig('admin/eacc_group/eacc_dir');
		$this->eaccVersion = $this->helper->getVersion();
		$this->eaccIsCdnActive = $this->helper->isCdnActive();
		$this->eaccIsThereUpdate = $this->helper->isThereUpdate();
		$this->dashboardURL = $this->helper->getDashboardURL();
		$this->configurationURL = $this->helper->getConfigurationURL();
		$this->updateURL = $this->helper->getSystemUpdateLink();

		parent::__construct();
	}

	private function redirectAfterPost() {
		$currentURL = $this->helper('core/url')->getCurrentUrl();
		Mage::app()->getResponse()->setRedirect($currentURL);
	}
}