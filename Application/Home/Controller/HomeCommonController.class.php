<?php
namespace Home\Controller;
use Think\Controller;
class HomeCommonController extends Controller {
	// 检查用户的授权情况
	public function _initialize(){
		if (!session('?userid')){
			cookie('redirect_url','http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], 300);	// 记录用户原访问地址
			$appId = APP_ID;
			$appSecret = APP_SECRET;
			$redirectUrl = urlencode('http://'.$_SERVER['HTTP_HOST'].U('Auth/getAuth'));	// 微信授权回调地址
			$authUrl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appId&redirect_uri=$redirectUrl&response_type=code&scope=snsapi_base&state=response#wechat_redirect";
			redirect($authUrl);		// 请求微信授权
		}		

/*		// （用于上线版本维护）捕获测试人员id
		if (session('userid') == 'xxxxxx'){
			$this->debug();
		}*/
	}

	// 用于线上测试
	public function debug(){
		$this->show('你已进入debug模式');
		exit;
	}
}