<?php
return array(

	'SHOW_ERROR_MSG'        =>  true,
	'URL_MODEL' => 2,
	'URL_CASE_INSENSITIVE'  =>  false,
	'TMPL_EXCEPTION_FILE'   => './404.html',

	//微信配置参数
	'WEIXINPAY_CONFIG'       => array(
		'APPID'              => 'wx11b9e625d235a4a6', // 微信支付APPID
		'MCHID'              => '1381160702', // 微信支付MCHID 商户收款账号
		'KEY'                => 'DC15F13D5CF25D9A388D5674EE90EC70', // 微信支付KEY
		'APPSECRET'          => '02138554ffc6e42242b99d11e02f8435', // 公众帐号secert (公众号支付专用)
		'NOTIFY_URL'         => 'http://www.lianbao100.com/Lianbao_PC/Wxpay/notify_url', // 接收支付状态的连接
	),

);