{if $ticket_sla_info.sla_days > 0}
	<span id="ticket_sla_days">
		<span class="cerb-sprite sprite-stopwatch" style="padding-bottom: 4px"></span>
		<strong>SLA:</strong>
			<span>{$ticket_sla_info.sla_days} {if $ticket_sla_info.sla_type == 'b'}business{/if} day{if $ticket_sla_info.sla_days != 1}s{/if}</span>
	</span>

	<span id="ticket_sla_first_response">
		<strong>First Response:</strong>
			{if $ticket_sla_info.sla_type == "b"}
				<span style="{if $ticket_sla_info.response_business_days == -1 && $ticket_sla_info.response_business_days <= $ticket_sla_info.sla_days}color:#CA0;font-weight:bold;{else if $ticket_sla_info.response_business_days > $ticket_sla_info.sla_days}color:#C00;font-weight:bold;{/if}">
					{if $ticket_sla_info.response_business_days == -1}
						none
					{else if $ticket_sla_info.response_business_days == 0}
						same day
					{else}
						in {$ticket_sla_info.response_business_days} business days
					{/if}
				</span>
			{else}
				<span style="{if $ticket_sla_info.response_days == -1 && $ticket_sla_info.response_days <= $ticket_sla_info.sla_days}color:#CA0;font-weight:bold;{else if $ticket_sla_info.response_days > $ticket_sla_info.sla_days}color:#C00;font-weight:bold;{/if}">
					{if $ticket_sla_info.response_days == -1}
						none
					{else if $ticket_sla_info.response_days == 0}
						same day
					{else}
						in {$ticket_sla_info.response_days} days
					{/if}
				</span>
			{/if}
	</span>
	
	<span id="ticket_sla_last_response">	
		<strong>Last Response:</strong>
			<span style="{if $ticket_sla_info.last_response_time == -1}color:#C00;{/if}">
				{if $ticket_sla_info.last_response_time == -1}
					none
				{else}
					{if $ticket_sla_info.sla_type == "b"}
						{$ticket_sla_info['last_response_business_days_ago']} business days ago
					{else}
						{$ticket_sla_info['last_response_days_ago']} days ago
					{/if}
				{/if}
			</span>
	</span>
{/if}