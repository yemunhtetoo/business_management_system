@extends('admin.layout.layout')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0">CMS Page</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">CMS Page</li>
            </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              @if(Session::has('success_message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                  <strong>Success!</strong> {{ Session::get('success_message')}}
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
              @endif
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">CMS Pages</h3>
                  @if($pagesModule['edit_access']==1 || $pagesModule['full_access']==1)
                  <a style="max-width:150px; float: right;display:inline-block;" href="{{url('admin/add-edit-cms-page')}}" class="btn btn-block btn-primary">Add CMS Page</a>
                  @endif
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <table id="example2" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th>ID</th>
                      <th>Title</th>
                      <th>URL</th>
                      <th>Created on</th>
                      <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                      @foreach ($cmsPages as $page)
                        <tr>
                          <td>{{$page['id']}}</td>
                          <td>{{$page['title']}}</td>
                          <td>{{$page['url']}}</td>
                          <td>{{date("F j, Y, g:i a",strtotime($page['created_at']));}}</td>
                          <td>

                            @if($pagesModule['edit_access']==1 || $pagesModule['full_access']==1)
                            @if($page['status']==1)
                              <a class="updateCmsPageStatus" style="color:#3f6ed3" id="page-{{$page['id']}}" page_id="{{$page['id']}}" href="javascript:void(0)"><i class="fas fa-toggle-on" status="Active"></i></a>
                            @else 
                            <a class="updateCmsPageStatus" style="color: grey" id="page-{{$page['id']}}" page_id="{{$page['id']}}" href="javascript:void(0)"><i class="fas fa-toggle-off" status="Inactive"></i></a>
                            @endif  
                            
                            @endif
                            @if($pagesModule['edit_access']==1 || $pagesModule['full_access']==1)
                            &nbsp; &nbsp;
                            <a style="color:#3f6ed3" href="{{url('admin/add-edit-cms-page/'.$page['id'])}}"><i class="fas fa-edit"></i></a>
                            &nbsp; &nbsp;
                            @endif
                            @if($pagesModule['full_access']==1)
                            <a style="color:#3f6ed3" class="confirmDelete" name="CMS Page" title="Delete CMS Page" href="javascript:void(0)" record="cms-page" recordid="{{$page['id']}}"><i class="fas fa-trash"></i></a>
                            @endif
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                      <th>Rendering engine</th>
                      <th>Browser</th>
                      <th>Platform(s)</th>
                      <th>Engine version</th>
                      <th>CSS grade</th>
                    </tr>
                    </tfoot>
                  </table>
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
      </section>
    <!-- /.content -->
</div>
@endsection