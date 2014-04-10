<?php
namespace Tx\CzSimpleCal\Indexer;

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

use \Tx\CzSimpleCal\Domain\Model\EventIndex;

/**
 * a class that handles indexing of events
 */
class Event {

	/**
	 * @var \Tx\CzSimpleCal\Domain\Repository\EventRepository
	 * @inject
	 */
	protected $eventRepository = null;

	/**
	 * @var \Tx\CzSimpleCal\Domain\Repository\EventIndexRepository
	 * @inject
	 */
	protected $eventIndexRepository = null;

	/**
	 * create an eventIndex
	 *
	 * @param integer|\Tx\CzSimpleCal\Domain\Model\Event $event
	 */
	public function create($event) {
		if(is_integer($event)) {
			$event = $this->fetchEventObject($event);
		}

		$this->doCreate($event);
	}

	/**
	 * update an eventIndex
	 *
	 * @param integer|\Tx\CzSimpleCal\Domain\Model\Event $event
	 */
	public function update($event) {
		if(is_integer($event)) {
			$event = $this->fetchEventObject($event);
		}

		$this->doDelete($event);
		$this->doCreate($event);

	}

	/**
	 * delete the eventIndex
	 *
	 * @param integer|\Tx\CzSimpleCal\Domain\Model\Event $event
	 */
	public function delete($event) {
		if(is_integer($event)) {
			$event = $this->fetchEventObject($event);
		}

		$this->doDelete($event);
	}

	/**
	 * delete an event
	 *
	 * @param \Tx\CzSimpleCal\Domain\Model\Event $event
	 */
	protected function doDelete($event) {
		$eventIndexEntries = $this->eventIndexRepository->findAllByEventEverywhere($event);
		foreach ($eventIndexEntries as $eventIndexEntry) {
			$this->eventIndexRepository->remove($eventIndexEntry);
		}
	}

	/**
	 * create the indexes
	 *
	 * @param \Tx\CzSimpleCal\Domain\Model\Event $event
	 */
	protected function doCreate($event) {
		$event->setLastIndexed(new \DateTime());
		$this->eventRepository->update($event);

		if(!$event->isEnabled()) {
			return;
		}
		// get all recurrances...
		foreach($event->getRecurrances() as $recurrance) {
			// ...and store them to the repository
			$instance = EventIndex::fromArray(
				$recurrance
			);

			$this->eventIndexRepository->add(
				$instance
			);
		}
	}

	/**
	 * get an event object by its uid
	 *
	 * @param integer $id
	 * @return \Tx\CzSimpleCal\Domain\Model\Event
	 * @throws \InvalidArgumentException
	 */
	protected function fetchEventObject($id) {
		$event = $this->eventRepository->findOneByUidEverywhere($id);
		if(empty($event)) {
			throw new \InvalidArgumentException(sprintf('An event with uid %d could not be found.', $id));
		}
		return $event;
	}
}