<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use DB;
use App\Tools\Tools;
class wechatController extends Controller
{
    public $tools;
    public function __construct(Tools $tools)
    {
        $this->tools = $tools;
    }
    /**
     * 调用频次清0
     */
    public function  clear_api(){
        $url = 'https://api.weixin.qq.com/cgi-bin/clear_quota?access_token='.$this->get_wechat_access_token();
        $data = ['appid'=>env('WECHAT_APPID')];
        $this->curl_post($url,json_encode($data));
    }
    public function post_test()
    {
        dd($_POST);
    }
    /**
     * 微信素材管理页面
     */
    public function wechat_source(Request $request,Client $client)
    {
        $req = $request->all();
        empty($req['source_type'])?$source_type = 'image':$source_type=$req['source_type'];
        if(!in_array($source_type,['image','voice','video','thumb'])){
            dd('类型错误');
        }
        if($req['page'] <= 0 ){
            dd('页码错误');
        }
        empty($req['page'])?$page = 1:$page=$req['page'];
        if($page <= 0 ){
            dd('页码错误');
        }
        $pre_page = $page - 1;
        $pre_page <= 0 && $pre_page = 1;
        $next_page = $page + 1;
        $url = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token='.$this->get_wechat_access_token();
        $data = [
            'type' =>$source_type,
            'offset' => $page == 1 ? 0 : ($page - 1) * 20,
            'count' => 20
        ];
        //guzzle使用方法
//        $r = $client->request('POST', $url, [ 
//            'body' => json_encode($data)
//        ]);
//        $re = $r->getBody();
        $re = $this->tools->redis->get('source_info');
        //$re = $this->curl_post($url,json_encode($data));
        $info = json_decode($re,1);
        $media_id_list = [];
        foreach($info['item'] as $v){
            $media_id_list[] = $v['media_id'];
        }
        $source_info = DB::connection('mysql_cart')->table('wechat_source')->whereIn('media_id',$media_id_list)->get();
        //dd($source_info);
        return view('Wechat.source',['info'=>$source_info,'pre_page'=>$pre_page,'next_page'=>$next_page,'source_type'=>$source_type]);
    }
    /**
     * 上传
     */
    public function upload(){
        return view('Wechat.upload',[]);
    }
    /**
     * image video voice thumb
     * id media_id type[类型] path ['/storage/wechat/image/imagename.jpg'] add_time
     * @param Request $request
     */
    public function do_upload(Request $request,Client $client){
        $type = $request->all()['type'];
        dd($type);
        $source_type = '';
        switch ($type){
            case 1: $source_type = 'image'; break;
            case 2: $source_type = 'voice'; break;
            case 3: $source_type = 'video'; break;
            case 4: $source_type = 'thumb'; break;
            default;
        }
        $name = 'file_name';
        if(!empty($request->hasFile($name)) && request()->file($name)->isValid()){
            //大小 资源类型限制
            $ext = $request->file($name)->getClientOriginalExtension();  //文件类型
            $size = $request->file($name)->getClientSize() / 1024 / 1024;
            if($source_type == 'image'){
                if(!in_array($ext,['jpg','png','jpeg','gif'])){
                    dd('图片类型不支持');
                }
                if($size > 2){
                    dd('太大');
                }
            }elseif($source_type == 'voice'){}
            $file_name = time().rand(1000,9999).'.'.$ext;
            $path = request()->file($name)->storeAs('wechat/'.$source_type,$file_name);
            $storage_path = '/storage/'.$path;
            $path = realpath('./storage/'.$path);
            $url = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token='.$this->get_wechat_access_token().'&type='.$source_type;
            //$result = $this->curl_upload($url,$path);
            $result = $this->guzzle_upload($url,$path,$client);
            $re = json_decode($result,1);
            //插入数据库
            DB::connection('mysql_cart')->table('wechat_source')->insert([
                'media_id'=>$re['media_id'],
                'type' => $type,
                'path' => $storage_path,
                'add_time'=>time()
            ]);
            echo 'ok';
        }
    }

	public function get_user_list()
    {
        $result=file_get_contents('https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$this->get_wechat_access_token().'&next_openid=');
        $re=json_decode($result,1);
        $last_info=[];
        foreach($re['data']['openid'] as $k=>$v){
            $user_info = file_get_contents('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->get_wechat_access_token().'&openid='.$v.'&lang=zh_CN');
            $user = json_decode($user_info,1);
            $last_info[$k]['nickname'] = $user['nickname'];
            $last_info[$k]['openid'] = $v;
        }
        // dd($last_info);
        //dd($re['data']['openid']);
        return view('Wechat.wechat_user_list',['info'=>$re['data']['openid']]);
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
        dd($result);
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
        dd($re);
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
                    return view('Wechat.edtag.blade.php',['id'=>$vo,'name'=>$v['name']]);
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
        dd($re);
    }
         /* 推送标签群发消息
     */
    public function push_tag_message(Request $request)
    {
        return view('Wechat.pushmsg',['tagid'=>$request->all()['tagid']]);
    }
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
        $re = $this->tools->curl_post($url,json_encode($data));
        $result = json_decode($re,1);
        dd($result);
    }
    public function user_tag_list(Request $request)
    {
        $req = $request->all();
        $url = 'https://api.weixin.qq.com/cgi-bin/tags/getidlist?access_token='.$this->tools->get_wechat_access_token();
        $data = [
            'openid'=>$req['openid']
        ];
        $re = $this->tools->curl_post($url,json_encode($data));
        $result = json_decode($re,1);
        $tag = file_get_contents('https://api.weixin.qq.com/cgi-bin/tags/get?access_token='.$this->tools->get_wechat_access_token());
        $tag_result = json_decode($tag,1);
        $tag_arr = [];
        foreach($tag_result['tags'] as $v){
            $tag_arr[$v['id']] = $v['name'];
        }
        foreach($result['tagid_list'] as $v){
            echo $tag_arr[$v]."<br/>";
        }
    }
    public function tag_openid(Request $request)
    {
        $req = $request->all();
        $url = 'https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token='.$this->tools->get_wechat_access_token();
        $data = [
            'openid_list'=>$req['openid_list'],
            'tagid'=>$req['tagid']
        ];
        $re = $this->tools->curl_post($url,json_encode($data));
        $result = json_decode($re,1);
        dd($result);
    }
    /**
     * 标签下粉丝列表
     */
    public function tag_openid_list(Request $request)
    {
        $req = $request->all();
        $url = 'https://api.weixin.qq.com/cgi-bin/user/tag/get?access_token='.$this->tools->get_wechat_access_token();
        $data = [
            'tagid' => $req['tagid'],
            'next_openid' => ''
        ];
        $re = $this->tools->curl_post($url,json_encode($data));
        $result = json_decode($re,1);
        dd($result);
    }
    public function push_template_message()
    {
        $openid = 'otAUQ1cLVoT4CrtTJX6ue6auuZn0';
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$this->tools->get_wechat_access_token();
        $data = [
            'touser'=>$openid,
            'template_id'=>'YBn0oqTBcJpzXNc8x9FUXfW9xCABnuc48SIC80w133g',
            'url'=>'http://www.wechat.com',
            'data'=>[
                'first'=>[
                    'value'=>'first',
                    'color'=>''
                ],
                'keyword1'=>[
                    'value'=>'keyword1',
                    'color'=>''
                ],
                'keyword2'=>[
                    'value'=>'keyword2',
                    'color'=>''
                ]
            ]
        ];
        $re = $this->tools->curl_post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
        $result = json_decode($re,1);
        dd($result);
    }
        
}
