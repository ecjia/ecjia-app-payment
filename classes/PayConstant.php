<?php
//
//    ______         ______           __         __         ______
//   /\  ___\       /\  ___\         /\_\       /\_\       /\  __ \
//   \/\  __\       \/\ \____        \/\_\      \/\_\      \/\ \_\ \
//    \/\_____\      \/\_____\     /\_\/\_\      \/\_\      \/\_\ \_\
//     \/_____/       \/_____/     \/__\/_/       \/_/       \/_/ /_/
//
//   上海商创网络科技有限公司
//
//  ---------------------------------------------------------------------------------
//
//   一、协议的许可和权利
//
//    1. 您可以在完全遵守本协议的基础上，将本软件应用于商业用途；
//    2. 您可以在协议规定的约束和限制范围内修改本产品源代码或界面风格以适应您的要求；
//    3. 您拥有使用本产品中的全部内容资料、商品信息及其他信息的所有权，并独立承担与其内容相关的
//       法律义务；
//    4. 获得商业授权之后，您可以将本软件应用于商业用途，自授权时刻起，在技术支持期限内拥有通过
//       指定的方式获得指定范围内的技术支持服务；
//
//   二、协议的约束和限制
//
//    1. 未获商业授权之前，禁止将本软件用于商业用途（包括但不限于企业法人经营的产品、经营性产品
//       以及以盈利为目的或实现盈利产品）；
//    2. 未获商业授权之前，禁止在本产品的整体或在任何部分基础上发展任何派生版本、修改版本或第三
//       方版本用于重新开发；
//    3. 如果您未能遵守本协议的条款，您的授权将被终止，所被许可的权利将被收回并承担相应法律责任；
//
//   三、有限担保和免责声明
//
//    1. 本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的；
//    2. 用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未获得商业授权之前，我们不承
//       诺提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任；
//    3. 上海商创网络科技有限公司不对使用本产品构建的商城中的内容信息承担责任，但在不侵犯用户隐
//       私信息的前提下，保留以任何方式获取用户信息及商品信息的权利；
//
//   有关本产品最终用户授权协议、商业授权与技术服务的详细内容，均由上海商创网络科技有限公司独家
//   提供。上海商创网络科技有限公司拥有在不事先通知的情况下，修改授权协议的权力，修改后的协议对
//   改变之日起的新授权用户生效。电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和
//   等同的法律效力。您一旦开始修改、安装或使用本产品，即被视为完全理解并接受本协议的各项条款，
//   在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本
//   授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。
//
//  ---------------------------------------------------------------------------------
//

namespace Ecjia\App\Payment;


class PayConstant
{

    /**
     * =======================================
     * 常见支付订单类型
     * =======================================
     */
    /**
     * 订单支付
     * @var string
     */
    const PAY_ORDER = 'buy';

    /**
     * 分单订单支付
     * @var string
     */
    const PAY_SEPARATE_ORDER = 'separate';

    /**
     * 会员预付款
     * @var string
     */
    const PAY_SURPLUS = 'surplus';
    
    /**
     * 闪付订单
     * @var string
     */
    const PAY_QUICKYPAY = 'quickpay';


    /**
     * ====================================
     * 废弃支付类型
     * ====================================
     */
    /**
     * 会员充值提现
     * @var string
     */
    const PAY_DEPOSIT = 'deposit';
    const PAY_WITHDRAW = 'withdraw';
    //@end


    /**
     * PC平台
     * @var number
     */
    const PLATFORM_PC       = 0b00000001;
    
    /**
     * 手机APP平台
     * @var number
     */
    const PLATFORM_APP      = 0b00000010;
    
    /**
     * H5平台
     * @var number
     */
    const PLATFORM_H5       = 0b00000100;
    
    /**
     * 微信小程序平台
     * @var number
     */
    const PLATFORM_WEAPP    = 0b00001000;
    
    
    /**
     * 支付代码类型 1 => 表单
     * @var integer
     */
    const PAYCODE_FORM     = 1;
    
    /**
     * 支付代码类型 2 => 链接
     * @var integer
     */
    const PAYCODE_STRING   = 2;
    
    /**
     * 支付代码类型 3 => 数组
     * @var integer
     */
    const PAYCODE_PARAM    = 3;


    /**
     * 流水记录的支付状态
     */
    const PAYMENT_RECORD_STATUS_WAIT        = 0; //等待支付
    const PAYMENT_RECORD_STATUS_PAYED       = 1; //支付完成
    const PAYMENT_RECORD_STATUS_PROGRESS    = 2; //支付进行中
    const PAYMENT_RECORD_STATUS_FAIL        = 11; //支付失败
    const PAYMENT_RECORD_STATUS_CANCEL      = 21; //订单撤消
    const PAYMENT_RECORD_STATUS_REFUND      = 22; //订单退款


    /**
     * 退款流水状态
     */
    const PAYMENT_REFUND_STATUS_CREATE      = 0; //创建退款请求
    const PAYMENT_REFUND_STATUS_REFUND      = 1; //确认退款
    const PAYMENT_REFUND_STATUS_PROGRESS    = 1; //退款处理中
    const PAYMENT_REFUND_STATUS_FAIL        = 11; //退款失败
    const PAYMENT_REFUND_STATUS_CLOSE       = 12; //退款失败

    
    protected static $payways = [
        '1'     => '支付宝',
        '2'     => '支付宝',
        '3'     => '微信',
        '4'     => '百度钱包',
        '5'     => '京东钱包',
        '6'     => 'qq钱包',
        '7'     => 'NFC支付',
        '8'     => '拉卡拉钱包',
        '9'     => '和包支付',
        '15'    => '拉卡拉微信',
        '16'    => '招商银行',
        '17'    => '银联二维码',
        '18'    => '翼支付',
        '19'    => 'Weixin-Local',
        '100'   => '储值支付',
    ];

    protected static $sub_payways = [
        '1' => '条码支付',
        '2' => '二维码支付',
        '3' => 'wap支付',
        '4' => '小程序支付',
        '5' => 'APP支付',
        '6' => 'H5支付',
    ];


    /**
     * 获取一级支付方式名称
     *
     * @param $sub_payway
     * @return mixed
     */
    public static function getPayway($payway)
    {
        return array_get(self::$payways, $payway, '未知');
    }


    /**
     * 获取二级支付方式名称
     *
     * @param $sub_payway
     * @return mixed
     */
    public static function getSubPayway($sub_payway)
    {
        return array_get(self::$sub_payways, $sub_payway, '未知');
    }


}
