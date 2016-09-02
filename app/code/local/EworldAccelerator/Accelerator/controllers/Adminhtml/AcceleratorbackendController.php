<?php

class EworldAccelerator_Accelerator_Adminhtml_AcceleratorbackendController extends Mage_Adminhtml_Controller_Action {
	public function indexAction() {
		$this->loadLayout();
		$this->renderLayout();
	}
}