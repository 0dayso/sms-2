<?php

/**
 * @author RonyLee <RonyLee.Lyz@gmail.com>
 * @date   2017/6/13
 */


namespace Sms;

use Sms\Contracts\MessageInterface;
use Sms\Exceptions\InvalidArgumentException;
use Sms\Model\SmsLog;
use Sms\Config\Config;
use Exception;
use Symfony\Component\HttpFoundation\Request;

/**
 * 短信类
 * Class ShortMessage
 */
class  ShortMessage
{

    /**
     * 配置
     * @var
     */
    public $config;

    /**
     * 网关
     * @var
     */
    public $gateways;


    /**
     * 构造函数
     * ShortMessage constructor.
     */
    public function __construct()
    {
        $this->config = new Config(); // 加载配置实例
    }

    /**
     * 发送短信
     * @param string $to        接收手机号
     * @param array  $message   发送信息
     * @param array  $productId 产品ID
     * @param string  $areaCode 电话地区号
     * @return mixed
     */
    public function send($to, $message, $productId = 1, $areaCode = '86')
    {
        if ($this->config->get('on_off') == 'off') {
            return 'The gateway is closed!';
        }

        $message = $this->getMessageInstance($message);

        $gateways = $this->config->get('default.gateways', []);

        $result = [];
        foreach ($gateways as $gateway) {
            try {
                $this->getGatewayInstance($gateway)->send($to, $message, $productId);
                $result = [
                    'err_code' => 0,
                    'message' => 'success',
                    'gateway' => $gateway,
                    'product_id' => $productId,
                ];
                break;
            } catch (Exception $e) {
                $result = [
                    'err_code' => $e->err_code,
                    'message' => $e->err_msg,
                    'gateway' => $gateway,
                    'gateway_result' => $e->gateway_result,
                    'product_id' => $productId
                ];
                continue;
            }
        }

        $this->saveLog($to, $message, $result, $areaCode); // 保存日志

        return $result;
    }

    /**
     * 返回消息实例
     * @param array|string $message
     * @return array|Message
     */
    protected function getMessageInstance($message)
    {
        if (!($message instanceof MessageInterface)) {
            if (!is_array($message)) {
                $message = [
                    'content' => strval($message),
                    'template' => strval($message),
                ];
            }

            $message = new Message($message);
        }

        return $message;
    }

    /**
     * 返回网关实例
     * @param $name
     * @return mixed
     */
    protected function getGatewayInstance($name)
    {
        if (!isset($this->gateways[$name])) {
            $config = $this->config->get("gateways.{$name}", []);

            $className = $this->getGatewayClassName($name);

            if (!class_exists($className)) {

                throw new InvalidArgumentException(sprintf('Gateway "%s" not exists.', $name));
            }

            $this->gateways[$name] = new $className($config, $this);
        }

        return $this->gateways[$name];
    }

    /**
     * 返回网关类名
     * @param $name
     * @return string
     */
    protected function getGatewayClassName($name)
    {
        if (class_exists($name)) {
            return $name;
        }

        $name = ucfirst(str_replace(['-', '_', ''], '', $name));

        return __NAMESPACE__ . "\\Gateways\\{$name}Gateway";
    }

    /**
     * 保存日志
     * @param                  $ateway
     * @param                  $to
     * @param MessageInterface $message
     * @param                  $result
     * @param                  $areaCode
     */
    protected function saveLog($to, MessageInterface $message, $result, $areaCode = '')
    {
        $gatewayInstance = $this->getGatewayInstance($result['gateway']);

        if (isset($message->template) && array_key_exists('templates', $gatewayInstance->currConfig)) {
            $content = $message->getTemplateContent($gatewayInstance);
        } else {
            $content = $message->getContent($gatewayInstance);
        }

        // 保存日志
        (new SmsLog)->save([
            'mobile' => $to,
            'area_code' => $areaCode,
            'content' => $content,
            'gateway' => $result['gateway'],
            'product_id' => $result['product_id'],
            'response' => isset($result['gateway_result']) ? $result['gateway_result'] : '',
            'err_code' => $result['err_code'],
            'ip' => ip2long((new Request($_GET, $_POST, array(), $_COOKIE, $_FILES, $_SERVER))->getClientIp()),
        ]);
    }

}