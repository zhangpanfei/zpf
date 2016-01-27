<?php
/**
  * wechat php test
  */

// 定义TOKEN
define ( "TOKEN", "itcast" );
// 实例化$wechatObj对象
$wechatObj = new wechatCallbackapiTest ();
// $wechatObj->valid();
$wechatObj->responseMsg ();
class wechatCallbackapiTest {
	// 验证函数
	public function valid() {
		$echoStr = $_GET ["echostr"];
		
		// valid signature , option
		if ($this->checkSignature ()) {
			echo $echoStr;
			exit ();
		}
	}
	
	// 响应信息
	public function responseMsg() {
		// get post data, May be due to the different environments
		// $GLOBALS["HTTP_RAW_POST_DATA"]功能与$_POST类似用于接收HTTP POST数据，两者不同在于GLOBALS可以接收xml数据
		$postStr = $GLOBALS ["HTTP_RAW_POST_DATA"];
		
		// extract post data
		if (! empty ( $postStr )) {
			/*
			 * libxml_disable_entity_loader is to prevent XML eXternal Entity Injection, the best way is to check the validity of xml by yourself
			 */
			// 解析xml时，不解析entity实体(防止产生文件泄露)
			libxml_disable_entity_loader ( true );
			// simplexml_load_string载入xml到字符串
			$postObj = simplexml_load_string ( $postStr, 'SimpleXMLElement', LIBXML_NOCDATA );
			// 用户的微信端（手机端）
			$fromUsername = $postObj->FromUserName;
			// 微信公众平台
			$toUsername = $postObj->ToUserName;
			// 定义msgType用于判断接收到的信息类型
			$msgType = $postObj->MsgType;
			// 定义event接收event事件
			$event = $postObj->Event;
			$eventKey = $postObj->EventKey;
			// 获取经纬度
			$longitude = $postObj->Location_Y;
			$latitude = $postObj->Location_X;
			// 接收用户发送过来的数据，存储$keyword里
			$keyword = trim ( $postObj->Content );
			// 时间戳
			$time = time ();
			// 定义文本消息xml模板
			$textTpl = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            <FuncFlag>0</FuncFlag>
                            </xml>";
			$musicTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Music>
								<Title><![CDATA[%s]]></Title>
								<Description><![CDATA[%s]]></Description>
								<MusicUrl><![CDATA[%s]]></MusicUrl>
								<HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
							</Music>
						</xml>";
			$newsTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<ArticleCount>%s</ArticleCount>
							%s
						</xml>";
			if ($msgType == 'text') {
				// 判断用户传递过来文本消息是否为空
				if (! empty ( $keyword )) {
					header('Content-type:text/html;charset=utf-8');
					$url = "http://www.xiaohuangji.com/ajax.php";
					//1、初始化curl
					$ch = curl_init();
					//2、设置参数,参数1初始化$ch,参数2设置常量,参数3常量的值
					//设置请求url网址
					curl_setopt($ch,CURLOPT_URL,$url);
					//捕获url响应信息不输出
					curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
					//设置请求头信息
					curl_setopt($ch,CURLOPT_HEADER,0);
					//设置传输post数组
					$data = array(
							'para'=>$keyword
					);
					//设置开启POST请求
					curl_setopt($ch,CURLOPT_POST,1);
					//传输参数值
					curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
					//3、执行curl
					$contentStr = curl_exec($ch);
					//4、关闭句柄
					curl_close($ch);
					
					$msgType = "text";
					$resultStr = sprintf ( $textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr );
					echo $resultStr;
				} else {
					echo "Input something...";
				}
			} else if ($msgType == 'image') {
				// 定义返回消息类型（text文本）
				$msgType = "text";
				// 返回响应回复
				$contentStr = "您发送的图片，不会包含敏感信息吧！";
				// sprintf()函数，把字符串按照指定模式进行格式化%s
				// 有两个重要参数(格式化字符串，格式化变量)
				$resultStr = sprintf ( $textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr );
				echo $resultStr;
			} else if ($msgType == 'voice') {
				// 定义返回消息类型（text文本）
				$msgType = "text";
				// 返回响应回复
				$contentStr = "您的声音很动听！";
				// sprintf()函数，把字符串按照指定模式进行格式化%s
				// 有两个重要参数(格式化字符串，格式化变量)
				$resultStr = sprintf ( $textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr );
				echo $resultStr;
			} else if ($msgType == 'video') {
				// 定义返回消息类型（text文本）
				$msgType = "text";
				// 返回响应回复
				$contentStr = "您发送视频不会是大片吧！";
				// sprintf()函数，把字符串按照指定模式进行格式化%s
				// 有两个重要参数(格式化字符串，格式化变量)
				$resultStr = sprintf ( $textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr );
				echo $resultStr;
			} else if ($msgType == 'location') {
				/*
				//定义API接口地址
				$url = "http://api.map.baidu.com/telematics/v3/reverseGeocoding?location={$longitude},{$latitude}&coord_type=gcj02&ak=DFeb602c2287c0365ddc5776ee79af22&output=json";
				//获取数据
				$str = file_get_contents($url);
				//解析json
				$json = json_decode($str);
				*/
				//车陂店（113.374915,23.13095）
				$url = "http://api.map.baidu.com/telematics/v3/distance?waypoints=113.374915,23.13095;{$longitude},{$latitude}&ak=DFeb602c2287c0365ddc5776ee79af22&output=json";
				$str1 = file_get_contents($url);
				$json1 = json_decode($str1);
				$dis1 = $json1->results[0];
				
				//珠江新城店（113.327628,23.125367）
				$url = "http://api.map.baidu.com/telematics/v3/distance?waypoints=113.327628,23.125367;{$longitude},{$latitude}&ak=DFeb602c2287c0365ddc5776ee79af22&output=json";
				$str2 = file_get_contents($url);
				$json2 = json_decode($str2);
				$dis2 = $json2->results[0];
				
				//动物园店（113.313759,23.140671）
				$url = "http://api.map.baidu.com/telematics/v3/distance?waypoints=113.313759,23.140671;{$longitude},{$latitude}&ak=DFeb602c2287c0365ddc5776ee79af22&output=json";
				$str3 = file_get_contents($url);
				$json3 = json_decode($str3);
				$dis3 = $json3->results[0];
				
				//获取最小值
				$dis = min($dis1,$dis2,$dis3);
				if($dis==$dis1) {
					$contentStr = "距离您最近的店铺是车陂店 http://map.baidu.com/";
				} elseif($dis==$dis2) {
					$contentStr = "距离您最近的店铺是珠江新城店";
				} elseif($dis==$dis3) {
					$contentStr = "距离您最近的店铺是动物园店";
				}
				
				// 定义返回消息类型（text文本）
				$msgType = "text";
				// 返回响应回复
				
				// sprintf()函数，把字符串按照指定模式进行格式化%s
				// 有两个重要参数(格式化字符串，格式化变量)
				$resultStr = sprintf ( $textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr );
				echo $resultStr;
			} else if ($msgType == 'link') {
				// 定义返回消息类型（text文本）
				$msgType = "text";
				// 返回响应回复
				$contentStr = "您发送链接不会包含病毒吧！";
				// sprintf()函数，把字符串按照指定模式进行格式化%s
				// 有两个重要参数(格式化字符串，格式化变量)
				$resultStr = sprintf ( $textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr );
				echo $resultStr;
			} else if($msgType == 'event' && $event== 'CLICK') {
				if($eventKey == 'intro') {
					include 'init.php';
					$sql = 'select title,description from hnthdl_sgpage where aid=1';
					$res = mysql_query($sql);
					$row = mysql_fetch_array($res);
					$msgType = "news";
					$count = 1;
					$str = "<Articles>";
					for($i = 1; $i <= $count; $i ++) {
						$str .= "<item>
						<Title><![CDATA[{$row['title']}]]></Title>
						<Description><![CDATA[{$row['description']}]]></Description>
						<PicUrl><![CDATA[http://www.yyzljg.com/wechat/images/$i.jpg]]></PicUrl>
						<Url><![CDATA[http://itcast888.duapp.com/a/aboutus.html]]></Url>
						</item>";
					}
					$str .= "</Articles>";
					$resultStr = sprintf ( $newsTpl, $fromUsername, $toUsername, $time, $msgType, $count, $str );
					echo $resultStr;
				} elseif($eventKey == 'product') {
					include 'init.php';
					$msgType = "news";
					
					$sql = 'select count(*) as num from hnthdl_archives where typeid=15';
					$res = mysql_query($sql);
					$row = mysql_fetch_assoc($res);
					$count = $row['num'];
					
					if($count>10) {
						$count = 10;
					}
					
					$sql = "select title,litpic,description from hnthdl_archives where typeid=15 order by id desc limit 0,$count";
					$res = mysql_query($sql);
					$str = "<Articles>";
					while($row = mysql_fetch_assoc($res)) {
						$str .= "<item>
						<Title><![CDATA[{$row['title']}]]></Title>
						<Description><![CDATA[{$row['description']}]]></Description>
						<PicUrl><![CDATA[http://itcast888.duapp.com/{$row['litpic']}]]></PicUrl>
						<Url><![CDATA[http://itcast888.duapp.com/a/chanpinzhanshi/]]></Url>
						</item>";
					}
					$str .= "</Articles>";
					$resultStr = sprintf ( $newsTpl, $fromUsername, $toUsername, $time, $msgType, $count, $str );
					echo $resultStr;
				}
			} else if ($msgType == 'event' && $event == 'subscribe') {
				// 定义返回消息类型（text文本）
				$msgType = "text";
				// 返回响应回复
				$contentStr = "感谢您的关注？回复\n【1】公司简介\n【2】PHP资讯\n【3】JAVA资讯\n【4】Net资讯\n【5】联系我们";
				// sprintf()函数，把字符串按照指定模式进行格式化%s
				// 有两个重要参数(格式化字符串，格式化变量)
				$resultStr = sprintf ( $textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr );
				echo $resultStr;
			}
		} else {
			echo "";
			exit ();
		}
	}
	
	// 校检签名
	private function checkSignature() {
		// you must define TOKEN by yourself
		if (! defined ( "TOKEN" )) {
			throw new Exception ( 'TOKEN is not defined!' );
		}
		
		// 接收参数
		$signature = $_GET ["signature"];
		$timestamp = $_GET ["timestamp"];
		$nonce = $_GET ["nonce"];
		
		$token = TOKEN;
		$tmpArr = array (
				$token,
				$timestamp,
				$nonce 
		);
		// use SORT_STRING rule
		// 排序
		sort ( $tmpArr, SORT_STRING );
		// 拼接成字符串
		$tmpStr = implode ( $tmpArr );
		// 加密
		$tmpStr = sha1 ( $tmpStr );
		
		if ($tmpStr == $signature) {
			return true;
		} else {
			return false;
		}
	}
}

?>