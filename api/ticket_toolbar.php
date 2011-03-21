<?php
if (class_exists('Extension_TicketToolbarItem',true)):
	class PiSlaToolbarSLA extends Extension_TicketToolbarItem {
		function render(Model_Ticket $ticket) {
			$properties = PiSlaUtils::getProperties();
			
			if (isset ($properties['show_sla_bar']) && $properties['show_sla_bar'] == 1) {
				$tpl = DevblocksPlatform::getTemplateService();
				
				$ticket_sla_info = PiSlaUtils::getTicketSLAInfo ($ticket->id);
		
				// add additional info
				if ($ticket_sla_info['sla_type'] == 'b') {
					$response_days = $ticket_sla_info['first_response_time'] != -1 ? PiSlaUtils::calculateWorkingDays ($ticket->created_date, $ticket_sla_info['first_response_time'], $properties) : -1;
					$response_days_ago = PiSlaUtils::calculateWorkingDays ($ticket_sla_info['last_response_time'], time());
				} else {
					$response_days = $ticket_sla_info['first_response_time'] != -1 ? PiSlaUtils::calculateDays ($ticket->created_date, $ticket_sla_info['first_response_time']) : -1;
					$response_days_ago = PiSlaUtils::calculateDays ($ticket_sla_info['last_response_time'], time());
				}
				
				$tpl->assign ('ticket_sla_info', $ticket_sla_info);
				$tpl->assign ('response_days', $response_days);
				$tpl->assign ('response_days_ago', $response_days_ago);
				
				$tpl->display('devblocks:net.pixelinstrument.sla::ticket_toolbar.tpl');
			}
		}
	};
endif;
