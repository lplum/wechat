<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Tools\Tools;
class wechatLoginController extends Controller
{
    public $tools;
    public function __construct(Tools $tools)
    {
        $this->tools = $tools;
    }
    public function login()
    {
    	return view('Wechat.login');
    }
    public function wechatLogin()
    {
    	$redirect_uri='http://www.zxc.com/wechat/code';
        $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".env('WECHAT_APPID')."&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
        header('Location:'.$url);
    }
    public function code(Request $request)
    {
        $req = $request->all();
        $result = file_get_contents('https://api.weixin.qq.com/sns/oauth2/access_token?appid='.env('WECHAT_APPID').'&secret='.env('WECHAT_APPSECRET').'&code='.$req['code'].'&grant_type=authorization_code');
        $re = json_decode($result,1);
        // dd($re);
        $res = file_get_contents('https://api.weixin.qq.com/sns/oauth2/refresh_token?appid='.env('WECHAT_APPID').'&grant_type=refresh_token&refresh_token='.$re['refresh_token']);
        $ree = json_decode($res,1);
        // dd($ree);
        $user_info = file_get_contents('https://api.weixin.qq.com/sns/userinfo?access_token='.$ree['access_token'].'&openid='.$ree['openid'].'&lang=zh_CN');
        $user=json_decode($user_info,1);
        // dd($user);
        $openid=$ree['openid'];
        $wechat_info = DB::table('wechat_user')->where(['openid'=>$openid])->first();
        if(!empty($wechat_info)){
//            存在
            $request->session()->put('uid',$wechat_info->uid);
//            echo "ok";
            return redirect('/wechat/get_user_list');
        }else{
//            不存在
//            插入user表数据一条
            DB::beginTransaction();//打开事务
            $uid = DB::table('wuser')->insertGetId([
                'name'=>$user['nickname'],
                'password'=>'',
                'reg_time'=>time()
            ]);
            $insert_result = DB::table('wechat_user')->insert([
                'uid'=>$uid,
                'openid'=>$openid
            ]);
//            登录操作
            $request->session()->put('uid',$wechat_info['uid']);
//            echo "ok";
            return redirect('/wechat/get_user_list');
        }
    }
}
