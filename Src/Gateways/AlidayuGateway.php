<?php

/**
 * @author RonyLee <RonyLee.Lyz@gmail.com>
 * @date   2017/6/13
 */

namespace Sms\Gateways;

use Sms\Contracts\MessageInterface;
use Sms\Exceptions\GatewayErrorException;
use Traits\PocoHttp;

/**
 * 阿里大于短信网关
 * Class AlidayuGateway.
 * @see http://open.taobao.com/doc2/apiDetail?apiId=25450#s2
 */
class AlidayuGateway extends Gateway
{
    use PocoHttp;

    const ENDPOINT_URL = 'https://eco.taobao.com/router/rest';
    const ENDPOINT_METHOD = 'alibaba.aliqin.fc.sms.num.send';
    const ENDPOINT_VERSION = '2.0';
    const ENDPOINT_FORMAT = 'json';

    /**
     * 发送短信
     * @param string           $to
     * @param MessageInterface $message
     * @return array
     */
    public function send($to, MessageInterface $message, $productId)
    {
        $params = [
            'method' => self::ENDPOINT_METHOD,
            'format' => self::ENDPOINT_FORMAT,
            'v' => self::ENDPOINT_VERSION,
            'sign_method' => 'md5',
            'timestamp' => date('Y-m-d H:i:s'),
            'sms_type' => 'normal',
            'sms_free_sign_name' => $this->getSignName($productId),
            'app_key' => $this->currConfig['app_key'],
            'sms_template_code' => $message->getTemplate(),
            'rec_num' => strval($to),
            'sms_param' => json_encode($message->getData()),
        ];

        $params['sign'] = $this->generateSign($params);

        $result = $this->post(self::ENDPOINT_URL, $params);

        $this->setErrorCode($result);

        return $result;
    }

    /**
     * Generate Sign.
     *
     * @param array $params
     *
     * @return string
     */
    protected function generateSign($params)
    {
        ksort($params);
        $stringToBeSigned = $this->currConfig['secret_key'];
        foreach ($params as $key => $value) {
            if (is_string($value) && '@' != substr($value, 0, 1)) {
                $stringToBeSigned .= "$key$value";
            }
        }

        $stringToBeSigned .= $this->currConfig['secret_key'];

        return strtoupper(md5($stringToBeSigned));
    }

    /**
     * 根据发送结果设置错误码
     * @param $result
     * @throws GatewayErrorException
     */
    public function setErrorCode($result)
    {
        $resArray = json_decode($result, true);

        if (isset($resArray['error_response'])) {

            $codeList = $this->config->get('code');
            $subCode = $resArray['error_response']['sub_code'];

            switch ($subCode){
                case 'isv.AMOUNT_NOT_ENOUGH':
                    $code = '1101'; // 余额不足
                    break;
                case 'isv.OUT_OF_SERVICE':
                    $code = '1102'; // 业务停机
                    break;
                case 'isv.ACCOUNT_NOT_EXISTS': // 不合法帐号
                case 'isv.ACCOUNT_ABNORMAL':
                    $code = '1005';
                    break;
                case 'isv.INVALID_PARAMETERS': // 不合法参数
                case 'isv.INVALID_JSON_PARAM':
                    $code = '1007';
                    break;
                case 'isv.SMS_SIGNATURE_ILLEGAL': // 不合法签名
                    $code = '1002';
                    break;
                case 'isv.MOBILE_COUNT_OVER_LIMIT': // 号码数量限制
                    $code = '1202';
                    break;
                case  'isv.MOBILE_NUMBER_ILLEGAL': // 不合法手机号码
                    $code = '1001';
                    break;
                case 'isv.TEMPLATE_MISSING_PARAMETERS': // 不合法内容
                case 'isv.PARAM_LENGTH_LIMIT':
                case 'isv.PARAM_NOT_SUPPORT_URL':
                case 'isv.BLACK_KEY_CONTROL_LIMIT':
                    $code = '1004';
                    break;
                case 'isv.SMS_TEMPLATE_ILLEGAL': // 模板不合法
                    $code = '1003';
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
