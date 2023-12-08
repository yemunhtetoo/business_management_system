<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\AdminsRole;
use Hash;
use Auth;
use Validator;
use Image;
use Session;

class AdminController extends Controller
{
    public function dashboard(){
        Session::put('page','dashboard');
        return view('admin.dashboard');
    }

    public function login(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;

            $rules = [
                'email' => 'required|email|max:255',
                'password' => 'required|max:30',
            ];

            $customMessages = [
                'email.required'=>"Email is required",
                'email.email'=>"Valid Email is required",
                'password.required'=>"Password is required",
            ];

            $this->validate($request,$rules,$customMessages);

            if(Auth::guard('admin')->attempt(['email'=>$data['email'],'password'=>$data['password']])){
                if(isset($data['remember']) && !empty($data['remember'])){
                    setcookie("email",$data['email'],time()+3600);
                    setcookie("password",$data['password'],time()+3600);
                }else{
                    setcookie("email","");
                    setcookie("password","");
                }
                return redirect("admin/dashboard");
            }else{
                return redirect()->back()->with("error_message","Invalid Email or Password");
            }
        }
        return view('admin.login');
    }

    public function logout(){
        Auth::guard('admin')->logout();
        return redirect("admin/login");
    }

    public function updatePassword(Request $request){
        Session::put('page','update-password');
        if($request->isMethod('post')){
            $data = $request->all();
            //Check current password is correct or not
            if(Hash::check($data['current_pwd'],Auth::guard('admin')->user()->password)){
                //Check if new password and confirm password are matching
                if($data['new_pwd'] == $data['confirm_pwd']){
                    //Update new password
                    Admin::where('id',Auth::guard('admin')->user()->id)->update(['password'=>bcrypt($data['new_pwd'])]);
                return redirect()->back()->with("success_message","Password have been updated successfully");
                }else{
                return redirect()->back()->with("error_message","New Password and Confirm password doesn't match");
                }
            }else{
                return redirect()->back()->with("error_message","Current Password is incorrect");
            }
        }
        return view('admin.update_password');
    }

    public function checkCurrentPassword(Request $request){
        $data = $request->all();
        if(Hash::check($data['current_pwd'],Auth::guard('admin')->user()->password)){
            return "true";
        }else{
            return "false";
        }
    }

    public function updateAdminDetails(Request $request){
        Session::put('page','update-details');
        if($request->isMethod('post')){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;

            $rules = [
                'admin_name' => 'required|regex:/^[\pL\s\-]+$/u',
                'admin_mobile' => 'required|numeric|digits:10',
                'admin_image' => 'image',
            ];

            $customMessages = [
                'admin_name.required'=>"Name is required",
                'admin_name.regex'=>"Valid Name is required",
                'admin_mobile.required'=>" Mobile is required",
                'admin_mobile.numeric'=>"Valid Mobile is required",
                'admin_mobile.digits'=>"Valid Mobile is required",
                'admin_image.image'=>"Valid Image is required",
            ];

            $this->validate($request,$rules,$customMessages);

            //Admin image upload
            if($request->hasFile('admin_image')){
                $image_tmp = $request->file('admin_image');
                if($image_tmp->isValid()){
                    //Get Image Extension
                    $extension = $image_tmp->getClientOriginalExtension();
                    //Generate New Image Name
                    $imageName = rand(111,99999).'.'.$extension;
                    $image_path = 'admin/img/photos/'.$imageName;
                    Image::make($image_tmp)->save($image_path);
                }
            }
                else if(!empty($data['current_image'])){
                    $imageName = $data['current_image'];
                }else{
                    $imageName = "";
                }
            
            //Update Admin Details
            Admin::where('email',Auth::guard('admin')->user()->email)->update(['name'=>$data['admin_name'],'mobile'=>$data['admin_mobile'],'image'=>$imageName]);
            return redirect()->back()->with("success_message","Admin Details have been updated successfully");

            if(Auth::guard('admin')->attempt(['email'=>$data['email'],'password'=>$data['password']])){
                return redirect("admin/dashboard");
            }else{
                return redirect()->back()->with("error_message","Invalid Email or Password");
            }
        }
        return view('admin.update_details');
    }

    public function subadmins(){
        Session::put('page','subadmins');
        $subadmins = Admin::where('type','subadmin')->get();
        return view('admin.subadmins.subadmins')->with(compact('subadmins'));
    }

    public function updateSubAdminStatus(Request $request)
    {
        if($request->ajax()){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;
            if($data['status']=="Active"){
                $status = 0;
            }else{
                $status = 1;
            }
            Admin::where('id',$data['subadmin_id'])->update(['status'=>$status]);
            return response()->json(['status'=>$status,'subadmin_id'=>$data['subadmin_id']]);
        }
    }

    public function addEditSubadmin(Request $request,$id=null){
        if($id==""){
            $title = "Add Subadmin";
            $subadmindata = New Admin;
            $message = "Subadmin added successfully";
        }else{
            $title = "Edit Subadmin";
            $subadmindata = Admin::find($id);
            $message = "Subadmin have been updated successfully";
        }
        if($request->isMethod('post')){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;
            if($id==""){
                $subadminCount = Admin::where('email',$data['email'])->count();
                if($subadminCount>0){
                    return redirect()->back()->with('error_message','Subadmin already exists');
                }
            }

            //Subadmin validate
            $rules = [
                'name' => 'required',
                'mobile' => 'required|numeric',
                'image' => 'image'
            ];

            $customMessages = [
                'name.required' => 'Name is required',
                'mobile.required' => 'Mobile is required',
                'mobile.numeric' => 'Valid Mobile is required',
                'image.image' => 'Valid image is required',
            ];
            $this->validate($request,$rules,$customMessages);

            //Subadmin image upload
            if($request->hasFile('image')){
                $image_tmp = $request->file('image');
                if($image_tmp->isValid()){
                    //Get Image Extension
                    $extension = $image_tmp->getClientOriginalExtension();
                    //Generate New Image Name
                    $imageName = rand(111,99999).'.'.$extension;
                    $image_path = 'admin/img/photos/'.$imageName;
                    Image::make($image_tmp)->save($image_path);
                }
            }
                else if(!empty($data['current_image'])){
                    $imageName = $data['current_image'];
                }
                else{
                    $imageName = "";
                }

            $subadmindata->image = $imageName;
            $subadmindata->name = $data['name'];
            $subadmindata->mobile = $data['mobile'];
            if($id==""){
                $subadmindata->email = $data['email'];
                $subadmindata->type = 'subadmin';
                $subadmindata->status = 0;
            }
            if($data['password']!=""){
                $subadmindata->password = bcrypt($data['password']);
            }
            $subadmindata->status = 1;
            $subadmindata->save();
            return redirect('admin/subadmins')->with('success_message',$message);


        }
        return view('admin.subadmins.add_edit_subadmin')->with(compact('title','subadmindata'));
    }
    public function deleteSubadmin($id)
    {
        //Delete Subadmin 
        Admin::where('id',$id)->delete();
        return redirect()->back()->with('success_message','Subadmin deleted successfully!');
    }

    public function updateRole($id,Request $request){
        if($request->isMethod('post')){
            $data= $request->all();
            // echo "<pre>"; print_r($data); die;
            //Delete all earlier roles for Subadmin
            AdminsRole::where('subadmin_id',$id)->delete();

            //Add new roles for subadmin
            foreach ($data as $key => $value) {
                if(isset($value['view'])){
                    $view = $value['view'];
                }else{
                    $view = 0;
                }
                if(isset($value['edit'])){
                    $edit = $value['edit'];
                }else{
                    $edit = 0;
                }
                if(isset($value['full'])){
                    $full = $value['full'];
                }else{
                    $full = 0;
                }
            }
            // if(isset($data['cms_page']['view'])){
            //     $cms_pages_view = $data['cms_page']['view'];
            // }else{
            //     $cms_pages_view = 0;
            // }

            // if(isset($data['cms_page']['edit'])){
            //     $cms_pages_edit = $data['cms_page']['edit'];
            // }else{
            //     $cms_pages_edit = 0;
            // }

            // if(isset($data['cms_page']['full'])){
            //     $cms_pages_full = $data['cms_page']['full'];
            // }else{
            //     $cms_pages_full = 0;
            // }

            $role = new AdminsRole;
            $role->subadmin_id = $id;
            $role->module = $key;
            $role->view_access = $view;
            $role->edit_access = $edit;
            $role->full_access = $full;
            $role->save();

            $message = "Subadmin Roles updated successfully";
            return redirect()->back()->with('success_message',$message);

        }

        $subadminRoles = AdminsRole::where('subadmin_id',$id)->get()->toArray();
        $subadminDetails = Admin::where('id',$id)->first()->toArray();
        // dd($subadminRoles);
        $title = "Update".$subadminDetails['name']." Roles/Permission";
        // dd($subadminRoles);
        return view('admin.subadmins.update_roles')->with(compact('title','id','subadminRoles'));
    }
}   
