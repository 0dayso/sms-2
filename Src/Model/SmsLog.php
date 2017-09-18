<?php

/**
 * @author RonyLee <RonyLee.Lyz@gmail.com>
 * @date   2017/7/4
 */

namespace Sms\Model;


/**
 * Class SmsLog
 * @package Sms\Model
 */
class SmsLog extends BaseModel
{

    const TABLE = 'sms_log_tbl';

    /**
     * SmsLog constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 保存日志
     * @param $logData
     * @return \Doctrine\DBAL\Driver\Statement|int|string
     */
    public function save($logData)
    {
        $mobile = isset($logData['mobile']) ? $logData['mobile'] : '';
        $areaCode = isset($logData['area_code']) ? $logData['area_code'] : '';
        $content = isset($logData['content']) ? $logData['content'] : '';
        $gateway = isset($logData['gateway']) ? $logData['gateway'] : '';
        $productId = isset($logData['product_id']) ? $logData['product_id'] : '';
        $response = isset($logData['response']) ? $logData['response'] : '';
        $errCode = isset($logData['err_code']) ? $logData['err_code'] : '';
        $ip = isset($logData['ip']) ? $logData['ip'] : '';

        $columns = array(
            'mobile' => $mobile, // 手机号码
            'area_code' => $areaCode,
            'content' => $content, // 短信内容
            'gateway' => $gateway, // 网关
            'product_id' => $productId, // 产品ID：由程序配置
            'response' => $response, // 网关响应
            'err_code' => $errCode, // 错误码
            'ip' => $ip, // IP
            'create_time' => time(), // 创建时间
            'update_time' => time(), // 更新时间
        );

        return $this->insert(self::TABLE, $columns);
    }

}