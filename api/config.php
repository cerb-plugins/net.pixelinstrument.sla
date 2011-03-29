<?php
class PageMenuItem_SetupPluginsPiSla extends Extension_PageMenuItem {
	const POINT = 'net.pixelinstrument.sla.setup.plugins.menu';
	
	function render() {
		$tpl = DevblocksPlatform::getTemplateService();
		$tpl->assign('extension', $this);
		$tpl->display('devblocks:net.pixelinstrument.sla::menu_item.tpl');
	}
};

class PageSection_PiSla extends Extension_PageSection {
	const ID = 'net.pixelinstrument.sla.setup.section';
	
	function render() {
		$tpl = DevblocksPlatform::getTemplateService();
		$visit = CerberusApplication::getVisit();
		
		$visit->set(ChConfigurationPage::ID, 'pi_sla');

		// Get settings
        $properties = PiSlaUtils::getProperties();
        $tpl->assign('properties', $properties);
        
        $customer_fields = DAO_CustomField::getByContext(CerberusContexts::CONTEXT_ORG);
        $tpl->assign('customer_fields', $customer_fields);
        
        $ticket_fields = DAO_CustomField::getByContext(CerberusContexts::CONTEXT_TICKET);
		$tpl->assign('ticket_fields', $ticket_fields);
		
		$tpl->display('devblocks:net.pixelinstrument.sla::configure.tpl');
	}
	
	function saveAction() {
        @$show_sla_bar = DevblocksPlatform::importGPC($_REQUEST['show_sla_bar'], 'string', 0);
        @$working_days = DevblocksPlatform::importGPC($_REQUEST['working_days'],'array', array(1,2,3,4,5));
        @$holiday_name = DevblocksPlatform::importGPC($_REQUEST['holiday_name'],'array', array());
        @$holiday_date = DevblocksPlatform::importGPC($_REQUEST['holiday_date'],'array', array());
        @$customer_type_field_id = DevblocksPlatform::importGPC($_REQUEST['customer_type_field_id'],'integer', 0);
        
        @$sla_opt = DevblocksPlatform::importGPC($_REQUEST['sla_opt'],'array', array());
        @$sla = DevblocksPlatform::importGPC($_REQUEST['sla'],'array', array());
		@$sla_type = DevblocksPlatform::importGPC($_REQUEST['sla_type'],'array', array());
        
        // write all dates as YYYY-MM-DD
        $holidays = array();
        foreach ($holiday_date as $key => $date) {
            $time = strtotime($date);
            $name = $holiday_name[$key];
            
            if (strlen($name) && $time > 0) {
                $holidays[date ("Y-m-d", $time)] = $name;
            }
        }
        
        $properties = array();
		$properties['show_sla_bar'] = $show_sla_bar;
        $properties['working_days'] = $working_days;
        $properties['holidays'] = $holidays;
        $properties['customer_type_field_id'] = $customer_type_field_id;
        $properties['sla'] = (sizeof ($sla_opt) && sizeof ($sla)) ? array_combine ($sla_opt, $sla) : array();
		$properties['sla_type'] = (sizeof ($sla_opt) && sizeof ($sla_type)) ? array_combine ($sla_opt, $sla_type) : array();
        
		DevblocksPlatform::setPluginSetting('net.pixelinstrument.sla', 'properties', json_encode($properties));
        
		DevblocksPlatform::redirect(new DevblocksHttpResponse(array('config','pi_sla')));
	}
};
