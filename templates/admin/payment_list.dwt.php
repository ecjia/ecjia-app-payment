<?php defined('IN_ECJIA') or exit('No permission resources.');?>
<!-- {extends file="ecjia.dwt.php"} -->

<!-- {block name="footer"} -->
<script type="text/javascript">
ecjia.admin.payment_list.initList();
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
<div class="row-fluid">
	<div class="span12">
		<table class="table table-striped table-hide-edit" data-rowlink="a">
			<thead>
				<tr>
					<th class="w100">{$lang.payment_name}</th>
					<th class="w600">{$lang.payment_desc}</th>
					<th class="w50">{$lang.sort_order}</th>
					<th class="w50">{$lang.short_pay_fee}</th>
				</tr>
			</thead>
			<tbody>
				<!-- {foreach from=$modules item=module} -->
				<!-- {if $module.code neq "tenpayc2c"} -->
				<tr>
					<td >
						<!-- {if $module.enabled == 1} -->
							<span class="pay_name cursor_pointer" data-trigger="editable" data-url="{RC_Uri::url('payment/admin/edit_name')}" data-name="title" data-pk="{$module.id}"  data-title="编辑支付方式名称">{$module.name}</span>
						<!-- {else} -->
							{$module.name}
						<!-- {/if} -->
					</td>
					<td class="hide-edit-area">
						<!-- {if $module.enabled == 1} -->
							{$module.desc|nl2br}
							<div class="edit-list">
								{assign var=payment_edit value=RC_Uri::url('payment/admin/edit',"code={$module.code}")}
								<a class="data-pjax" href="{$payment_edit}" title="{$lang.edit}">{t}编辑{/t}</a>&nbsp;|&nbsp;
								{assign var=payment_disable value=RC_Uri::url('payment/admin/disable',"code={$module.code}")}
								<a class="switch ecjiafc-red" href="javascript:;" data-url="{$payment_disable}" title="{$lang.disable}">{t}禁用{/t}</a>
							</div>
						<!-- {else} -->
							{$module.desc|nl2br}
							<div class="edit-list">
								{assign var=payment_enable value=RC_Uri::url('payment/admin/enable',"code={$module.code}")}
								<a class="switch" href="javascript:;" data-url="{$payment_enable}" title="{$lang.enable}">{t}启用{/t}</a>
							</div>
						<!-- {/if} -->
					</td>
					<td>
						<!-- {if $module.enabled == 1} -->
						<span class="pay_order cursor_pointer" data-trigger="editable" data-url="{RC_Uri::url('payment/admin/edit_order')}" data-name="title" data-pk="{$module.id}" data-title="编辑支付方式排序">{$module.pay_order}</span>
						<!-- {else} -->
						{$module.pay_order}
						<!-- {/if} -->
					</td>
					<td>
						<!-- {if $module.is_cod} -->
							{$lang.decide_by_ship}
						<!-- {else} -->
							{$module.pay_fee}
						<!-- {/if} -->
					</td>
				</tr>
				<!-- {/if} -->
				<!-- {foreachelse} -->
				   <tr><td class="no-records" colspan="10">{t}没有找到任何记录{/t}</td></tr>
				<!-- {/foreach} -->
			</tbody>
		</table>	
	</div>
</div>
<!-- {/block} -->