<?php
class ChRest_Sla extends Extension_RestController implements IExtensionRestController {
	function __construct($manifest) {
		parent::__construct($manifest);
	}
	
	function getAction($stack) {
		@$action = array_shift($stack);
		
		// Looking up a single ID?
		if(is_numeric($action)) {
			$this->success(PiSlaUtils::getTicketSLAInfo(intval($action)));
			
		} else { // actions
			switch($action) {
				default:
					$this->error(self::ERRNO_NOT_IMPLEMENTED);
					break;
			}
		}
	}
	
	function putAction($stack) {
		$this->error(self::ERRNO_NOT_IMPLEMENTED);
	}
	
	function postAction($stack) {
		$this->error(self::ERRNO_NOT_IMPLEMENTED);
	}
	
	function deleteAction($stack) {
		$this->error(self::ERRNO_NOT_IMPLEMENTED);
	}
	
	function translateToken($token, $type='dao') {
		return null;
	}
	
	function getContext($id) {
		return null;
	}
	
	function search($filters=array(), $sortToken='email_id', $sortAsc=1, $page=1, $limit=10) {
		return null;
	}
};