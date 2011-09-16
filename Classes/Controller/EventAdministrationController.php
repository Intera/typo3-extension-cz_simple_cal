<?php

/***************************************************************
*  Copyright notice
*
*  (c) 2010 Christian Zenker <christian.zenker@599media.de>, 599media GmbH
*  			
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
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Controller for the Event object with editing capabilities for frontend-users
 * 
 * (We use a seperate controller for this to avoid side effects with the
 *  BaseExtendableController)
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */

class Tx_CzSimpleCal_Controller_EventAdministrationController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * @var Tx_CzSimpleCal_Domain_Repository_EventRepository
	 */
	protected $eventRepository;

	/**
	 * inject an eventRepository
	 * 
	 * @param Tx_CzSimpleCal_Domain_Repository_EventRepository $eventRepository
	 */
	public function injectEventRepository(Tx_CzSimpleCal_Domain_Repository_EventRepository $eventRepository) {
		$this->eventRepository = $eventRepository;
	}

	
	/** 
	 * list all events by the logged in user
	 */
	public function listAction() {
		//TODO: user filtering
		$this->view->assign('events', $this->eventRepository->findAllByUserId($this->getFrontendUserId()));
	}
	
	
    /**
     * Displays a form for creating a new event
     *
     * @param Tx_CzSimpleCal_Domain_Model_Event $newEvent 
     * @return void
     * @dontvalidate $newEvent
     */
    public function newAction(Tx_CzSimpleCal_Domain_Model_Event $newEvent = NULL) {
    	if($newEvent) {
    		$this->setDefaults($newEvent);
    		$newEvent->setCruserFe($this->getFrontendUserId());
    	}
        $this->view->assign('event', $newEvent);
    }

    /**
     * Creates a new event
     *
     * @param Tx_CzSimpleCal_Domain_Model_Event $newEvent
     * @return void
     * @dontvalidate $newEvent
     */
    public function createAction(Tx_CzSimpleCal_Domain_Model_Event $newEvent) {
    	$this->setDefaults($newEvent);
    	$newEvent->setCruserFe($this->getFrontendUserId());
    	$this->view->assign('newEvent', $newEvent);
    	
    	$this->eventRepository->add($newEvent);
    	
		$this->redirect('list');
    }

    /**
     * Displays a form for editing an existing event
     *
     * @param Tx_CzSimpleCal_Domain_Model_Event $event
     * @return void
     * @dontValidate $event
     */
    public function editAction(Tx_CzSimpleCal_Domain_Model_Event $event) {
        $this->view->assign('event', $event);
    }

    /**
     * Updates an existing event
     *
     * @param Tx_CzSimpleCal_Domain_Model_Event $event
     * @return void
     */
    public function updateAction(Tx_CzSimpleCal_Domain_Model_Event $event) {
        // TODO access protection
//        $this->blogRepository->update($blog);
//        $this->addFlashMessage('updated');
//        $this->redirect('index');
    }

    /**
     * Deletes an existing event
     *
     * @param Tx_CzSimpleCal_Domain_Model_Event $event The event to delete
     * @return void
     * @dontvalidate $newEvent
     */
    public function deleteAction(Tx_CzSimpleCal_Domain_Model_Event $event) {
        // TODO access protection
//        $this->blogRepository->remove($blog);
//        $this->addFlashMessage('deleted', t3lib_FlashMessage::INFO);
//        $this->redirect('index');
    }
    
    /**
     * set defaults on an object
     * 
     * @param Tx_CzSimpleCal_Domain_Model_Event $event
     */
    public function setDefaults($event) {
    	//TODO	
    }
    
    /**
     * get the frontend User id
     */
    public function getFrontendUserId() {
	    $fe_user = $GLOBALS['TSFE']->fe_user->user['uid'];
	    return $fe_user ? $fe_user : false;
    }
    
    
    /** 
     * validate the event
     * 
     * Considerations
     * ===============
     * Extabse Validation for models and properties is not suitable for most of the validations needed
     * as the validation would *always* be checked - even if just displaying.
     * So if we don't want a frontend user to enter an event in the past and did it
     * using extbase's built-in validation, we would not be able to show *any* event 
     * in the past.
     * 
     * @param Tx_CzSimpleCal_Domain_Model_Event $event
     */
    protected function validateEvent($event) {
    	
    	// check: event is not in the past
    	if($event->getDateTimeObjectStart()->format('U') + 24 * 60 * 60 < time()) {
    		throw new InvalidArgumentException('start date must not be in the past');
    	}  
    	
    	// check: end date is after start date
    	if($event->getDateTimeObjectStart()->format('U') > $event->getDateTimeObjectEnd()->format('U')) {
    		throw new InvalidArgumentException('end date has to be after start date');
    	}
    	
    	if($event->getDescription()) {
    		if(strlen($event->getDescription()) > strlen(strip_tags($event->getDescription()))) {
    			throw new InvalidArgumentException('we don\'t like markup here');
    		}
    	}
    }

}
?>