<?php defined('IN_ECJIA') or exit('No permission resources.');?>
<!-- {extends file="ecjia.dwt.php"} -->

<!-- {block name="footer"} -->
<script type="text/javascript">
	ecjia.admin.payment_list.init();
</script>
<!-- {/block} -->

<!-- {block name="main_content"} -->
<div>
	<h3 class="heading">
		<!-- {if $ur_here}{$ur_here}{/if} -->
		{if $action_link}
		<a href="{$action_link.href}" class="btn plus_or_reply data-pjax" id="sticky_a"><i class="fontello-icon-reply"></i>{$action_link.text}</a>
		{/if}
	</h3>
</div>

<div class="row-fluid batch" >
	<form method="post" action="{$search_action}" name="searchForm">
		<div class="top_right f_r" >
			<input type="text" name="order_sn" value="{$smarty.get.order_sn}" placeholder="{lang key='payment::payment.find_order_sn'}"/>
			<input type="text" name="trade_no" value="{$smarty.get.trade_no}" placeholder="{lang key='payment::payment.find_trade_no'}"/>
			<button class="btn m_l5" type="submit">{lang key='user::users.serach'}</button>
		</div>
	</form>
</div>

<div class="row-fluid">
	<div class="span12">
		<table class="table table-striped table-hide-edit" data-rowlink="a">
			<thead>
				<tr>
					<th class="w80">{lang key='payment::payment.order_sn'}</th>
					<th class="w80">{lang key='payment::payment.trade_type'}</th>
					<th class="w80">{lang key='payment::payment.trade_no'}</th>
					<th class="w80">{lang key='payment::payment.pay_name'}</th>
					<th class="w80">{lang key='payment::payment.total_fee'}</th>
					<th class="w80">{lang key='payment::payment.create_time'}</th>
					<th class="w80">{lang key='payment::payment.pay_status'}</th>
				</tr>
			</thead>

			<!-- {foreach from=$modules item=list} -->
			<tr>
				<td class="first-cell" >{$list.order_sn}</td>
				<td class="first-cell" >{$list.trade_type}</td>
				<td class="first-cell" >{$list.trade_no}</td>
				<td class="first-cell" >{$list.pay_name}</td>
				<td class="first-cell" >{$list.total_fee}</td>
				<td class="first-cell" >{$list.create_time}</td>
				<td class="first-cell" >{$list.pay_status}</td>
			</tr>
			<!-- {/foreach} -->
		</table>	
	</div>
</div>
<!-- {/block} -->