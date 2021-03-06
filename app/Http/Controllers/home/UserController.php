<?php

namespace App\Http\Controllers\home;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
class UserController extends Controller
{

    //设置属性
     public $id;

    public function usercenter()
    {
        //加载个人中心
        return view('home.usercenter.usercenter');
    }

    public static function gainUsername()
    {
    	//得到sseione里的username值
    	 $name=session('username');
    	 //根据name查询用户信息
    	 $info=DB::table('users')->where('username','=',$name)->first();
    	 return $info;
    }

    public function index()
    {

        //得到sseione里的username值
         $name=session('username');
         //dd($name);
         //查询登陆的用户信息
         $info=DB::table('users')->where('username','=',$name)->first();

    	return view('home.usercenter.userdata',['info'=>$info]);
    }

    //修改个人资料数据
    public function dataedit(Request $request)
    {
        //dd($request->all());
         $name=session('username');
         //dd($name);
         //查询登陆的用户信息
         $info=DB::table('users')->where('username','=',$name)->first();
         //dd($info);
         $id=$info->id;
         //dd($id);
        //的到上传数据
        $data=$request->only('nikename','email','phone');
        //判断是否有图片上传
        if($request->hasFile('pic')){

            //获的上传文件后缀
            $ext=$request->file('pic')->getClientOriginalExtension();
            //获得新图片名字
            $name=time().rand(1000,9999);
            //设置上传路径
           $request->file('pic')->move('./static/image/', $name.'.'.$ext);
            //拼接头像路径
            $data['pic']='./static/image/'.$name.'.'.$ext;
             //删除老图片
            //unlink($info->pic);
        }else{

            //老图片路径
            $data['pic']=$info->pic;
        }
        //dd($data);
        //修改数据
        $row=DB::table('users')->where('id','=',$id)->update($data);

        if($row){

           

             return redirect('/userdata')->with('success','更新成功');

        }else{

            return redirect('/userdata')->with('error','更新失败');
        }
    }

    //添加收货地址
    public function address()
    {
        $name=session('username');
         //dd($name);
         //查询登陆的用户信息
         $info = DB::table('users')->where('username','=',$name)->first();
         //得到uid
         $uid = $info->id;
        //查询收货地址
        $address = DB::table('addresses')->where('user_id','=',$uid)->get();
        //dd($address);
        return view('home.usercenter.address',['address'=>$address]);
    }

    //删除收货地址
    public function deladdress(Request $request)
    {
        //接收id
        $id=$request->input('id');

        if(DB::table('addresses')->where('id','=',$id)->delete()){

            echo 1;
            
        }else{

            echo 2;
        }

    }


    //添加收货地址
    public function add()
    {

        return view('home.usercenter.add');
    }


    //处理添加收货地址
    public function doadd(Request $request)
    {
            $name=session('username');
         //dd($name);
         //查询登陆的用户信息
         $info = DB::table('users')->where('username','=',$name)->first();

        //dd($request->all());
        $data=$request->except('_token');
        //dd($data);
        $data['user_id']=$info->id;
        //把得到的数据插入数据库
        $row=DB::table('addresses')->insert($data);

        if($row){

            return redirect('/useraddress');

        }else{

            return redirect('/add')->withInput('error','添加失败');
        }
    }

    public static function getgoods($orderid)
    {
        $goods=DB::table('order_goods')->where('order_id','=',$orderid)->get();

        return $goods;
    }

    public static function getimg($skuid)
    {
        $img = DB::table('skus')->where('id','=',$skuid)->first();

        return $img;
    }

    //查看订单
    public function userorder()
    {
        //得到sseione里的username值
         $name=session('username');
         //dd($name);
         //查询登陆的用户信息
        $info=DB::table('users')->where('username','=',$name)->first();
        $id = $info->id;

        $order=DB::table('orders')->where('user_id','=',$id)->get();
        // dd($order);
       
       $nums='';
        
        return view('home.usercenter.order',['order'=>$order,'nums'=>$nums]);
    }

    //查看用户评论
    public function usercomment()
    {
         $name=session('username');
         //dd($name);
         //查询登陆的用户信息
        $info=DB::table('users')->where('username','=',$name)->first();
        $pic = $info->pic;

        $id = $info->id;

        $comment=DB::table('comments')->where('user_id','=',$id)->get();

        return view('home.usercenter.comment',['comment'=>$comment,'pic'=>$pic]);
    }

}
