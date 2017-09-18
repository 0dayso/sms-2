<?php

/*
 * This file is part of the overtrue/easy-sms.
 * (c) overtrue <i@overtrue.me>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sms\Exceptions;

use Exception;

/**
 * Class GatewayErrorException.
 */
class GatewayErrorException extends Exception
{

    public $err_code = ''; // 错误代码
    public $err_msg = ''; // 错误信息
    public $gateway_result = ''; // 网关直接返回的结果

    /**
     * GatewayErrorException constructor.
     *
     * @param array $raw
     */
    public function __construct($message, $code, $result)
    {
        parent::__construct($message, intval($code));

        $this->err_code = $code;
        $this->err_msg = $message;
        $this->gateway_result = $result;

    }



}
