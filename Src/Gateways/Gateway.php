<?php

/**
 * @author RonyLee <RonyLee.Lyz@gmail.com>
 * @date   2017/6/14
 */

namespace Sms\Gateways;

use Sms\Contracts\GatewayInterface;
use Sms\Contracts\MessageInterface;
use Sms\Config\Config;

class Gateway implements GatewayInterface
{

    const DEFAULT_TIMEOUT = 5.0;

    /**
     * 短信组件配置
     * @var array
     */
    public $config;

    /**
     * 当前网关配置
     * @var
     */
    public $currConfig;

    /**
     * @var float
     */
    protected $timeout;

    /**
     * Gateway constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->currConfig = $config;
        $this->config = new Config();
    }

    /**
     * Return timeout.
     *
     * @return int|mixed
     */
    public function getTimeout()
    {
        return $this->timeout ?: $this->config->get('timeout', self::DEFAULT_TIMEOUT);
    }

    /**
     * Set timeout.
     *
     * @param int $timeout
     *
     * @return $this
     */
    public function setTimeout($timeout)
    {
        $this->timeout = floatval($timeout);

        return $this;
    }

    /**
     * 返回当前网关配置
     * @return array
     */
    public function getConfig()
    {
        return $this->currConfig;
    }

    /**
     * 设置配置
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config)
    {
        $this->config = array_merge($this->currConfig, $config);

        return $this;
    }

    /**
     * 发送短信
     * @param string           $to
     * @param MessageInterface $message
     * @param int              $productId
     */
    public function send($to, MessageInterface $message, $productId)
    {
        // TODO: Implement send() method.
    }

    /**
     * 返回短信签名
     * @param int  $productId
     * @param bool $isFormat
     */
    public function getSignName($productId = 1, $isFormat = false)
    {
        $proArray = $this->config->get('product');
        if (array_key_exists($productId, $proArray)) {
            $signName = $proArray[$productId]['sign_name'];
        } else {
            $signName = $proArray[1]['sign_name'];
        }
        return $isFormat ? '【' . $signName . '】' : $signName;
    }

}