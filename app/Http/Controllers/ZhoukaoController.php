<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tools\Tools;
use DB;
class ZhoukaoController extends Controller
{
    public $tools;
    public function __construct(Tools $tools)
    {
        $this->tools = $tools;
    }
    public function login()
    {
    	return view('Zhoukao.login');
    }
    public function wechatLogin()
    {
    	$redirect_uri='http://www.zxc.com/zhou/code';
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
            return redirect('/zhou/get_user_list');
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
            return redirect('/zhou/get_user_list');
        }
    }
    public function get_user_list(Request $request)
    {
        $access_token = $this->tools->get_wechat_access_token();
        $data = file_get_contents("https://api.weixin.qq.com/cgi-bin/user/get?access_token=".$access_token."&next_openid=");
        $data = json_decode($data,1);
//        dd($data);
        $last_info = [];
        foreach($data['data']['openid'] as $k=>$v){
            $user_info = file_get_contents('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->tools->get_wechat_access_token().'&openid='.$v.'&lang=zh_CN');
            $user = json_decode($user_info,1);
//            dd($user);
            $last_info[$k]['nickname'] = $user['nickname'];
            $last_info[$k]['openid'] = $v;
        }
        return view('Zhoukao.wechat_user_list',['data'=>$last_info]);
    }
    public function push_tag_message(Request $request)
    {
        $ree=$request->all();
        dd($ree);
        return view('Zhoukao.push',['openid'=>$ree['openid_list[]']]);
    }
    //推送消息操作
    public function do_push_tag_message(Request $request)
    {
        $req = $request->all();
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token='.$this->tools->get_wechat_access_token();
        $data = [
            'filter' => [
                'is_to_all'=>false,
                'openid_list'=>$req['openid_list']
            ],
            'text'=>[
                'content'=>$req['message']
            ],
            'msgtype'=>'text'
        ];
        $re = $this->tools->curl_post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
        $result = json_decode($re,1);
        return redirect('/zhou/get_user_list');
    }
}