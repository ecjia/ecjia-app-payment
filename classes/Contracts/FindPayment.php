<?php
/**
 * Created by PhpStorm.
 * User: royalwang
 * Date: 2018/11/27
 * Time: 13:32
 */
namespace Ecjia\App\Payment\Contracts;

interface FindPayment
{

    /**
     * @return array | \ecjia_error
     */
    public function find();

}