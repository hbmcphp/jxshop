<?php
$config = array (	
		//应用ID,您的APPID。
		'app_id' => "2016080800191992",

		//商户私钥
		'merchant_private_key' => "MIIEogIBAAKCAQEAk0iTygqhO0eY8GwPi/a2xghFwn35vu8Og6KYjtrKY5icovrPz4oiNJIy8ab5COAAm2URAkEPt79p612YMF51EGSb4aKY5LxvntScQAgtmF6i8HIuhPpMOTSqP7gm2MQTHv3+jbUnZTkne2TwCBLnCpjMoeMFODIViB0SafBNcXeJnOhL+0/sGz9P9YDstGvw26BzWvonL/SbY3hZRcjZGevpJzZb+EtPmEq/Hdy5XI6pUAaisnY0ZcPWLlqUG8EZqFYTrNRGQZJONGZ/sOeYotWo5PgU9cQ83XsWc61Jyb58+VDVXsvIHXNes1iT8vB+yodlxqj5lmYrCqpGCAIqQwIDAQABAoIBAAj28O/qR7gI+pkfqq6VPLi/Bif5bPsfJ94BRpHjZVA8mMQeyglS2hQjFfC5fgz5PXA37Zoaha9kejj2QG4pQazWwtowAGhjw9OCE2SeWtAcaoSCHqy/Y2ZT+0zXk528CYHoadcMQLKbkPikwytqox90/3eXDtlFyyO2yUkjdENEsLslzlV1RP1TOUudsH7IeLPSksZASfT94YH8UFyPkIYJncXFCRtrAUDYZhwlkwk70DWHZtH7j0RZVc1yJWAGd4uvPDTQxOi1RXnC/NhiJmfuXedLSrjJoEVbAmtz0Z2c2eM3dGysKTlfPg7RbkXILxEu36MGSNJI4Zsz/NSnVJkCgYEAw2Nd/+yQ+QGo+csYdH7ovRWjbALSHvEqf6P1oy/ucpT9GqWnJDDxJykhlHAT9zp66n184KkeSh38wV7xUL1c9DK6kPKxL9X5++b+jHnHoqvqJRfwRnKUosR2GzJ8oSNUgYEg9owChoKgsBkSaKlNxxJ4wm6PL1o68CyZBDd3GpUCgYEAwPkCJdsLjC/XwbxYrM2tVcSffDiT1dlFzwBnitak3zJUJ000KMvmyisn80Jv7STIPtUhUgYcNwqULfs/PTJgUsIescPkuchyYaNv3nZnNdCfBu9qozAu9Ngu5zOynJ8RYvtXfYYOxBsEhQMr2dWC1OVOLzD1SI/fw/FpHM7M03cCgYBuzritfk0NWpqo5zw8PQjiyxW+GK9vatfuQV76KCGzi7kq1o0+oh7xVYs8sn8wM1MoDvi2NFMAN5dzVtPGNU41E1vo/insdg5qhKsRobLNF3AXj0btvOd8k6xjajNx8yZmt4OpLufzvrtAg0eEYZfBcUFZCvhbI3HPaYtx7761LQKBgBujDgWx62N2bqYWfXSyOELyWA0IZVPZPEA7RJkDyLUWlirSyceV/EW0DJKwTytdezhUeeMvcNcJMtOrChGPp2/y0UaQUa+x33/QUnM/7eQtLrlljJY2jmMOmxkNtGTt+yV77bnSe0bmv6z1GVCnXYd0F9B2aIqt4FRFvNIdZARdAoGAVSiLD2n0EnPFb0/07SFw01UVJnSmBBTaaXnaL/6smo7L54Ym0l9N0nwwLyQ1pWZbXVW5qD1f6IbFRdufU5H7J2wBHjERhRV1kOBhs31rKstaRR5Vk3ESeQ/A+zWWlkQPqmwSQOpGHnEGUNg/RrHJfOfZ+nIaCm4ssf5pOnfJJG0=",
		
		//异步通知地址
		'notify_url' => "http://外网可访问网关地址/alipay.trade.page.pay-PHP-UTF-8/notify_url.php",
		
		//同步跳转
		'return_url' => "http://jxshop.com/Order/returnUrl.html",

		//编码格式
		'charset' => "UTF-8",

		//签名方式
		'sign_type'=>"RSA2",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipaydev.com/gateway.do",

		//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyIrNAbhKky9aJqKmdzZqpUT+cEg29uB/j/80OkZRLeDOKtg6FwrtH6LHj3GNqLpHlxFUQFN8zogtbPnVwH+rPPwaCYpPhVcFw6xv0/SlScz8qxXRcoCb43V4/nOXqc4cx9fEA/sldQeuS1TlPYWS6YyDlLm/CxR7LNTO3kqqVeA8Eu8qB7NvShfURQkeE6hW/pmczvzWwNFLlQ75WERCSRv1F4et54vxuaJnYfoSW6jbHW5TmfUnvpCCVX0U7JZFnydRosxw3SisnIZwZPueCPpTtAqfiVa7hOen+Y6n8a9mkt1oLQczeHkjA/2g9LE2Q6BREV6Qc+dfg/NBWm0wBQIDAQAB",
);