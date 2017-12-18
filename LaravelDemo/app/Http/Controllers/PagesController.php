<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TheLoai;
use App\Slide;
use App\LoaiTin;
use App\TinTuc;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\gioithieu;
class PagesController extends Controller
{
    //
    function __construct()
    {
    	$theloai = TheLoai::all();
    	$slide = Slide::all();
    	view()->share('theloai',$theloai);
    	view()->share('slide',$slide);
        if(Auth::check())
            {
                view()->share('nguoidung',Auth::user());
            }
            
    }
    function trangchu()
    {
    	return view('pages.trangchu');
    }
    function lienhe()
    {
    	return view('pages.lienhe');
    }
    function loaitin($id)
    {	
    	$loaitin= LoaiTin::find($id);
    	$tintuc = TinTuc::where('idLoaiTin',$id)->paginate(5);
    	return view('pages.loaitin',['loaitin'=>$loaitin,'tintuc'=>$tintuc]); 
    }
    function tintuc($id)
    {
        $tintuc = TinTuc::find($id);
        $tinnoibat = TinTuc::where('NoiBat',1)->take(4)->get();
        $tinlienquan = TinTuc::where('idLoaiTin',$tintuc->idLoaiTin)->take(4)->get();
        return view('pages.tintuc',['tintuc'=>$tintuc,'tinnoibat'=>$tinnoibat,'tinlienquan'=>$tinlienquan]);
    }
    function getDangnhap()
    {
        return view('pages.dangnhap');
    }
    function postDangnhap(Request $request)
    {
       $this->validate($request,[
            'email'=>'required',
            'password'=>'required|min:3|max:32'

        ],[
            'email.required'=>'Ban chua nhap email',
            'password.required'=>'ban chua nhap password',
            'password.min'=>'Password khong duoc nho hon 3 ky tu',
            'password.max'=>'Password khong duoc lon hon 32 ky tu'
        ]);
        if(Auth::attempt(['email'=>$request->email,'password'=>$request->password]))
            {
                return redirect('trangchu');
            }
            else
            {
                return redirect('dangnhap')->with('thongbao','Dang nhap khong thanh cong');
            }
    }
    function pgetDangxuat()
    {
        Auth::logout();
        return redirect('trangchu');
    }
    function getNguoidung()
    {
        $user = Auth::user();
        return view('pages.nguoidung',['nguoidung'=>$user]);
    }
    function postNguoidung(Request $request)
    {

         $this->validate($request,[
        'name'=>    'required|min:3',    
    ],[
        'name.required'=>'Ban chua nhap ten nguoi dung',
        'name.min'=>'Ten nguoi dung phai co it nhat 3 ky tu'   
    ]);
        $user =  Auth::user();
        $user->name = $request->name;
       

        if($request->checkpassword == "on")
        {   
            $this->validate($request,[
        
        'password'=>'required|min:3|max:32',
        'passwordAgain'=>'required|same:password'

    ],[
        
        'password.required'=>'Ban chua nhap mat khau',
        'password.min'=>'Mat khau phai co it nhat 3 kys tu',
        'password.max'=>'Mat khau chi duoc toi da 32 ky tu',
        'passwordAgain.required'=>'ban chua nhap lai mat khau',
        'paaswordAgain.same'=>'Mat khau nhap lai chua khop'
    ]);
          $user->password = bcrypt($request->password);
        }


        $user->save();

        return redirect('nguoidung')->with('thongbao','Ban da sua thanh cong');
    }
    function getdangky()
    {
            return view('pages.dangky');
    }
    function postdangky(Request $request)
    {
        $this->validate($request,[
        'name'=>    'required|min:3',
        'email'=>   'required|email|unique:users,email',
        'password'=>'required|min:3|max:32',
        'passwordAgain'=>'required|same:password'

    ],[
        'name.required'=>'Ban chua nhap ten nguoi dung',
        'name.min'=>'Ten nguoi dung phai co it nhat 3 ky tu',
        'email.required'=>'Ban chua nhap email',
        'email.email'=>'Ban chua nhap dung dinh danh email',
        'email.unique'=>'Email da ton tai',
        'password.required'=>'Ban chua nhap mat khau',
        'password.min'=>'Mat khau phai co it nhat 3 kys tu',
        'password.max'=>'Mat khau chi duoc toi da 32 ky tu',
        'passwordAgain.required'=>'ban chua nhap lai mat khau',
        'paaswordAgain.same'=>'Mat khau nhap lai chua khop'
    ]);
        $user= new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);

        $user->quyen = 0;

        $user->save();

        return redirect('dangky')->with('thongbao','dang ky thanh cong');
    }
    function posttimkiem(Request $request)
    {
        $tukhoa= $request->tukhoa;
        $tintuc= TinTuc::where('TieuDe','like',"%$tukhoa%")->orwhere('TomTat','like',"%$tukhoa%")->orwhere('NoiDung','like',"%$tukhoa%")->take(30)->paginate(5);
        return view('pages.timkiem',['tintuc'=>$tintuc,'tukhoa'=>$tukhoa]);    
    }
    function Gioithieu()
    {
        return view('pages.gioithieu');
    }

}
