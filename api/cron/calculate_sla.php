<?php
// Need to load the audit_log plugin
DevblocksPlatform::registerClasses(APP_PATH . '/features/cerberusweb.auditlog/api/App.php', array(
        'DAO_TicketAuditLog',
        'SearchFields_TicketAuditLog'
));

/*
 * class PiSlaCalculate
 * Calculate the SLA for tickets that don't have one yet
 */
class PiSlaCalculate extends CerberusCronPageExtension {
	function run() {
		// Initialize the logger
		$logger = DevblocksPlatform::getConsoleLog();
		
		// initialize the translation service
		$translate = DevblocksPlatform::getTranslationService();
		
		$logger->info($translate->_('net.pixelinstrument.sla.begin'));
		
		// select all tickets without a SLA
		list($sla_logs) = DAO_TicketAuditLog::search(
			array(
				new DevblocksSearchCriteria(SearchFields_TicketAuditLog::CHANGE_FIELD, DevblocksSearchCriteria::OPER_EQ, 'sla_date')
			),
			-1);
		
		$tickets_ok_ids = array();
		foreach ($sla_logs as $log) {
			array_push($tickets_ok_ids, $log['l_ticket_id']);
		}
		
		list($bad_tickets) = DAO_Ticket::search(
			array(),
			array(
				new DevblocksSearchCriteria(SearchFields_Ticket::TICKET_ID, DevblocksSearchCriteria::OPER_NIN,$tickets_ok_ids)
			),
			100, // update up to 100 tickets on each run
			0,
			SearchFields_Ticket::TICKET_UPDATED_DATE,
			false,
			false
		);
		
		foreach ($bad_tickets as $ticket_id => $ticket) {
			$logger->info(vsprintf($translate->_('net.pixelinstrument.sla.updating_ticket'), $ticket_id));
			PiSlaUtils::getTicketSLAInfo($ticket_id);
		}
		
		$logger->info($translate->_('net.pixelinstrument.sla.end'));
	}
};
?>
