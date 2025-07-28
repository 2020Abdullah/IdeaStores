@extends('layouts.app')

@section('content')
    <!-- advanced search -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">بحث متقدم</h3>
        </div>
        <div class="card-body">
            <form action="#" id="searchForm" method="POST">
                @csrf
                <div class="row">
                    <div class="col-3">
                        <label class="form-label">من</label>
                        <input type="date" class="form-control start_date" name="start_date" placeholder="YYY-MMM-DDD">
                    </div>
                    <div class="col-3">
                        <label class="form-label">إلي</label>
                        <input type="date" class="form-control end_date" name="end_date" placeholder="YYY-MMM-DDD">
                    </div>
                    <div class="col-3">
                        <label class="form-label">الحالة</label>
                        <select name="statue" class="form-select" id="statue">
                            <option value="" selected>اختر الحالة</option>
                            <option value="0">عميل محتمل</option>
                            <option value="1">عميل مؤكد</option>
                            <option value="2">عميل سقط</option>
                        </select>
                    </div>
                    <div class="col-3">
                        <label class="form-label">بحث بالإسم أو بالرقم</label>
                        <input type="text" class="form-control searchText" name="searchText" placeholder="بحث بالإسم أو بالرقم ...">
                    </div>
               </div>
               <button type="submit" class="btn btn-outline-success waves-effect mt-2">بحث</button>
            </form>
        </div>
    </div>

    <!-- show data -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">عملائنا</h3>
            <a href="{{ route('customer.add') }}" class="btn btn-relief-success addBtn">
                <i data-feather='plus-circle'></i>
                <span>عميل جديد</span>
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @include('Customers.customer_table')
            </div>
            <div class="pagination">
                {{ $customers->links() }}
            </div>
        </div>
    </div>

    <!-- show modal confirm delete -->
    <div class="modal fade modal-danger text-start" id="deleteModal" tabindex="-1" aria-labelledby="myModalLabel130" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel130">حذف العنصر</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('customer.delete') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" value="" name="id" class="id">
                        <p>هل تريد بالفعل حذف هذا العميل سيتم حذف أيضاً شئ مرتبط به طلبات أو تاسكات ؟</p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger waves-effect waves-float waves-light">تأكيد الحذف</button>
                        <button type="button" class="btn btn-warning waves-effect waves-float waves-light" data-bs-dismiss="modal">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(function(){
            $(document).on('click', '.delBtn', function(){
                let id = $(this).data('id');
                $("#deleteModal .id").val(id);
            })
        })

        // search filter 
        $(document).on('submit', '#searchForm', function(e){
            e.preventDefault();
            let formData = $(this).serialize();
            $.ajax({
                url: "{{ route('customer.filter') }}",
                method: 'POST',
                data: formData,
                beforeSend: function () {
                    $('#loading-excute').fadeIn(500);
                },
                success: function (response) {
                    $('.table-responsive').html(response);
                    feather.replace();
                },
                error: function(xhr){
                    console.log(xhr);
                },
                complete: function(){
                    $('#loading-excute').fadeOut(500);
                }
            });
        })
    </script>
@endsection
