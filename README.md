# README.md


---

### 调用方式

发送短信的正确姿势

```php

$shortMessage = new \Sms\ShortMessage();

// 模板类型
$result = $shortMessage->send('13500000000', [
    'content'  => '您的验证码为: 6379',
    'template' => 'SMS_13075120',
    'data' => [
        'code' => '6379'
    ],
]);

// 无模板类型
$result = $shortMessage->send('13500000000', 'Hello RonyLee!', 2, 86);



// 产品ID，区号，可以不填写，调用send()会自动保存日志
$shortMessage->send(发送手机, 消息内容, 产品id, 手机区号);

```

----------

返回数据

```php

[
  'err_code' => string '1001'  // 错误码
  'message' => string 'invalid mobile' // 信息
  'gateway' => string 'alidayu' // 短信渠道
  'gateway_result' => string '{}' // 网关直接返回错误信息
]

err_code = 0 为发送成功
err_code > 0 才会返回 gateway_result

```

----------


返回码说明

| 返回码    | 错误码描述   |  说明   |
| :-------  | :---------   | :----   |
|    0      |  success             |    发送成功        | 
|    1001   |  invalid mobile      |    不合法手机号码  | 
|    1002   |  invalid sign        |    不合法签名      |
|    1003   |  invalid template    |    不合法模板      |
|    1004   |  invalid content     |    不合法内容      |
|    1005   |  invalid account     |    不合法帐号      |
|    1006   |  invalid password    |    不合法密码      |
|    1007   |  invalid Params      |    不合法参数      |
|    1101   |  amount no enough    |    余额不足        |
|    1102   |  out of service      |    服务停机        |
|    1201   |  ip  limit           |    IP频率限制      |
|    1202   |  number limit        |    号码数量限制    |
|    1202   |  mobile limit        |    号码频率限制    |
|    1301   |  other error         |    其它错误        |



每个网关，返回的错误码都不一样，需要网关类中实现setErrorCode()方法转成通用错误码，接口响应错误同时会返回原网的错误信息

----------
  
### 网关配置

配置文件：Sms/Src/Config.php

``` php

	'on_off' => 'on'  // 网关开关：on 开，off 关
    
    // 各网关的配置，帐号和密钥，如果是模板类型，可以按格式设置template,为方便获得完全的短信内容，网关下标名称必须对应网关类的名字
    'gateways' => [
        'alidayu' => [
            'app_key' => '23432925',
            'secret_key' => 'ea3f749a2b3cd8eaee1b7c9b6a961679',
            'templates' => [
                'SMS_13075120' => '您的验证码为：${code}，本次短信15分钟有效。',
            ]
        ],
        'haobo' => [
            'account' => '10690095',
            'password' => 'Mei2016',
        ],
    ],
    
    // 默认网关，网关名字，必须对应以上配置的网关，程序会按顺序轮询调用，只有调用失败才会进入下一个网关的调用
    'default' => [
        'gateways' => [
            'haobo'，'alidayu'
        ],
    ],
    
     // 配置不同产品的签名，如调用指定产品ID 1，短信内容就会带上【POCO摄影】，默认为产品1，poco摄影
    'product' => [
        '1' => [
            'id' => 1,
            'sign_name' => 'POCO摄影'
        ],
        '2' => [
            'id' => 2,
            'sign_name' => '游学院'
        ]
    ],
     

```


----------

### 添加网关

1、在 Sms/Src/Gateways，目录下添加网关类，实现接口方法就可以
2、类命名必须与配置对应，如 AlidayuGateway.php, 配置网关名为 alidayu
