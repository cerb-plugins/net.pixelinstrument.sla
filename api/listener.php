<?php
class PiSlaEventListener extends DevblocksEventListenerExtension {
    /**
     * @param Model_DevblocksEvent $event
     */
    function handleEvent(Model_DevblocksEvent $event) {
        switch($event->id) {
            case 'dao.ticket.update':
            	@$objects = $event->params['objects'];
				
				list ($object_id, $object) = each($objects);
				
				foreach($objects as $object_id => $object) {
            		$model = $object['model'];
            		$changes = $object['changes'];
					
	            	if(!empty($changes) &&
						isset($changes[DAO_Ticket::LAST_ACTION_CODE]) &&
						isset($changes[DAO_Ticket::LAST_ACTION_CODE]['to']) &&
						$changes[DAO_Ticket::LAST_ACTION_CODE]['to'] == CerberusTicketActionCode::TICKET_OPENED)
					{
						// calculate sla date: this will create an audit log
						$ticket_sla_info = PiSlaUtils::getTicketSLAInfo($object_id);
					}
				}
           		break;
        }
    }
};
?>