<?php
header("Content-type:text/html;charset=utf-8");

//获取用户头像和用户名
function getusernamepic($id){
	$Role = D("user");
	$list = $Role->getField('id,username,avatar');
	$list = $list [$id];
	$list = "<img src='".$list['avatar']."' width='80' /><br />".$list['username'];
	return $list;
}

//根据用户id获取用户头像
function get_user_avatar($id){
	$avatar = M('User')->getFieldById($id,'avatar');
	return $avatar;
}

//获取商品图片和名称
function getGoodsInfo($id){
	$Goods = M('Goods');
	$goodsInfo = $Goods->where('id='.$id)->find();
	$info =  "<img src='".$goodsInfo['image']."' width='80' />".$goodsInfo['title'];
	return $info;
}

/**
 * 根据类型和名称判断类别是否唯一
 */
function unique_category($type,$title){
	$where['type'] = $type;
	$where['title'] = $title;
	$result = M('Category')->where($where)->find();
	if($result){
		return false;
	}else{
		return ture;
	}
}

/**
 * 输出rss
 */
function create_rss(){
	$where['created_time'] = array('EGT',strtotime(date('Y-m-d')));
	$where['created_time'] = array('ELT',time());
	$article_list = M('Article')->where($where)->order('agree desc')->limit('20')->select();
	import('Rss');
	$rss = new Rss(C('site_title'),C('site_url'),C('site_description'),C('site_logo'));
	foreach($article_list as $vo){
		$rss->AddItem($vo['title'],U('Artilce?id='.$vo['id']),$vo['description'],$vo['logo']);
	}
	$rss->Display();
}

/*发送短消息*/
function sendsms()
{
	$code = rand(100000,999999);
	session(array('code_'.$phone=>$code,'code_expire'=>900));
	$rlt = M('Sms')->sendsms($phone,$code);
	if($rlt != (int)$rlt) {
		return(array('status'=>0,'message'=>$rlt));
	}else {
		return(array('status'=>1,'message'=>'发送成功'));
	}
}

/**
 * 发送邮件
 * @param string $address
 * @param string $subject
 * @param string $message
 * @return array<br>
 * 返回格式：<br>
 * array(<br>
 * 	"error"=>0|1,//0代表出错<br>
 * 	"message"=> "出错信息"<br>
 * );
 */
function send_email($address,$title,$message){
	import("PHPMailer");
	$mail=new \PHPMailer();
	// 设置PHPMailer使用SMTP服务器发送Email
	$mail->IsSMTP();
	$mail->IsHTML(true);
	// 设置邮件的字符编码，若不指定，则为'UTF-8'
	$mail->CharSet='UTF-8';
	// 添加收件人地址，可以多次使用来添加多个收件人
	$mail->AddAddress($address);
	// 设置邮件正文
	$mail->Body=$message;
	// 设置邮件头的From字段。
	$mail->From=C('mail_address');
	// 设置发件人名字
	$mail->FromName=C('mail_fromname');;
	// 设置邮件标题
	$mail->Subject=$title;
	// 设置SMTP服务器。
	$mail->Host=C('mail_smtp');
	// 设置SMTP服务器端口。
	$port=C('mail_smtp_port');
	$mail->Port=empty($port)?"25":$port;
	// 设置为"需要验证"
	$mail->SMTPAuth=true;
	// 设置用户名和密码。
	$mail->Username=C('mail_loginname');
	$mail->Password=C('mail_password');
	//以下是支持QQ个人邮箱的
	$mail->Port = '465';
	$mail->SMTPSecure = 'ssl';
	// 发送邮件。
	if(!$mail->Send()) {
		$mailerror=$mail->ErrorInfo;
		return array("error"=>1,"message"=>$mailerror);
	}else{
		return array("error"=>0,"message"=>"success");
	}
}

/*生成随机6位数*/
function rand_six_num(){
	return mt_rand(100000,999999);
}

/*密码保存16位*/

function substr_pwd($pwd){
	$pwd = md5(md5($pwd));
	if(strlen($pwd) == 32) {
		return substr($pwd, 8, 16);
	}else{
		return $pwd;
	}

}

/*取前面几位*/
function substr_str($str){
		return substr($str,0,6);
	}

//开发调试
function show_bug($msg){
	echo "<pre style='color:red'>";
	var_dump($msg);
	echo "</pre>";
	exit();
}

/** 检测是否有敏感词*/
function check_word($content) {
	$word = D('badword')->field('badword')->select();
	$names = array_column($word, 'badword');
	foreach ($names as $key => $value) {
		if(strpos($content, $value) > -1) {
			return true;//包含敏感词
			exit();
		}
	}
	return false;
}

/** 检测是否有特殊用户名*/
function check_nickname($content) {
	$word = str_replace('，',',',C('banned_user'));
	$word = explode(',',$word);
	foreach ($word as $key => $value) {
		if(stripos($content, $value) > -1) {
			return true;//包含特殊用户名
			exit();
		}
	}
	return false;
}

// 获取广告调用地址
function getadsurl($str,$charset="utf-8"){
	return '<script type="text/javascript" src="'.C('site_url').C('ads_file').'/'.$str.'.js" charset="'.$charset.'"></script>';
}

/**
*验证码统一调用函数
**/
function captcha($cap){
	$checkcode_type = C('checkcode_type');
	if($checkcode_type == 1){
		//普通验证
		$code = trim($cap['code']);
		$Verify = new \Think\Verify();
		if(!$Verify->check($code)){
			return false;
		}
	}elseif($checkcode_type == 2){
		//极验证
		$data['geetest_challenge'] = $cap['geetest_challenge'];
		$data['geetest_validate'] =  $cap['geetest_validate'];
		$data['geetest_seccode'] = $cap['geetest_seccode'];
		if (!geetest_check_verify($data)) {
			return false;
		}
	}
}

/**
 * geetest检测验证码
 */
function geetest_check_verify($data){
	$geetest_id = C('GEETEST_ID');
	$geetest_key = C('GEETEST_KEY');
	$geetest=new \Geetestlib($geetest_id,$geetest_key);
	$user_id=$_SESSION['geetest']['user_id'];
	if ($_SESSION['geetest']['gtserver']==1) {
		$result=$geetest->success_validate($data['geetest_challenge'], $data['geetest_validate'], $data['geetest_seccode'], $user_id);
		if ($result) {
			return true;
		} else{
			return false;
		}
	}else{
		if ($geetest->fail_validate($data['geetest_challenge'],$data['geetest_validate'],$data['geetest_seccode'])) {
			return true;
		}else{
			return false;
		}
	}
}

//MYSQL版本低于5.5时  启用自定义array_column涵数
if(!function_exists('array_column')){
	function array_column($input, $columnKey, $indexKey = NULL){
		$columnKeyIsNumber = (is_numeric($columnKey)) ? TRUE : FALSE;
		$indexKeyIsNull = (is_null($indexKey)) ? TRUE : FALSE;
		$indexKeyIsNumber = (is_numeric($indexKey)) ? TRUE : FALSE;
		$result = array();
		foreach ((array)$input AS $key => $row){
			if ($columnKeyIsNumber){
				$tmp = array_slice($row, $columnKey, 1);
				$tmp = (is_array($tmp) && !empty($tmp)) ? current($tmp) : NULL;
			}else{
				$tmp = isset($row[$columnKey]) ? $row[$columnKey] : NULL;
			}
			if(!$indexKeyIsNull){
				if ($indexKeyIsNumber){
					$key = array_slice($row, $indexKey, 1);
					$key = (is_array($key) && ! empty($key)) ? current($key) : NULL;
					$key = is_null($key) ? 0 : $key;
				}else{
					$key = isset($row[$indexKey]) ? $row[$indexKey] : 0;
				}
			}
			$result[$key] = $tmp;
		}
		return $result;
	}
}

// 重写动态路径
function UU($model,$params = array()){
	//rewrite重写
	if(C('rewrite_type')){
		$model = strtolower($model);
		$reurl = C($model);
		//伪静态规则设置正确
		if($reurl){
			if(isset($params['p'])) {
				$reurl = str_replace(array('$currpath','$page'),array($reurl,$params['p']),C('page'));
			}
			return '/'.$reurl.'.'.C('url_html_suffix');
		}
		$reurl = str_replace('Home/', '' ,U($model,$params));
		$reurl = str_replace('Wap/', '' ,$reurl);
		return $reurl;
	}
	return U($model,$params);
}

function deleteHtml($str){
	$str=preg_replace("/\s+/", " ", $str); //过滤多余回车
	$str=preg_replace("/<[ ]+/si","<",$str); //过滤<__("<"号后面带空格)
	$str=preg_replace("/<\!--.*?-->/si","",$str); //注释
	$str=preg_replace("/<(\!.*?)>/si","",$str); //过滤DOCTYPE
	$str=preg_replace("/<(\/?html.*?)>/si","",$str); //过滤html标签
	$str=preg_replace("/<(\/?head.*?)>/si","",$str); //过滤head标签
	$str=preg_replace("/<(\/?meta.*?)>/si","",$str); //过滤meta标签
	$str=preg_replace("/<(\/?body.*?)>/si","",$str); //过滤body标签
	$str=preg_replace("/<(\/?link.*?)>/si","",$str); //过滤link标签
	$str=preg_replace("/<(\/?form.*?)>/si","",$str); //过滤form标签
	$str=preg_replace("/cookie/si","COOKIE",$str); //过滤COOKIE标签
	$str=preg_replace("/<(applet.*?)>(.*?)<(\/applet.*?)>/si","",$str); //过滤applet标签
	$str=preg_replace("/<(\/?applet.*?)>/si","",$str); //过滤applet标签
	$str=preg_replace("/<(style.*?)>(.*?)<(\/style.*?)>/si","",$str); //过滤style标签
	$str=preg_replace("/<(\/?style.*?)>/si","",$str); //过滤style标签
	$str=preg_replace("/<(title.*?)>(.*?)<(\/title.*?)>/si","",$str); //过滤title标签
	$str=preg_replace("/<(\/?title.*?)>/si","",$str); //过滤title标签
	$str=preg_replace("/<(object.*?)>(.*?)<(\/object.*?)>/si","",$str); //过滤object标签
	$str=preg_replace("/<(\/?objec.*?)>/si","",$str); //过滤object标签
	$str=preg_replace("/<(noframes.*?)>(.*?)<(\/noframes.*?)>/si","",$str); //过滤noframes标签
	$str=preg_replace("/<(\/?noframes.*?)>/si","",$str); //过滤noframes标签
	$str=preg_replace("/<(i?frame.*?)>(.*?)<(\/i?frame.*?)>/si","",$str); //过滤frame标签
	$str=preg_replace("/<(\/?i?frame.*?)>/si","",$str); //过滤frame标签
	$str=preg_replace("/<(script.*?)>(.*?)<(\/script.*?)>/si","",$str); //过滤script标签
	$str=preg_replace("/<(\/?script.*?)>/si","",$str); //过滤script标签
	$str=preg_replace("/javascript/si","Javascript",$str); //过滤script标签
	$str=preg_replace("/vbscript/si","Vbscript",$str); //过滤script标签
	$str=preg_replace("/on([a-z]+)\s*=/si","On\\1=",$str); //过滤script标签
	$str=preg_replace("/&#/si","&＃",$str); //过滤script标签，如javAsCript:alert(
	return trim($str);
}

/*时间戳格式化函数*/
function timestampFormat($time, $format=''){
    if($format){
        return date($format,$time);
    }
    $s = date('Y-m-d H:i:s',$time);
    $sdaytime = strtotime(date("Y-m-d"),time());
    $stime = time() - $time;
    if($time >= $sdaytime){ //当天
        if($stime > 3600) {
            return intval($stime / 3600).'小时前';
        } elseif($stime > 1800) {
            return '">半小时前';
        } elseif($stime > 60) {
            return intval($stime / 60).'分钟前';
        } elseif($stime > 0) {
            return $stime.'秒前';
        } elseif($stime == 0) {
            return '">刚刚';
        } else {
            return '';
        }
    }else if(($days = intval($stime / 86400)) >= 0 && $days < 7){
        if($days == 0) {
            return '">昨天&nbsp;'.date("H:i", $time).'';
        } elseif($days == 1) {
            return '">前天&nbsp;'.date("H:i", $time).'';
        } else {
            return ($days + 1).'天前';
        }
    }else if(empty($time)){
        return '">0';
    }else{
        return '';
    }
}


/*数组键值过滤。通常用户过滤不允许前台修改的表字段*/
function check_fields($data, $fields=null)
{
    if($fields === null){
        $fields = $this->_allow_fields;
    }

    if(!is_array($fields)){
        $fields = explode(',', $fields);
    }
    foreach((array)$data as $k=>$v){
        if(!in_array($k, $fields)){
            unset($data[$k]);
        }
    }
    return $data;
}


/*用于抛到模板中变量的字段过滤*/
function filter_fields($fields,$row,$type=1)
{
    if(!is_array($fields)){
        $fields = explode(',', $fields);
    }
    foreach((array)$row as $k=>$v){
        if($type == 1){//如果不在fields里，则unset
            if(!in_array($k, $fields)){
                unset($row[$k]);
            }
        }else{
           if(in_array($k, $fields)){
                unset($row[$k]);
           }
        }
    }
    return $row;
}


/*验证手机号是否正确*/
function is_mobile($mobile) {
    if (!is_numeric($mobile)) {
        return false;
    }
    if(preg_match("/^1[3-8]\d{9}$/", $mobile)){
        return $mobile;
    }
    return false;
}

//	检验ids
function check_ids($ids)	{
    if(is_array($ids)){
        $ids = implode(',', $ids);
    }
    if(preg_match("/^(\d+|(\d([\d,]+?)\d))$/",$ids)){
        return $ids;
    }
    return false;
}

// 获取配置
function get_cfg($k,$fields)
{
	if (!empty($k)) {
		$config = D('Systemconfig')->find($k);
		$cfg = unserialize($config['v']);
		if($cfg) {
			$items = array();
			if(!is_array($fields)){
		        $fields = explode(',', $fields);
		    }
		    foreach($cfg as $key=>$val) {
		    	if(in_array($key,$fields)) {
		    		$items[$key] = $val;
		    	}
		    }
		    return $items;
		}
	}
}

//检查用户是否登录，未登录返回false,登录返回用户信息
function check_login(){
	if(session('user.id')) {
		return true;
	}
	if(!cookie('user_id') or !cookie('password'))return false;
	$where['id'] = cookie('user_id');
	$where['password'] = cookie('password');
	$user = M('User')->where($where)->find();
	if(!$user){
		return false;
	}else{
		//判断用户当前ip地址是否改变
		if(get_client_ip() != $user['last_login_ip']){
			return false;
		}
		$info = filter_fields('id,username,avatar,sex',$user);
		session('user',$info);
		return true;
	}
}


//	日志写到文件
function log_result($log) {
    $fname = 'payment-alipay-'.date('Ymd');
    if(is_array($log) || is_object($log)){
		$log = var_export($log,true);
	}
	$log =	'<?php exit("Access denied");?>'."\n+-----------------------------------------------------+\n".
			"Time:".date("Y-m-d H:i:s")."\nLog:{$log}\n\n";
	$fp = @fopen(SITE_PATH . 'Logs/'."{$fname}.php","a");
	@fwrite($fp,$log);
	@fclose($fp);
}

//拼接分页
function join_page($func,$params,$num,$p=1){
	$content ='<ul class="am-pagination am-pagination-centered">';
    if($p>1){$content.='<li><a href="page_up()">上一页</a></li>';}
    for($i=1;$i<$num;$i++){
    	$params['p'] = $i;
    	$p_url = UU($func,$params);
    	if($i == $p){$class = 'current';}
    	$content.='<li class='.$class.'><a href="javascript:;" data-url="'.$p_url.'">'.$i.'</a></li>';
    }
    if($p<$num){$content.='<li><a href="page_down()">下一页</a></li>';}
    $content .= '</ul>';
    return $content;
}