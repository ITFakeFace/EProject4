@extends('main._layouts.master')

@section('content')
    <!-- Basic datatable -->
            <div class="form-group d-flex">
                <div class="ml-1">
                    <button id="register_leave" class="btn btn-info" data-toggle="modal" data-target="#exampleModalCenter2"></button>
                </div>
            </div>
        </div>
       

         <!-- Modal dkp -->
        <div class="modal fade" id="exampleModalCenter2" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    
                    <form action="{{action('DepartmentController@CreateDepartment')}}" method="post">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Thêm Mới Phòng Ban</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Tên Phòng Ban</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="txtName"  required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Tên Phòng Ban Tiếng Việt</label>
                                <div class="col-lg-9">
                                    <textarea class="form-control" name="txtName1" id="note_dkp" cols="20" rows="10" placeholder="" required></textarea>
                                </div>
                            </div> 
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-primary">Thêm mới</button>
                        </div>
                    </form>  
                </div>
            </div>
        </div>
            </thead>
        </table>
    </div>
    <!-- /basic datatable -->
@endsection

@section('scripts')
    <script>
        
        
       
            

    </script>
@endsection