<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tools\Tools;
use App\Model\Upload;
class uploadController extends Controller
{
	public $tools;
    public $request;
    public function __construct(Tools $tools,Request $request)
    {
        $this->tools = $tools;
        $this->request = $request;
    }
    public function upload()
    {
    	return view('Wechat.upload');
    }
    public function doupload()
    {
    	$ree=$this->request->all();
    	$type_arr = ['image'=>1,'voice'=>2,'video'=>3,'thumb'=>4];
    	// dd($ree);
    	if (!$this->request->hasFile('file_name')) {
    		dd('null');
    	}
    	$res=$this->request->file('file_name');
    	$ext=$res->getClientOriginalExtension();
    	$file_name=time().rand(1000,9999).".".$ext;
    	// dd($file_name);
    	$name=$this->request->file('file_name')->storeAs('wechat/'.$ree['type'],$file_name);
    	$url="https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=".$this->tools->get_wechat_access_token()."&type=".$ree['type'];
    	// $re=$this->tools->wechat_curl_file($url,storage_path('app/public/'.$name));
    	// $req=json_decode($re,1);
    	// dd($req);
    	$data = [
            'media'=>new \CURLFile(storage_path('app/public/'.$name)),
        ];
        if($ree['type'] == 'video'){
            $data['description'] = json_encode(['title'=>'标题','introduction'=>'描述'],JSON_UNESCAPED_UNICODE);
        }
        $re = $this->tools->wechat_curl_file($url,$data);
        $result = json_decode($re,1);
        if(!isset($result['errcode'])){
            Upload::insert([
                'media_id'=>$result['media_id'],
                'type'=>$type_arr[$ree['type']],
                'path'=>'/storage/'.$name,
                'addtime'=>time()
            ]);
        }
        return redirect('/wechat/uploadlist');
    }
    public function source_list()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token='.$this->tools->get_wechat_access_token();
        $data = [
            'type'=>'image',
            'offset'=>'0',
            'count'=>20
        ];
        $re = $this->tools->curl_post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
        $result = json_decode($re,1);
        dd($result);
    }
    public function uploadlist()
    {
        $req = $this->request->all();
        !isset($req['type']) ? $type = 1 : $type = $req['type'];
        $info = Upload::where(['type'=>$type])->paginate(10);
        return view('Wechat.uploadlist',['info'=>$info,'type'=>$type]);
    }
    public function download()
    {
        $req = $this->request->all();
        $url = 'https://api.weixin.qq.com/cgi-bin/material/get_material?access_token='.$this->tools->get_wechat_access_token();
        $re = $this->tools->curl_post($url,json_encode(['media_id'=>$req['media_id']]));
        $result = json_decode($re,1);
        //设置超时参数
        $opts=array(
            "http"=>array(
                "method"=>"GET",
                "timeout"=>3  //单位秒
            ),
        );
        //创建数据流上下文
        $context = stream_context_create($opts);
        //创建数据流上下文
        $file_source = file_get_contents($result['down_url'],false, $context);
        Storage::put('/wechat/video/22223332.mp4', $file_source);
    }
}