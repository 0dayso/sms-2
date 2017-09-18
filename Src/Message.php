<?php
/**
 * @author RonyLee <RonyLee.Lyz@gmail.com>
 * @date   2017/6/14
 */

namespace Sms;

use Sms\Contracts\MessageInterface;
use Sms\Contracts\GatewayInterface;
use Sms\Config\Config;

/**
 * 消息类
 * Class Message
 * @package Sms
 */
class Message implements MessageInterface
{
    /**
     * 消息类型
     * @var
     */
    public $type;

    /**
     * 消息内容
     * @var
     */
    public $content;

    /**
     * 消息模板
     * @var
     */
    public $template;

    /**
     * 模板数据
     * @var
     */
    public $data;

    /**
     * @var
     */
    public $gateways;

    /**
     * @var string
     */
    public $config;

    /**
     * 构造函数
     * Message constructor.
     */
    public function __construct(array $attributes = [], $type = MessageInterface::TEXT_MESSAGE)
    {
        $this->config = new Config();
        $this->type = $type;

        foreach ($attributes as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
    }

    /**
     * 返回消息类型
     * @return mixed
     */
    public function getMessageType()
    {
        return $this->type;
    }

    /**
     * 设置消息类型
     * @param $type
     * @return $this
     */
    public function setMessageType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * 返回消息内容
     * @param GatewayInterface|null $gateway
     * @return mixed
     */
    public function getContent(GatewayInterface $gateway = null)
    {
        return $this->content;
    }

    /**
     * 返回模板内容
     * @param GatewayInterface|null $gateway
     * @return mixed
     */
    public function getTemplateContent(GatewayInterface $gateway = null)
    {
        $content = '';
        if (array_key_exists('templates', $gateway->currConfig)) {
            $templates = $gateway->currConfig['templates'];
            $templateCode = $this->template;
            if (array_key_exists($templateCode, $templates)) {
                $patterns = [];
                $replacements = [];
                foreach ($this->data as $key => $value) {
                    $patterns[] = '/\${' . $key . '}/';
                    $replacements[] = $value;
                }
                $content = preg_replace($patterns, $replacements, $templates[$templateCode]);
            }
        }
        return $content;
    }

    /**
     * 设置消息内容
     * @param string $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * 返回消息模板
     * @param GatewayInterface|null $gateway
     * @return mixed
     */
    public function getTemplate(GatewayInterface $gateway = null)
    {
        return $this->template;
    }

    /**
     * 设置消息模板
     * @param string $template
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    /**
     * 返回模板数据
     * @param GatewayInterface|null $gateway
     * @return mixed
     */
    public function getData(GatewayInterface $gateway = null)
    {
        return $this->data;
    }

    /**
     * 设置模板数据
     * @param array $data
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * 返回短信网关
     * @return array
     */
    public function getGateways()
    {
        return $this->gateways;
    }

    /**
     * 设置短信网关
     * @param array $gateways
     * @return $this
     */
    public function setGateways(array $gateways)
    {
        $this->gateways = $gateways;
        return $this;
    }

    /**
     * @param $property
     * @return mixed
     */
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }


}