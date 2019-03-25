<?php defined('IN_ECJIA') or exit('No permission resources.');?>
<!-- {extends file="ecjia.dwt.php"} -->

<!-- {block name="footer"} -->
<script type="text/javascript">
	ecjia.admin.payment_record.init();
</script>
<!-- {/block} -->
<!-- {block name="main_content"} -->

<div>
    <h3 class="heading">
        <!-- {if $ur_here}{$ur_here}{/if} -->
        {if $action_link}
        	<a  href="{$action_link.href}" class="btn plus_or_reply data-pjax" id="sticky_a"><i class="fontello-icon-reply"></i>{$action_link.text}</a>
        {/if}
        {if $change_status eq '1'}
        	<button type='button' data-url='{url path="payment/admin_payment_record/change_order_status" args="id={$modules.id}"}' class="btn change_status plus_or_reply data-pjax" id="sticky_a">{t domain="payment"}修复订单状态{/t}</button>
    	{/if}
    </h3>
</div>

<div class="row-fluid">
    <div class="span12">
        <div class="form-inline foldable-list">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle acc-in" data-toggle="collapse" data-target="#collapseOne"><strong>{t domain="payment"}资金流水记录{/t}</strong></a>
                </div>
                <div class="accordion-body in in_visable collapse" id="collapseOne">
                    <table class="table table-oddtd m_b0">
                        <tbody class="first-td-no-leftbd">
                        <tr>
                            <td><div align="right"><strong>{t domain="payment"}商城订单编号{/t}</strong></div></td>
                            <td>
	                            {if $modules.trade_type eq 'buy'}
	                            	<a target="_blank" href='{url path="orders/admin/info" args="order_id={$order.order_id}"}'>{$modules.order_sn}</a>
	                            {elseif $modules.trade_type eq 'quickpay'}
	                            	<a target="_blank" href='{url path="quickpay/admin_order/order_info" args="order_id={$quickpay_order.order_id}"}'>{$modules.order_sn}</a>
	                            {elseif $modules.trade_type eq 'separate'}
	                            	{$modules.order_sn}
	                            {else}
	                            	<a target="_blank" href='{url path="finance/admin_account/check" args="order_sn={$user_account.order_sn}&id={$user_account.id}{if $type}&type={$type}{/if}"}'>{$modules.order_sn}</a>
	                            {/if}
                            </td>
                            <td><div align="right"><strong>{t domain="payment"}交易状态{/t}</strong></div></td>
                            <td>{$modules.label_pay_status}</td>
                        </tr>
                        <tr>
                            <td><div align="right"><strong>{t domain="payment"}交易类型{/t}</strong></div></td>
                            <td>{$modules.label_trade_type}</td>
                            <td><div align="right"><strong>{t domain="payment"}流水号{/t}</strong></div></td>
                            <td>{$modules.trade_no}</td>
                        </tr>
                        <tr>
                            <td><div align="right"><strong>{t domain="payment"}支付方式{/t}</strong></div></td>
                            <td>{$modules.pay_code}</td>
                            <td><div align="right"><strong>{t domain="payment"}支付名称{/t}</strong></div></td>
                            <td>{$modules.pay_name} </td>
                        </tr>
                        <tr>
                            <td><div align="right"><strong>{t domain="payment"}支付金额{/t}</strong></div></td>
                            <td>{$modules.total_fee}</td>
                            <td><div align="right"><strong>{t domain="payment"}创建时间{/t}</strong></div></td>
                            <td>{$modules.create_time}</td>
                        </tr>
                        <tr>
                            <td><div align="right"><strong>{t domain="payment"}修改更新时间{/t}</strong></div></td>
                            <td>{$modules.update_time}</td>
                            <td><div align="right"><strong>{t domain="payment"}支付成功时间{/t}</strong></div></td>
                            <td>{$modules.pay_time}</td>
                        </tr>
                         <tr>
                            <td><div align="right"><strong>{t domain="payment"}支付订单号：{/t}</strong></div></td>
                            <td colspan="3">{$modules.order_trade_no}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

			<div class="accordion-group">
				<div class="accordion-heading">
				    <a class="accordion-toggle acc-in" data-toggle="collapse" data-target="#collapseTwo"><strong>{t domain="payment"}订单信息{/t}</strong></a>
				</div>
				<div class="accordion-body in in_visable collapse" id="collapseTwo">
				    <table class="table table-oddtd m_b0">
				        <tbody class="first-td-no-leftbd">
				        <!-- {if !$check_modules and $modules.trade_type neq 'refund'} -->
					        <!-- {if $modules.trade_type eq 'surplus'} -->
						        <tr>
						            <td><div align="right"><strong>{t domain="payment"}订单总金额：{/t}</strong></div></td>
						            <td>{$user_account.formated_order_amount}</td>
						            <td><div align="right"><strong>{t domain="payment"}订单状态：{/t}</strong></div></td>
						            <td>{$user_account.formated_order_status}</td>
						        </tr>
						    <!-- {elseif $modules.trade_type eq 'separate'} -->
						    	<tr>
						            <td><div align="right"><strong>{t domain="payment"}订单总金额：{/t}</strong></div></td>
						            <td>{$order.formated_order_amount}</td>
						            <td><div align="right"><strong>{t domain="payment"}订单状态：{/t}</strong></div></td>
						            <td>{$order.formated_order_status}</td>
						        </tr>
					        <!-- {else} -->
					        	<tr>
						            <td><div align="right"><strong>{t domain="payment"}买单消费总金额：{/t}</strong></div></td>
						            <td>{$quickpay_order.formated_goods_amount}</td>
						            <td><div align="right"><strong>{t domain="payment"}买单优惠总金额：{/t}</strong></div></td>
						            <td>{$quickpay_order.formated_total_discount}</td>
						        </tr>
						        <tr>
						            <td><div align="right"><strong>{t domain="payment"}买单实付金额：{/t}</strong></div></td>
						            <td>{$quickpay_order.formated_order_amount}</td>
						            <td><div align="right"><strong>{t domain="payment"}订单状态：{/t}</strong></div></td>
						            <td>{$quickpay_order.formated_order_status}</td>
						        </tr>
					        <!-- {/if} -->
				        <!-- {/if} -->
				        <!-- {if $check_modules} -->
					        <tr>
					        	<td><div align="right"><strong>{t domain="payment"}订单状态：{/t}</strong></div></td>
					        	<td colspan='3'>{$os[$order.order_status]},{$ps[$order.pay_status]},{$ss[$order.shipping_status]}</td>
					        </tr>
					        <tr>
					            <td><div align="right"><strong>{t domain="payment"}商品总金额：{/t}</strong></div></td>
					            <td>{$order.formated_goods_amount}</td>
					            <td><div align="right"><strong>{t domain="payment"}折扣：{/t}</strong></div></td>
					            <td>{$order.discount}</td>
					        </tr>
					        <tr>
					            <td><div align="right"><strong>{t domain="payment"}发票税额：{/t}</strong></div></td>
					            <td>{$order.tax}</td>
					            <td><div align="right"><strong>{t domain="payment"}订单总金额：{/t}</strong></div></td>
					            <td>{$order.formated_total_fee}</td>
					        </tr>
					        <tr>
					            <td><div align="right"><strong>{t domain="payment"}配送费用：{/t}</strong></div></td>
					            <td>{$order.shipping_fee}</td>
					            <td><div align="right"><strong>{t domain="payment"}已付款金额：{/t}</strong></div></td>
					            <td>{$order.formated_money_paid} </td>
					        </tr>
					        <tr>
					            <td><div align="right"><strong>{t domain="payment"}保价费用：{/t}</strong></div></td>
					            <td>{if $exist_real_goods}{else}0{/if}</td>
					            <td><div align="right"><strong>{t domain="payment"}使用余额：{/t}</strong></div></td>
					            <td>{$order.surplus}</td>
					        <tr>
					            <td><div align="right"><strong>{t domain="payment"}支付费用：{/t}</strong></div></td>
					            <td>{$order.pay_fee}</td>
					            <td><div align="right"><strong>{t domain="payment"}贺卡费用：{/t}</strong></div></td>
					            <td>{$order.card_fee}</td>
					        </tr>
					        <tr>
					            <td><div align="right"><strong>{t domain="payment"}包装费用：{/t}</strong></div></td>
					            <td>{$order.pack_fee}</td>
					            <td><div align="right"><strong>{if $order.order_amount >= 0} {t domain="payment"}应付款金额：{/t} {else} {t domain="payment"}应退款金额：{/t} {/if}</strong></div></td>
					            <td>{$order.formated_order_amount}</td>
					        </tr>
				        <!-- {/if} -->
				        </tbody>
				    </table>
				</div>
			</div>
    		
        </div>
    </div>
</div>
<!-- {/block} -->