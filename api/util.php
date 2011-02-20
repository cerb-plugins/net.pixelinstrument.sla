<?php
class PiSlaUtils {
	const ID = 'net.pixelinstrument.sla.utils';
	
	function getTicketSLAInfo($ticket_id, $customer_id) {
		$ticket_sla_info = array(
			'first_response_time' => -1,
			'last_response_time' => -1,
			'response_days' => -1,
			'response_business_days' => -1,
			'customer_type' => '',
			'sla_days' => -1,
			'sla_status' => '',
			'sla_end_date' => -1
		);
		
		$ticket = DAO_Ticket::get ($ticket_id);
		if (!$ticket || $ticket->id != $ticket_id)
			return $ticket_sla_info;
		
		$properties = self::getProperties();
		
		
		// Get a list of responses
		list($tickets_messages) = DAO_Message::search(
			array(
				new DevblocksSearchCriteria(SearchFields_Message::TICKET_ID, DevblocksSearchCriteria::OPER_EQ, $ticket_id),
				new DevblocksSearchCriteria(SearchFields_Message::IS_OUTGOING, '=', 1),
			),
			-1,
			0,
			SearchFields_Message::CREATED_DATE,
			true,
			false
		);
		
			
		// Calculate first and last response times
		foreach ($tickets_messages as $message) {
			if ($ticket_sla_info['first_response_time'] == -1 || $ticket_sla_info['first_response_time'] > $message[SearchFields_Message::CREATED_DATE]) {
				$ticket_sla_info['first_response_time'] = $message[SearchFields_Message::CREATED_DATE];
			}
			
			if ($ticket_sla_info['last_response_time'] == -1 || $ticket_sla_info['last_response_time'] < $message[SearchFields_Message::CREATED_DATE]) {
				$ticket_sla_info['last_response_time'] = $message[SearchFields_Message::CREATED_DATE];
			}
		}
		
		
		// now transform the dates in number of days and business days
		
		$response_date = $ticket_sla_info['first_response_time'] != -1 ? $ticket_sla_info['first_response_time'] : time();
		
		$ticket_sla_info['response_days'] = self::calculateDays ($ticket->created_date, $ticket_sla_info['first_response_time']);
			
		$ticket_sla_info['response_business_days'] = self::calculateWorkingDays ($ticket->created_date, $ticket_sla_info['first_response_time'], $properties);
		
		
		// find out if we missed the SLA
		$customer_type_field_id = $properties['customer_type_field_id'];
		
		// get all the customers custom values
        
        list($customers) = DAO_ContactOrg::search (
            array(),
            array(),
            -1,
            0,
            null,
            null,
            false
        );

        $customers_custom_values = DAO_CustomFieldValue::getValuesByContextIds(CerberusContexts::CONTEXT_ORG, array_keys ($customers));
		
		$customer_type = "";
		if ($customer_id &&
			$customer_type_field_id &&
			isset ($customers_custom_values[$customer_id]) &&
			isset ($customers_custom_values[$customer_id][$customer_type_field_id])) {
			
			$customer_type = $customers_custom_values[$customer_id][$customer_type_field_id];

			$sla = isset ($properties['sla'][$customer_type]) ? ($properties['sla'][$customer_type]) : 0;
			$sla_type = isset ($properties['sla_type'][$customer_type]) ? ($properties['sla_type'][$customer_type]) : "b";
			
			$ticket_sla_info['sla_days'] = $sla;
			$ticket_sla_info['sla_type'] = $sla_type;
			$ticket_sla_info['customer_type'] = $customer_type;
		
		
			// calculate SLA end date
			switch ($ticket_sla_info['sla_type']) {
				case 's':
					$first_response_time_to_use = $ticket_sla_info['response_business_days'];
					$ticket_sla_info['sla_end_date'] = $ticket->created_date + ($sla * 24 * 60 * 60);
					
					break;
			
				case 'b':
				default:
					$ticket_sla_info['sla_end_date'] = self::getEndBusinessDate ($ticket->created_date, $sla);
					$first_response_time_to_use = $ticket_sla_info['response_days'];
					
					break;
			}
		
			// check if we missed SLA
			if ($ticket_sla_info['sla_days'] == 0) {
				$ticket_sla_info['sla_status'] = "green"; // everything is ok, no SLA here
			} else if ($first_response_time_to_use > $ticket_sla_info['sla_days']) {
				$ticket_sla_info['sla_status'] = "red"; // oops, we missed it
			} else if ($ticket_sla_info['first_response_time'] == -1) {
				$ticket_sla_info['sla_status'] = "yellow"; // we're still in time...
			} else {
				$ticket_sla_info['sla_status'] = "green"; // great job!
			}
		}
		
		return $ticket_sla_info;
	}
	
	
	static function getEndBusinessDate ($start, $business_days, $properties =  null) {
		if ($business_days <= 0)
			return $start;
			
		if (!$properties)
			$properties = self::getProperties();
			
		$holidays = array_keys ($properties['holidays']);
        $working_days = $properties['working_days'];
			
		$cur_date = date ("Y-m-d", $start);
		while ($business_days) {
			if (in_array (date("w", strtotime ($cur_date)), $working_days)) {
				if (!in_array ($cur_date, $holidays)) {
					$business_days--;
				}
			}
				
			$cur_date = date ("Y-m-d", strtotime ("+1 days", strtotime($cur_date)));
		}
		
		return strtotime($cur_date);
	}
	
	
	/***
	* calculateWorkingDays
	* Calculate the number of working days between two dates.
	* The working days can be set in the settings.
	*
	* $start the timestamp of the starting date
	* $end the time stamp of the ending date
	*
	* return the number of working days between the two dates
	*/
	static function calculateWorkingDays ($start, $end, $properties = null) {
        if ($end < $start)
            return 0;
        
		if (!$properties)
			$properties = self::getProperties();
        
		$holidays = array_keys ($properties['holidays']);
        $working_days = $properties['working_days'];
        
		return self::_calculateDays($start, $end, $working_days, $holidays);
    }
	
	
	/***
	* calculateDays
	* Calculate the number of days between two dates (include holidays)
	*
	* $start the timestamp of the starting date
	* $end the time stamp of the ending date
	*
	* return the number of days between the two dates
	*/
	static function calculateDays ($start, $end) {
        if ($end < $start)
            return 0;
        
		return self::_calculateDays($start, $end, array(0,1,2,3,4,5,6), array());
    }
	
	
	static function getProperties() {
        // get properties
        $properties = array();
        
        $properties = DAO_DevblocksExtensionPropertyStore::get(self::ID, 'properties', '');
        
        if (empty ($properties)) {
            $properties = array ();
        } else {
            $properties = unserialize ($properties);
        }
        
        
        // add default values
        
        if (!isset($properties['working_days']))
            $properties['working_days'] = array(1,2,3,4,5);
            
        if (!isset($properties['holidays']))
            $properties['holidays'] = array();
            
        if (!isset($properties['customer_type_field_id']))
            $properties['customer_type_field_id'] = 0;
            
        if (!isset($properties['sla']))
            $properties['sla'] = array();
        
		return $properties;
    }
	
	/*** ------------------ PRIVATE FUNCTIONS ------------------ ***/
	
	/***
	* _calculateDays
	* Calculate the number of days between two dates, including only $working_days days and excluding $holidays.
	*
	* $start the timestamp of the starting date
	* $end the time stamp of the ending date
	* $working_days array containing the working days (0=Sun, 1=Mon, ...)
	* $holidays array containing the holidays to avoid in counting (key=>date in YYYY-MM-DD format, value=>name of the holiday)
	*
	* return the number of days between the two dates
	*/
	private static function _calculateDays ($start, $end, $working_days, $holidays) {
        if ($end < $start)
            return 0;
        
        $start_date = date ("Y-m-d", $start);
        $end_date = date ("Y-m-d", $end);
        
        $cur_date = $start_date;
        
        $num_days = 0;
        while ($cur_date != $end_date) {
            if (in_array (date("w", strtotime ($cur_date)), $working_days)) {
                if (!in_array ($cur_date, $holidays)) {
                    $num_days++;
                }
            }
            
            $cur_date = date ("Y-m-d", strtotime ("+1 days", strtotime($cur_date)));
        }
        
        return $num_days;
    }
};
?>
