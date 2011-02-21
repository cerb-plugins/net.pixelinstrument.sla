<?php
if (class_exists('Extension_TicketToolbarItem',true)):
	class PiSlaToolbarSLA extends Extension_TicketToolbarItem {
		function render(Model_Ticket $ticket) {
			$properties = PiSlaUtils::getProperties();
			
			if (isset ($properties['show_sla_bar']) && $properties['show_sla_bar'] == 1) {
				$ticket_sla_info = PiSlaUtils::getTicketSLAInfo ($ticket->id);
				
				$tpl = DevblocksPlatform::getTemplateService();
				
				$tpl->assign ('ticket_sla_info', $ticket_sla_info);				
				$tpl->display('devblocks:net.pixelinstrument.sla::ticket_toolbar.tpl');
			}
		}
	};
endif;
