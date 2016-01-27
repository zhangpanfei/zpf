<?php
$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=lXn_v9QPQCFDN0urZd5kqmjju7E5Ksiy3jRlF3Efgx54j6oNJWZQCWDx-T2HOm7uFt7sQiCAob9QY137ozWdcQ";
// 1、初始化
$ch = curl_init ();
// 2、设置参数
curl_setopt ( $ch, CURLOPT_URL, $url );
curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
curl_setopt ( $ch, CURLOPT_HEADER, 0 );
//禁止SSL校检操作
curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, false );
$data = '{
     "button":[
     {	
          "type":"view",
          "name":"微官网",
          "url":"http://itcast888.duapp.com/"
      },
      {
		  "type":"click",
          "name":"关于我们",
          "key":"intro"
       },
	   {
		  "type":"click",
          "name":"产品展示",
          "key":"product"
	   }]
 }';
curl_setopt ( $ch, CURLOPT_POST, 1 );
curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
// 3、执行curl
$output = curl_exec ( $ch );
if ($output === false) {
	echo 'error:' . curl_error ( $ch );
}
// 4、关闭句柄
curl_close ( $ch );
echo $output;
?>