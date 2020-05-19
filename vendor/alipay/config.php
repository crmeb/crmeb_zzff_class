<?php
$config = array (	
		//应用ID,您的APPID。
		'app_id' => "2019021863275112",

		//商户私钥，您的原始格式RSA私钥
		'merchant_private_key' => "MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCjxAnTSLG4hxRk0xzq5OgZzvWuakQRl5UF3Q6I+43fur6UHGuZoNiJUHLiMHVkero4DmyzNHh5sFi24kEGKbAGRsoHPdi70Jet+x3nLyQPtM/kOrE7mfnwLc1BaT7AM4HDCJwfErGzGkaXycVs06Pwj1wCuZobiKkwUdktgVeQITWhCCfUOyltBPs3LgR7jWG2WV1vkuYCKlq/+Xo3N6l/2VYeAV0WIQvUKDHmH/xPxS4/jw3E0LpCA0ZsIIw/1h99WpQao3xREP9fx7r/IwclRmfeuuXFv3Ao99ONbJgSuUvUe2RGLSogRUsfmStJcrcbmkoN51l2O5EATlhP02t/AgMBAAECggEAGhmrazYhxQQaVvtil3lGGJ7ofxTGpnsfMCEMKP7WnLq5lwd0irac7D2bIbuRaiM5PKfn4SPSs6pMaVFkBaRtPoLXvhG4Ui6rjfoAyRC1UZ+CpqBIEPS4ZfQWy38HlkBfRQNBRp6HHcFpUNXCllagwT1H79M+4XaFaIMS9vKvTvQKRgyL7ajEBXPXXV3aiyU+9k32TyhFyxYYzKbb/W8oisAi3clTcwfeLm1dG8IY6dbeX8AA9hLpxGzcIKRnMtMZ+eUo1dfayz2rvM4VUi29FFUxhXzXo58hywcXGlqdM/07IpkczjYgeYcmH1eFddUsCegUAf2snOmmHF+tMy61+QKBgQDnIQEkbC3r+FedrPQ7jf6vS92bqQDCFAx4aYDBt71Lj2Ly9q+G6tY2gtjebGijtlE5dMUJb0aQ6fid7nsz+ECNXoCH1s7nDyLXm5iaIsBjz+zvkrAROJXf4AN1mjP19Q2lW5WKp/A86DXVvuPa2im5qWE/Svy1z2MNeF+c5PSPvQKBgQC1Y1sa+rkbewEFLb8kciIxBIbFZm5VW076qD45xwqna6d9KO69VRjzmpqbCitOzBg2abkQRK4raz3YOQG5dBxBJEocdy9f37XCh/gTyn31qJOVyiJIWL4UfAKnGXy7Jk/JR5p/nKWEAkWI4AsA8TfWLihUKke8ROyvNMDJnKBt6wKBgA+3BsTbNiQdNpfZ8qE5/l8c4Wx8CTko89AcHE0PmSdSEIBElZquzPHX47b72AGJm+w0LFF/52RjBCx40peuAXWbP1H9IQRE2zGdurqAMmSW9p0zdBU3q7kVGicc3PuUNeFQYNKUqJj7kO1/lS6ENuIQyjPrfVM/TpTT4mxghOBhAoGBAJvb0LfhAleLnPfPNDPj4l9OLWPHPT0Y5Udac4V/8zaiuVtBZcrIKVWA3iIkXcwqcQ+oTn2dFi92ycBWU5YAIBsVTJ0jCFEQUATkgpS25P7GKHKIKmcR4dqGKF1e+B9zrxEb9rR6bHp+3TLcmHfpzXm7VankXpl2fA2cfZ9/dxabAoGAG9HmM+GPKbcOAToLnBlIpenEob7b5lonVLgJd5+RoIbpSjahWBDNldZerGLaZrAyO7cJljE6Oh5F9nsRSPu81Uf+lBJpTDZDdLKeFw+Mbb8Bn05jVmmBC302xaH+/rpfzmxSb1HuBzezMNjVAcv/nmMIorC7ZdP+J0JDhPglu9I=",
		
		//异步通知地址
		'notify_url' => "http://dounixue.crmeb.net/alipay/alipay_success_notify.html",
		
		//同步跳转
		'return_url' => "http://dounixue.crmeb.net/alipay/alipay_success_synchro.html",

		//编码格式
		'charset' => "UTF-8",

		//签名方式
		'sign_type'=>"RSA2",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

		//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAlOTlvHG5FYvcaoNlQAS4xqmQeuw4JVdr4tNrf4AhXhlPTiE/mH4H4G5Kg8l9j41eNuOwJ3XDROhX2DvAc3AF1KtZQTHcJocd2bs7jOsEWr6k1tsvDvQAU+tfJV6LNy2NytX9AW+UsP9EUI/Jme/97IknYHoxer2a1XMEW+yU4RwtT+ZVHnnXsouRLjBtUcAVmf4S943aU/ExIWKtz/kJRnjgihTvW2LsMV4KtNUJtH0EvuphvlwhyYf6rFb7xnVDz1Ukh8cQ2lRZdBQiuhGcGzpa9QYKsumAgQ21LK1XZGiFY+UwamyhjQ1wSn9apgs2Wxk0HaIhxCuj57DkXIEO+QIDAQAB",

);