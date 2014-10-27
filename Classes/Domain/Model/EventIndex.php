<?php
namespace Tx\CzSimpleCal\Domain\Model;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Tx\CzSimpleCal\Utility\Inflector;
use Tx\CzSimpleCal\Utility\DateTime as CzSimpleCalDateTime;
use Tx\CzSimpleCal\Domain\Model\Enumeration\EventStatus;

/**
 * Event index entry.
 */
class EventIndex extends Base {

	/**
	 * the timestamp from the beginning of that event
	 *
	 * @ugly integer is used as we'd like an instance of the Utility_DayTime, but extbase would only return a DateTime Object in the extbase version shipped with TYPO3 4.4
	 * @var integer
	 */
	protected $start;

	/**
	 * the timestamp from the end of that event
	 *
	 * @ugly integer is used as we'd like an instance of the Utility_DayTime, but extbase would only return a DateTime Object in the extbase version shipped with TYPO3 4.4
	 * @var integer
	 */
	protected $end;

	/**
	 * the start date as DateTime object
	 *
	 * @var \Tx\CzSimpleCal\Utility\DateTime
	 */
	protected $dateTimeObjectStart = NULL;

	/**
	 * the end date as DateTime object
	 *
	 * @var \Tx\CzSimpleCal\Utility\DateTime
	 */
	protected $dateTimeObjectEnd = NULL;

	/**
	 * @inject
	 * @transient
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * the pid of the record
	 *
	 * @var integer
	 */
	protected $pid;

	/**
	 * @var \Tx\CzSimpleCal\Domain\Model\Event
	 */
	protected $event;

	/**
	 * @var \Tx\CzSimpleCal\Domain\Model\Enumeration\EventStatus
	 */
	protected $status;

	/**
	 * @var string
	 */
	protected $teaser;

	/**
	 * set the timestamp from the beginning of that event
	 *
	 * @param integer $start
	 * @return null
	 */
	public function setStart($start) {
		$this->start = $start;
		$this->dateTimeObjectStart = NULL;
	}

	/**
	 * get the timestamp from the beginning of that event
	 *
	 * @return integer
	 */
	public function getStart() {
		return $this->start;
	}

	/**
	 * get the start of this event as a dateTimeObject
	 *
	 * @return CzSimpleCalDateTime
	 */
	public function getDateTimeObjectStart() {
		if (is_null($this->dateTimeObjectStart)) {
			$this->dateTimeObjectStart = new CzSimpleCalDateTime(
				'@' . $this->start
			);
			$this->dateTimeObjectStart->setTimezone(new \DateTimeZone(date_default_timezone_get()));
		}
		return $this->dateTimeObjectStart;
	}

	/**
	 * set the timestamp from the end of that event
	 *
	 * @param integer $end
	 * @return null
	 */
	public function setEnd($end) {
		$this->end = $end;
		$this->dateTimeObjectEnd = NULL;
	}

	/**
	 * get the timestamp from the end of that event
	 *
	 * @return integer
	 */
	public function getEnd() {
		return $this->end;
	}

	/**
	 * get the end of this event as a dateTimeObject
	 *
	 * @return CzSimpleCalDateTime
	 */
	public function getDateTimeObjectEnd() {
		if (is_null($this->dateTimeObjectEnd)) {
			$this->dateTimeObjectEnd = new CzSimpleCalDateTime(
				'@' . $this->end
			);
			$this->dateTimeObjectEnd->setTimezone(new \DateTimeZone(date_default_timezone_get()));
		}
		return $this->dateTimeObjectEnd;
	}

	/**
	 * Sets the event and the sys_language.
	 *
	 * @param Event $event
	 */
	public function setEvent($event) {
		$this->event = $event;
		$this->_languageUid = $event->getSysLanguageUid();
	}

	/**
	 * Returns the related event object.
	 *
	 * @return Event
	 */
	public function getEvent() {
		return $this->event;
	}

	/**
	 * If a status was set in this event index entry, return this status,
	 * otherwise return the status of the event.
	 *
	 * @return string
	 */
	public function getStatus() {

		if (!isset($this->status)) {
			$this->status = $this->objectManager->get('Tx\\CzSimpleCal\\Domain\\Model\\Enumeration\\EventStatus', EventStatus::UNDEFINED);
		}

		if ($this->status->equals(EventStatus::UNDEFINED)) {
			$status = $this->event->getStatus();
		} else {
			$status = (string)$this->status;
		}

		return $status;
	}

	/**
	 * Setter for the status
	 *
	 * @param string $status
	 */
	public function setStatus($status) {
		$this->status = $this->objectManager->get('Tx\\CzSimpleCal\\Domain\\Model\\Enumeration\\EventStatus', $status);
	}


	/**
	 * create a new instance with data from a given array
	 *
	 * @param EventIndex $obj
	 * @param $data
	 * @throws \InvalidArgumentException
	 * @return EventIndex
	 */
	public static function fromArray($obj, $data) {

		foreach ($data as $name => $value) {
			$methodName = 'set' . GeneralUtility::underscoredToUpperCamelCase($name);

			// check if there is a setter defined (use of is_callable to check if the scope is public)
			if (!is_callable(array($obj, $methodName))) {
				throw new \InvalidArgumentException(sprintf('Could not find the %s method to set %s in %s.', $methodName, $name, get_class($obj)));
			}

			call_user_func(array($obj, $methodName), $value);
		}

		return $obj;
	}

	/**
	 * get a hash for this recurrance of the event
	 *
	 * @return string
	 */
	public function getHash() {
		return md5(
			'eventindex-' .
			$this->getEvent()->getHash() . '-' .
			$this->getStart() . '-' .
			$GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']
		);
	}

	/**
	 * tunnel all methods that were not found to the Event
	 *
	 * @param $method
	 * @param $args
	 * @return mixed
	 * @throws \BadMethodCallException
	 */
	public function __call($method, $args) {
		if (!$this->event) {
			throw new \BadMethodCallException(sprintf('The method %s was not found in %s.', $method, get_class($this)));
		}
		$callback = array($this->event, $method);
		if (!is_callable($callback)) {
			throw new \BadMethodCallException(sprintf('The method %s was neither found in %s nor in %s.', $method, get_class($this), get_class($this->event)));
		}

		return call_user_func_array($callback, $args);
	}

	/**
	 * the property slug
	 *
	 * @var string slug
	 */
	protected $slug;

	/**
	 * getter for slug
	 *
	 * @return string
	 */
	public function getSlug() {
		return $this->slug;
	}

	/**
	 * setter for slug
	 *
	 * @param string $slug
	 * @return EventIndex
	 * @throws \InvalidArgumentException
	 */
	public function setSlug($slug) {
		if (preg_match('/^[a-z0-9\-]*$/i', $slug) === FALSE) {
			throw new \InvalidArgumentException(sprintf('"%s" is no valid slug. Only ASCII-letters, numbers and the hyphen are allowed.'));
		}
		$this->slug = $slug;
		return $this;
	}

	/**
	 * generate a slug for this record
	 *
	 * @return string
	 */
	public function generateSlug() {
		$value = $this->generateRawSlug();
		$value = Inflector::urlize($value);

		/** @var \Tx\CzSimpleCal\Domain\Repository\EventIndexRepository $eventIndexRepository */
		$eventIndexRepository = $this->objectManager->get('Tx\\CzSimpleCal\\Domain\\Repository\\EventIndexRepository');

		$slug = $eventIndexRepository->makeSlugUnique($value, $this->uid);
		$this->setSlug($slug);
	}

	/**
	 * generate a raw slug that might have invalid characters
	 *
	 * you could overwrite this if you want a different slug
	 *
	 * @return string
	 */
	protected function generateRawSlug() {
		$value = $this->getEvent()->getSlug();
		if ($this->getEvent()->isRecurrant()) {
			$value .= ' ' . $this->getDateTimeObjectStart()->format('Y-m-d');
		}
		return $value;
	}

	/**
	 * @return string
	 */
	public function getTeaser() {
		if (isset($this->teaser)) {
			return $this->teaser;
		}
		return $this->event->getTeaser();
	}

	/**
	 * will be called before instance is added to the repository
	 *
	 * @return null
	 */
	public function preCreate() {
		$this->generateSlug();
	}

	/**
	 * @param string $teaser
	 */
	public function setTeaser($teaser) {
		$this->teaser = $teaser;
	}
}