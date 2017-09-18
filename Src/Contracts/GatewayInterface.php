<?php
/**
 * @author RonyLee <RonyLee.Lyz@gmail.com>
 * @date   2017/6/14
 */

namespace Sms\Contracts;

/**
 * 短信网关接口
 * Interface GatewayInterface
 */
interface GatewayInterface
{

    /**
     * 发送短信
     * @param string           $to
     * @param MessageInterface $message
     * @param integer          $productId
     * @return mixed
     */
    public function send($to, MessageInterface $message, $productId);

}