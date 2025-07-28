@extends('layouts.app')

@section('content')

    <!-- show all errors -->
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger">
                <div class="alert-body">
                    <span>{{$error}}</span>
                </div>
            </div>
        @endforeach
    @endif

    <!-- show success -->
    @if (session('success'))
        <div class="alert alert-success">
            <h3 class="alert-heading">عملية ناجحة</h3>
            <div class="alert-body">
                {{session('success')}}
            </div>
        </div>
    @endif  

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">إضافة عميل جديد</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('customer.store') }}" method="POST" id="FormCustomer" enctype="multipart/form-data">
                @csrf
                <div class="row mb-1">
                    <div class="col-6 mb-2">
                        <label class="form-label">صورة</label>
                        <input type="file" class="form-control" name="imagePath" id="imagePath">
                    </div>
                    <div class="col-6 mb-2">
                        <label class="form-label">اسم العميل</label>
                        <input type="text" class="form-control" name="name" id="name">
                    </div>
                    <div class="col-6 mb-2">
                        <label class="form-label">اسم الشركة</label>
                        <input type="text" class="form-control" name="business_name" id="business_name">
                    </div>
                    <div class="col-6 mb-2">
                        <label class="form-label">نوع النشاط</label>
                        <input type="text" class="form-control" name="business_type" id="business_type">
                    </div>
                    <div class="col-6 mb-2">
                        <label class="form-label">رقم الهاتف</label>
                        <input type="text" class="form-control" name="phone" id="phone">
                    </div>
                    <div class="col-6 mb-2">
                        <label class="form-label">رقم هاتف آخر إن وجد (اختيارى)</label>
                        <input type="text" class="form-control" name="other_phone" id="other_phone">
                    </div>
                    <div class="col-6 mb-2">
                        <label class="form-label">رقم الواتساب (اختيارى)</label>
                        <input type="text" class="form-control" name="whatsUp" id="whatsUp">
                    </div>
                    <div class="col-6 mb-2">
                        <label class="form-label">جنسية العميل</label>
                        <input type="text" class="form-control" name="National" id="National">
                    </div>
                    <div class="col-6 mb-2">
                        <label class="form-label">مكان العميل</label>
                        <input type="text" class="form-control" name="place" id="place">
                    </div>
                    <div class="col-6 mb-2">
                        <label class="form-label">وظيفة العميل</label>
                        <input type="text" class="form-control" name="job" id="job">
                    </div>
                    <div class="col-6 mb-2">
                        <label class="form-label">تاريخ التواصل</label>
                        <input type="date" class="form-control" name="date" id="date">
                    </div>
                    <div class="col-6 mb-2">
                        <label class="form-label">حالة العميل</label>
                        <select name="statue" class="form-select" id="statue">
                            <option selected>اختر الحالة ...</option>
                            <option value="0">محتمل</option>
                            <option value="1">مؤكد</option>
                            <option value="2">سقط</option>
                        </select>
                    </div>
                    <div class="col-12 mb-2">
                        <label class="form-label">ملاحظات</label>
                        <textarea name="notes" class="form-control" id="notes" cols="5" rows="5"></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-success waves-effect waves-float waves-light mt-2">
                    حفظ البيانات
                </button>
            </form>
        </div>
    </div>
@endsection
