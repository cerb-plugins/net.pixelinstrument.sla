{if $ticket_sla_info.sla_end_date > 0}
	<span id="ticket_sla_days">
		<span class="cerb-sprite sprite-stopwatch" style="padding-bottom: 4px"></span>
		<strong>SLA:</strong>
			<span>{$ticket_sla_info.sla_end_date|date_format:"%e %b. %Y"}</span>
	</span>

	{assign var=sla_color value=''}
	<span id="ticket_sla_first_response">
		<strong>First Response:</strong>
			{if $ticket_sla_info.sla_status == "yellow"}
				{assign var=sla_color value='#CA0'}
			{else if $ticket_sla_info.sla_status == "red"}
				{assign var=sla_color value='#C00'}
			{/if}
				
			<span style="{if $sla_color}color:{$sla_color};font-weight:bold;{/if}">
				{if $response_days == -1}
					none
				{else if $response_days == 0}
					same day
				{else}
					in {$response_days} {if $ticket_sla_info.sla_type == "b"}business{/if} days
				{/if}
			</span>
	</span>
	
	<span id="ticket_sla_last_response">	
		<strong>Last Response:</strong>
			<span style="{if $sla_color}color:{$sla_color};font-weight:bold;{/if}">
				{if $ticket_sla_info.last_response_time == -1}
					none
				{else}
					{$response_days_ago} {if $ticket_sla_info.sla_type == "b"}business{/if} days ago
				{/if}
			</span>
	</span>
{/if}