<?php
/**
 * Created by PhpStorm.
 * User: royalwang
 * Date: 2018/11/27
 * Time: 13:32
 */
namespace Ecjia\App\Payment\Contracts;

interface ScanPayment
{

    /**
     * 扫码收款
     *
     * @param string $dynamic_code 动态条形码
     * @return array | \ecjia_error
     */
    public function scan($dynamic_code);

}