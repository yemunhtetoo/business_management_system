<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Models\AdminsRole;
use Auth;
use Illuminate\Http\Request;
use Session;

class CmsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        Session::put('page','cmspage');
        $cmsPages = Cmspage::get()->toArray();
        // dd($cmsPage);

        //Set Admin/Subadmin permissions for cms pages
        $cmspageModuleCount = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'cms_pages'])->count();
        // dd($cmspageModuleCount);
        $pagesModule = array();
        if(Auth::guard('admin')->user()->type=='admin'){
            $pagesModule['view_access'] = 1;
            $pagesModule['edit_access'] = 1;
            $pagesModule['full_access'] = 1;
        }else if($cmspageModuleCount==0){
            $message = "This feature is restricted for you";
            return redirect()->back()->with('error_message',$message);
        }else{
            $pagesModule =AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'cms_pages'])->first()->toArray();
            // dd($pagesModule);
        }
        return view('admin.pages.cms_pages')->with(compact('cmsPages','pagesModule'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CmsPage $cmsPage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request,$id = null)
    {
        if($id==""){
            $title = "Add CMS Page";
            $cmspage = new CmsPage;
            $message = "CMS Page added successfully";
        }else{
            $cmspage = CmsPage::find($id);
            $title = "Edit CMS Page";
            $message = "CMS Page updated successfully";
        }

        if($request->isMethod('post')){
            $data =$request->all();
            // echo "<pre>"; print_r($data); die;

            //CMS Page Validate
            $rules = [
                'title' => 'required',
                'url' => 'required',
                'description' => 'required'
            ];

            $customMessages = [
                'title.required' => 'Page title is required',
                'url.required' => 'Page URL is required',
                'description.required' => 'Page Description is required',
            ];
            $this->validate($request,$rules,$customMessages);
            
            $cmspage->title = $data['title'];
            $cmspage->url = $data['url'];
            $cmspage->description = $data['description'];
            $cmspage->meta_title = $data['meta_title'];
            $cmspage->meta_description = $data['meta_description'];
            $cmspage->meta_keywords = $data['meta_keywords'];
            $cmspage->status = 1;
            $cmspage->save();
            return redirect('admin/cms-pages')->with('success_message',$message);

        }
        return view('admin.pages.add_edit_cmspage')->with(compact('title','cmspage'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        if($request->ajax()){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;
            if($data['status']=="Active"){
                $status = 0;
            }else{
                $status = 1;
            }
            CmsPage::where('id',$data['page_id'])->update(['status'=>$status]);
            return response()->json(['status'=>$status,'page_id'=>$data['page_id']]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //Delete CMS Page
        CmsPage::where('id',$id)->delete();
        return redirect()->back()->with('success_message','CMS Page deleted successfully!');
    }
}
