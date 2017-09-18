<?php

/**
 * @author RonyLee <RonyLee.Lyz@gmail.com>
 * @date   2017/6/13
 */

namespace Sms\Contracts;

/**
 * 消息接口
 * Interface MessageInterface
 */
interface MessageInterface
{

    const TEXT_MESSAGE = 'text';
    const VOICE_MESSAGE = 'voice';

    /**
     * 返回消息类型
     * @return mixed
     */
    public function getMessageType();

    /**
     * 返回消息内容
     * @param GatewayInterface|null $gateway
     * @return mixed
     */
    public function getContent(GatewayInterface $gateway = null);

    /**
     * 返回消息模板
     * @param GatewayInterface|null $gateway
     * @return mixed
     */
    public function getTemplate(GatewayInterface $gateway = null);

    /**
     * 模板消息内容
     * @param GatewayInterface|null $gateway
     * @return mixed
     */
    public function getTemplateContent(GatewayInterface $gateway = null);

    /**
     * 返回模板数据
     * @param GatewayInterface|null $gateway
     * @return mixed
     */
    public function getData(GatewayInterface $gateway = null);

    /**
     * 返回网关
     * @return mixed
     */
    public function getGateways();

}