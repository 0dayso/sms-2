<?php
/**
 * @author RonyLee <RonyLee.Lyz@gmail.com>
 * @date   2017/6/27
 */

namespace Sms\Gateways;

use Sms\Contracts\MessageInterface;
use Sms\Exceptions\GatewayErrorException;
use Traits\PocoHttp;

/**
 * 昊博短信网关
 * Class HaoboGateway
 * @package Sms\Gateways
 */
class HaoboGateway extends Gateway
{
    use PocoHttp;

    /**
     * 接口地址
     */
    const ENDPOINT_URL = 'http://101.227.68.49:7891/mt?';

    /**
     * 编码，utf-8
     */
    const CHARSET = '15';

    /**
     * 是否需要状态报告
     */
    const STATUS_REPORT = '1';

    /**
     * 定时发送，为空时表示立即发送
     */
    const SEND_DELAYED = '';

    /**
     * 发送短信
     * @param string           $to
     * @param MessageInterface $message
     * @param integer          $productId
     * @return array
     */
    public function send($to, MessageInterface $message, $productId)
    {
        $content = $message->getContent() . $this->getSignName($productId, true);
        $content = mb_convert_encoding($content, 'GB2312', 'UTF-8');
        $content = bin2hex($content);

        $params = array(
            'un' => $this->currConfig['account'],
            'pw' => $this->currConfig['password'],
            'da' => strval($to),
            'sm' => $content,
            'dc' => self::CHARSET,
            'rd' => self::STATUS_REPORT,
            'st' => self::SEND_DELAYED,
        );

        $result = $this->get(self::ENDPOINT_URL, $params);

        $this->setErrorCode($result);

        return $result;
    }

    /**
     * 根据发送结果设置错误码
     * 如果发送失败返回: r=<错误码>
     * 如果发送成功返回: id=<消息编号>
     * 或者返回: r=0&id=<消息编号>
     * @param $result
     * @throws GatewayErrorException
     */
    public function setErrorCode($result)
    {
        $errCode = 0;
        $resArray = explode('&', $result);
        foreach ($resArray as $key => $value) {
            $itemArray = explode('=', $value);
            if ($itemArray[0] == 'r' && (int)$itemArray[1] > 0) {
                $errCode = $itemArray[1];
            }
        }
        if ($errCode > 0) {
            $codeList = $this->config->get('code');
            switch ($errCode){
                case '9401':
                    $code = '1101'; // 余额不足
                    break;
                case '9102': // 不合法帐号
                case '9107':
                case '9405':
                case '9412':
                    $code = '1005';
                    break;
                case '9101': // 不合法参数
                    $code = '1007';
                    break;
                case '9027': // 不合法签名
                    $code = '1002';
                    break;
                case  '9020': // 不合法手机号码
                case  '9501':
                    $code = '1001';
                    break;
                case '9012': // 不合法内容
                case '9014':
                case '9022':
                case '9402':
                    $code = '1004';
                    break;
                default :
                    $code = '1301'; // 其它错误
                    break;
            }
            $message = $codeList[$code];
            throw new GatewayErrorException($message, $code, $result);
        }
    }


}