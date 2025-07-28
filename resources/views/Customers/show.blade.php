@extends('layouts.app')

@section('css')
<style>
  #customer_layout {
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  #customer_layout .action {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  #customer_layout .profileImg {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 2rem;
  }
  #customer_layout .profileImg img {
    width: 200px;
    height: 200px;
    border-radius: 50px;
  }
  #customer_layout .profileImg h4 {
    text-align: center;
    margin-top: 1rem;
    font-size: 25px;
  }
  .pagination {
    margin-right: 1rem;
  }
</style>
@endsection

@section('content')
<div class="row">
    <!-- show info customer -->
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div id="customer_layout">
                    <div class="profileImg">
                        @if($customer->imagePath == null)
                            <img src="{{ asset('assets/images/web/profile.png') }}" alt="img">
                        @else
                            <img src="{{ asset($customer->imagePath) }}" alt="img">
                        @endif
                        <h4>{{ $customer->name }}</h4>
                    </div>
                    <div class="action">
                        @if ($customer->whatsUp !== null)
                            @php
                                $clean_number = preg_replace('/\D+/', '', $customer->whatsUp);
                            @endphp
                            <a href="https://wa.me/{{ $clean_number }}" class="btn btn-success waves-effect waves-float waves-light" target="_blank">
                                <i data-feather='message-circle'></i>
                                <span>إرسال رسالة </span>
                            </a>
                        @endif
                        <a href="{{ route('customer.edit', $customer->id) }}" class="btn btn-outline-success waves-effect waves-float waves-light">
                            <i data-feather='edit'></i>
                            <span>تعديل البينات</span>
                        </a>
                    </div>
                    <div class="customer_info">
                        <h4>معلومات عن العميل</h4>
                        <hr />
                        <div class="row">
                            <div class="col-md-6 col-sm-12 mb-2">
                                <label class="form-label">اسم العميل</label>
                                <input type="text" class="form-control disabled" readonly value="{{ $customer->name }}">
                            </div>
                            <div class="col-md-6 col-sm-12 mb-2">
                                <label class="form-label">اسم الشركة</label>
                                <input type="text" class="form-control disabled" readonly value="{{ $customer->business_name }}">
                            </div>
                            <div class="col-md-6 col-sm-12 mb-2">
                                <label class="form-label">نوع النشاط</label>
                                <input type="text" class="form-control disabled" readonly value="{{ $customer->business_type }}">
                            </div>
                            <div class="col-md-6 col-sm-12 mb-2">
                                <label class="form-label">رقم الهاتف</label>
                                <input type="text" class="form-control disabled" readonly value="{{ $customer->phone }}">
                            </div>
                            <div class="col-md-6 col-sm-12 mb-2">
                                <label class="form-label">رقم الوتساب</label>
                                <input type="text" class="form-control disabled" readonly value="{{ $customer->whatsUp }}">
                            </div>
                            <div class="col-md-6 col-sm-12 mb-2">
                                <label class="form-label">رقم هاتف آخر</label>
                                <input type="text" class="form-control disabled" readonly value="{{ $customer->other_phone }}">
                            </div>
                            <div class="col-md-6 col-sm-12 mb-2">
                                <label class="form-label">الجنسية</label>
                                <input type="text" class="form-control disabled" readonly value="{{ $customer->National }}">
                            </div>
                            <div class="col-md-6 col-sm-12 mb-2">
                                <label class="form-label">الدولة / المدينة</label>
                                <input type="text" class="form-control disabled" readonly value="{{ $customer->place }}">
                            </div>
                            <div class="col-md-6 col-sm-12 mb-2">
                                <label class="form-label">وظيفة العميل</label>
                                <input type="text" class="form-control disabled" readonly value="{{ $customer->job }}">
                            </div>
                            <div class="col-md-6 col-sm-12 mb-2">
                                <label class="form-label">حالة العميل</label>
                                @if ($customer->statue == 0)
                                    <input type="text" class="form-control disabled" readonly value="محتمل">
                                @elseif($customer->statue == 1)
                                    <input type="text" class="form-control disabled" readonly value="مؤكد">
                                @else
                                    <input type="text" class="form-control disabled" readonly value="سقط">
                                @endif
                            </div>
                            <div class="col-md-6 col-sm-12 mb-2">
                                <label class="form-label">تاريخ التواصل</label>
                                <input type="text" class="form-control disabled" readonly value="{{ $customer->date }}">
                            </div>
                            <div class="col-md-6 col-sm-12 mb-2">
                                <label class="form-label">تاريخ آخر توريد</label>
                                <input type="text" class="form-control disabled" readonly value="{{ $last_order }}">
                            </div>
                            @if ($last_order == null)
                                <div class="col-md-12 col-sm-12 mb-2">
                                    <label class="form-label">طلب العميل</label>
                                    <textarea class="form-control disabled" cols="5" rows="5" readonly>{{ $customer->notes }}</textarea>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- show orders & tasks -->
    <div class="col-12">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3>tasks</h3>
                        <div class="card-action">
                            <a href="#" class="btn btn-icon btn-outline-success waves-effect waves-float waves-light" data-bs-toggle="modal" data-bs-target="#addTask">
                                <i data-feather='plus'></i>
                            </a>
                        </div>
                    </div>
                </div>
                @foreach ($tasks as $task)
                    <div class="card">
                        <div class="card-header task-header">
                            <h4>{{ $task->name }}</h4>
                            <div class="task-action">
                                <a href="#" class="btn btn-icon btn-outline-success edit-task-btn"
                                    data-id="{{ $task->id }}"
                                    data-name="{{ $task->name }}"
                                    data-task_type="{{ $task->task_type }}"
                                    data-follow_type="{{ json_encode($task->follow_type) }}"
                                    data-follow_date="{{ $task->follow_date }}"
                                    data-notes="{{ $task->notes }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editTask">
                                        <i data-feather='edit'></i>
                                </a>
                                <a href="#" class="btn btn-icon btn-outline-danger delete-task-btn"
                                    data-id="{{ $task->id }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteTask">
                                        <i data-feather='trash'></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body task-body">
                            <p><strong>تاريخ التاسك :</strong> {{ $task->follow_date }}</p>
                            <p><strong> آخر تحديث :</strong> {{ $task->updated_at->format('Y-m-d') }}</p>
                            <p><strong>نوع التاسك :</strong> {{ $task->task_type == 1 ? 'متابعة عملية شراء' : 'خدمة ما بعد البيع'}}</p>
                            <p>
                                <strong>الطريقة:</strong>
                                @foreach($task->follow_type as $type)
                                    <span class="badge bg-info text-white">{{ $type }}</span>
                                @endforeach
                            </p>
                            <p class="task-text"><strong>ملاحظات:</strong></p>
                            <p>{{ $task->notes }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">الطلبات المؤكدة</h3>
                        <div class="table-action">
                            <a href="#" class="btn btn-icon btn-outline-success waves-effect waves-float waves-light" data-bs-toggle="modal" data-bs-target="#addOrder">
                                <i data-feather='plus'></i>
                                <span>إنشاء طلب جديد</span>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>رقم الطلب</th>
                                    <th>تاريخ التوريد</th>
                                    <th>الطلب</th>
                                    <th>طريقة الإستلام</th>
                                    <th>حالة الطلب</th>
                                    <th>إجمالي السعر</th>
                                    <th>ملاحظات</th>
                                    <th>إجراء</th>
                                </tr>
                                @forelse ($customer->orders as $order)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $order->order_date }}</td>
                                        <td>{{ $order->order_info }}</td>
                                        <td>{{ $order->Receipt_method === 'shipping' ? 'شحن إلي المكان' : 'استلام من مخازننا'}}</td>
                                        <td>{{ $order->total_price }} EGP</td>
                                        <td>
                                            @if ($order->statue === 'progress')
                                                <span class="badge badge-glow bg-success">قيد التنفيذ</span>
                                            @else
                                                <span class="badge badge-glow bg-secondary">مكتمل</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="#" class="showNotesBtn btn btn-icon btn-outline-info waves-effect waves-float waves-light" data-bs-toggle="modal" data-bs-target="#showNotes"
                                            data-notes="{{ $order->notes }}"
                                            >
                                                <i data-feather='eye'></i>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="#" class="editOrderBtn btn btn-icon btn-outline-success waves-effect waves-float waves-light" data-bs-toggle="modal" data-bs-target="#editOrder"
                                            data-id="{{ $order->id }}"
                                            data-order_date="{{ $order->order_date }}"
                                            data-order_info="{{ $order->order_info }}"
                                            data-Receipt_method="{{ $order->Receipt_method }}"
                                            data-total_price="{{ $order->total_price }}"
                                            data-statue="{{ $order->statue }}"
                                            data-notes="{{ $order->notes }}"
                                            data-commission="{{ $order->commission }}"
                                            >
                                                <i data-feather='edit'></i>
                                            </a>

                                            <a href="#" class="delOrderBtn btn btn-icon btn-outline-danger waves-effect waves-float waves-light" data-bs-toggle="modal" data-bs-target="#deleteOrder"
                                            data-id="{{ $order->id }}"
                                            >
                                                <i data-feather='trash'></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="text-center">
                                        <td colspan="8">لم يقم بتأكيد اى اوردر حتي الآن</td>
                                    </tr>
                                @endforelse
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('Customers.models')

@endsection

@section('js')
<script>
    $(document).on('click', '.edit-task-btn', function () {
        let btn = $(this);
    
        let id = btn.data('id');
        let name = btn.data('name');
        let type = btn.data('task_type');
        let followType = btn.data('follow_type');
        let followDate = btn.data('follow_date');
        let notes = btn.data('notes');
    
        $('#editTask input[name="task_id"]').val(id);
        $('#editTask input[name="name"]').val(name);
        $('#editTask select[name="type"]').val(type);
        $('#editTask input[name="follow_date"]').val(followDate);
        $('#editTask textarea[name="notes"]').val(notes);
    
        // تفريغ وإعادة تعبئة المتابعة
        $('#editTask input[name="follow_type[]"]').prop('checked', false);
        if (followType) {
            followType.forEach(function (type) {
                $('#editTask input[name="follow_type[]"][value="' + type + '"]').prop('checked', true);
            });
        }
    });

    $(document).on('click', '.delete-task-btn', function(){
        let id = $(this).data('id');
        $("#deleteTask .id").val(id);
    })

    $(document).on('click', '.editOrderBtn', function(){
        let btn = $(this);
    
        let id = btn.data('id');
        let order_date = btn.data('order_date');
        let order_info = btn.data('order_info');
        let Receipt_method = btn.attr('data-Receipt_method');
        let total_price = btn.data('total_price');
        let statue = btn.data('statue');
        let notes = btn.data('notes');
        let commission = btn.data('commission');
    
        $('#editOrder input[name="id"]').val(id);
        $('#editOrder input[name="order_date"]').val(order_date);
        $('#editOrder textarea[name="order_info"]').val(order_info);
        $('#editOrder .Receipt_method').val(Receipt_method);
        $('#editOrder input[name="total_price"]').val(total_price);
        $('#editOrder select[name="statue"]').val(statue);
        $('#editOrder textarea[name="notes"]').val(notes);
        $('#editOrder input[name="commission"]').val(commission);
    })

    $(document).on('click', '.delOrderBtn', function(){
        let id = $(this).attr('data-id');
        $('#deleteOrder .id').val(id);
    })

    $(document).on('click', '.showNotesBtn', function(){
        let notes = $(this).attr('data-notes');
        if(notes != ''){
            $('#showNotes .notes').val(notes);
        }
    })

</script>
@endsection