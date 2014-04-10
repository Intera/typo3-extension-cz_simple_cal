<?php
namespace Tx\CzSimpleCal\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Christian Zenker <christian.zenker@599media.de>, 599media GmbH
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ArrayUtility;

/**
 * Base controller for all cz_simple_cal controllers. Initializes
 * the settings array and the submitted GET / POST parameters.
 */
abstract class BaseExtendableController extends ActionController {

	/**
	 * an array of settings of the specific action used
	 *
	 * @var array
	 */
	protected $actionSettings = null;

	/**
	 * set up all settings correctly allowing overrides from flexforms
	 *
	 * @return void
	 */
	protected function initializeActionSettings() {
		$this->actionSettings = &$this->settings[$this->request->getControllerName()]['actions'][$this->request->getControllerActionName()];

		// Since an action does not need any settings any more we need
		// to make sure that the actionSettings array is initalized.
		if (!is_array($this->actionSettings)) {
			$this->actionSettings = array();
		}

		// merge the settings from the flexform
		if(isset($this->settings['override']['action'])) {
			// this will override values if they are not empty
			ArrayUtility::mergeRecursiveWithOverrule($this->actionSettings, $this->settings['override']['action'], false, false);
		}

		// merge settings from getPost-values
		if(isset($this->actionSettings['getPostAllowed'])) {
			$allowed = GeneralUtility::trimExplode(',', $this->actionSettings['getPostAllowed'], true);

			$this->actionSettings = array_merge(
				$this->actionSettings,
				array_intersect_key(
					$this->request->getArguments(),
					array_flip($allowed)
				)
			);
		}
	}

	/**
	 * Merges the override settings defined in the FlexForm to the current
	 * settings.
	 *
	 * @return void
	 */
	protected function initializeSettings() {
		if(isset($this->settings['override'])) {
			// this will override values if they are not empty and they already exist (so no adding of keys)
			ArrayUtility::mergeRecursiveWithOverrule($this->settings, $this->settings['override'], true, false);
		}
	}

	/**
	 * Initializes the current action
	 *
	 * @return void
	 */
	protected function initializeAction() {
		$this->initializeSettings();
		$this->initializeActionSettings();
	}

	/**
	 * Generates the data needed for rendering description, images and files.
	 *
	 * @param \Tx\CzSimpleCal\Domain\Model\Event $event
	 * @return array
	 */
	protected function generateRenderData($event) {

		$renderData = array();
		$contentObject = $this->configurationManager->getContentObject();
		$contentObjectData = $contentObject->data;

		foreach (array('descriptionAndImages', 'files') as $variableName) {

			$dataSettings = $this->settings['rendering'][$variableName];
			$data = $contentObjectData;

			if (is_array($dataSettings['mapEventProperties'])) {
				foreach ($dataSettings['mapEventProperties'] as $dataProperty => $eventProperty) {
					$propertyGetter = 'get' . ucfirst($eventProperty);
					$data[$dataProperty] = $event->$propertyGetter();
				}
			}

			if (is_array($dataSettings['overrideData'])) {
				$data = array_merge($data, $dataSettings['overrideData']);
			}

			$renderData[$variableName] = $data;
		}

		return $renderData;
	}
}