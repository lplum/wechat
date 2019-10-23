<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use DB;
use App\Tools\Tools;
use App\Model\User;
use Illuminate\Support\Facades\Storage;
class wechatController extends Controller
{
    public $tools;
    public function __construct(Tools $tools)
    {
        $this->tools = $tools;
    }
    public function wechat_list()
    {
        $user_info = User::get();
        return view('Wechat.wechatList',['user_info'=>$user_info]);
    }
    public function create_qrcode(Request $request)
    {
        $req = $request->all();
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$this->tools->get_wechat_access_token();
        //{"expire_seconds": 604800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": 123}}}
        $data = [
            'expire_seconds'=> 30 * 24 * 3600,
            'action_name'=>'QR_SCENE',
            'action_info'=>[
                'scene'=>[
                    'scene_id'=>$req['uid']
                ]
            ]
        ];
        $re = $this->tools->curl_post($url,json_encode($data));
        $result = json_decode($re,1);
        $qrcode_url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$result['ticket'];
        $qrcode_source = $this->tools->curl_get($qrcode_url);
        $qrcode_name = $req['uid'].rand(10000,99999).'.jpg';
        Storage::put('wechat/qrcode/'.$qrcode_name, $qrcode_source);
        User::where(['id'=>$req['uid']])->update([
            'qrcode_url'=>'/storage/wechat/qrcode/'.$qrcode_name
        ]);
        return redirect('/wechat/wechat_list');
    }
    /**
     * 调用频次清0
     */
    public function  clear_api(){
        $url = 'https://api.weixin.qq.com/cgi-bin/clear_quota?access_token='.$this->get_wechat_access_token();
        $data = ['appid'=>env('WECHAT_APPID')];
        $this->tools->curl_post($url,json_encode($data));
    }
	public function get_user_list(Request $request)
    {
        $req = $request->all();
        $access_token = $this->get_access_token();
        $data = file_get_contents("https://api.weixin.qq.com/cgi-bin/user/get?access_token=".$access_token."&next_openid=");
        $data = json_decode($data,1);
//        dd($data);
        $last_info = [];
        foreach($data['data']['openid'] as $k=>$v){
            $user_info = file_get_contents('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->get_access_token().'&openid='.$v.'&lang=zh_CN');
            $user = json_decode($user_info,1);
//            dd($user);
            $last_info[$k]['nickname'] = $user['nickname'];
            $last_info[$k]['openid'] = $v;
        }
        return view('Wechat.wechat_user_list',['data'=>$last_info,'tagid'=>isset($req['tagid'])?$req['tagid']:'']);
    }
    public function get_user_detail(request $request)
    {
        $access_token = $this->get_access_token();
        $open_id=$request->openid;
//        dd($open_id);
       $data = file_get_contents("https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$open_id."&lang=zh_CN");
       $data = json_decode($data,1);
//       dd($data);
        return view("wechat/wechat_user_detail",['data'=>$data]);
    }
    public function get_access_token()
    {
        return $this->get_wechat_access_token();
    }
    public function get_wechat_access_token()
    {
        $redis = new \Redis();
        $redis->connect('127.0.0.1','6379');
        //加入缓存
        $access_token_key = 'wechat_access_token';
        if($redis->exists($access_token_key)){
            //存在
            return $redis->get($access_token_key);
        }else{
            //不存在
            $result = file_get_contents('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('WECHAT_APPID').'&secret='.env('WECHAT_APPSECRET'));
            $re = json_decode($result,1);
            $redis->set($access_token_key,$re['access_token'],$re['expires_in']);  //加入缓存
            return $re['access_token'];
        }
    }
    public function tag()
    {
        return view('Wechat.tagadd');
    }
    public function add_tag(Request $request)
    {
        $req = $request->all();
        $data = [
            'tag'=>[
                'name'=>$req['tag_name']
            ]
        ];
        $url = 'https://api.weixin.qq.com/cgi-bin/tags/create?access_token='.$this->tools->get_wechat_access_token();
        $re = $this->tools->curl_post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
        $result = json_decode($re,1);
        return redirect('/wechat/taglist');
    }
    public function tag_list()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/tags/get?access_token='.$this->tools->get_wechat_access_token();
        $re = file_get_contents($url);
        $result = json_decode($re,1);
        return view('Wechat.tag_list',['info'=>$result['tags']]);
    }
    public function del_tag(Request $request)
    {
        $url="https://api.weixin.qq.com/cgi-bin/tags/delete?access_token=".$this->tools->get_wechat_access_token();
        $data=[
            "tag" => [ "id"=>intval($request->id)]
        ];
//        dd($data);
        //不对中文进行编码
        $re=$this->tools->curl_post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
//        dd($re);
        $re=json_decode($re,1);
        return redirect('/wechat/taglist');
    }
    public function edit_tag(Request $request)
    {
        $id=$request->id;
//        dd($id);
        $tag_id=[ "tag_id"=>[$id] ];
        //拿到所有标签
        $re=file_get_contents("https://api.weixin.qq.com/cgi-bin/tags/get?access_token=".$this->tools->get_wechat_access_token()."");
        $re=json_decode($re,1);

        $re_arr = $re['tags'];
//        dd($re_arr);
        foreach($re_arr as $v){
            foreach($tag_id['tag_id'] as $vo){
                if($vo == $v['id']){
                    return view('Wechat.edtag',['id'=>$vo,'name'=>$v['name']]);
                }
            }
        }
    }
    public function do_edit_tag(Request $request)
    {
        $name=$request->all(['name']);
        $name=implode('',$name);
        $id=$request->all(['tag_id']);
        $id=implode('',$id);
//        dd($name);
        $url="https://api.weixin.qq.com/cgi-bin/tags/update?access_token=".$this->tools->get_wechat_access_token();
        $data=[
            "tag" => [ "id"=>$id ,"name"=>$name]
        ];
//        dd($data);
        //不对中文进行编码
        $re=$this->tools->curl_post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
//        dd($re);
        $re=json_decode($re,1);
        return redirect('/wechat/taglist');
    }
    //用户打标签
    public function usertaglist(Request $request)
    {
        $req = $request->all();
        $url = 'https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token='.$this->tools->get_wechat_access_token();
        $data = [
            'openid_list'=>$req['openid_list'],
            'tagid'=>$req['tagid']
        ];
        $re = $this->tools->curl_post($url,json_encode($data));
        $result = json_decode($re,1);
        return redirect('/wechat/taglist');
    }
    //标签下粉丝列表
    public function user_tag(Request $request)
    {
        $req = $request->all();
        $url = 'https://api.weixin.qq.com/cgi-bin/user/tag/get?access_token='.$this->tools->get_wechat_access_token();
        $data = [
            'tagid' => $req['tagid'],
            'next_openid' => ''
        ];
        $re = $this->tools->curl_post($url,json_encode($data));
        $result = json_decode($re,1);
        // dd($result);
        return view('Wechat.user_tag',['data'=>$result['data']['openid']]);
    }
    //推送消息
    public function push_tag_message(Request $request)
    {
        return view('Wechat.pushtag',['tagid'=>$request->all()['tagid']]);
    }
    //推送消息操作
    public function do_push_tag_message(Request $request)
    {
        $req = $request->all();
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token='.$this->tools->get_wechat_access_token();
        $data = [
            'filter' => [
                'is_to_all'=>false,
                'tag_id'=>$req['tagid']
            ],
            'text'=>[
                'content'=>$req['message']
            ],
            'msgtype'=>'text'
        ];
        $re = $this->tools->curl_post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
        $result = json_decode($re,1);
        return redirect('/wechat/taglist');
    }
}
