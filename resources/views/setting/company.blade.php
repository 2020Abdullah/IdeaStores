@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">معلومات الشركة</h3>
    </div>
    <form action="{{ route('setting.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="mb-2">
                <label for="logo" class="form-label">لوجو الشركة</label>
                <input type="file" id="logo" name="logo" class="form-control">
            </div>
            <div class="mb-2">
                <label for="company_name" class="form-label">اسم الشركة</label>
                <input type="text" id="company_name" name="company_name" value="{{ $app->company_name ?? ''}}" class="form-control">
            </div>
            <div class="mb-2">
                <label for="company_info" class="form-label">وصف الشركة</label>
                <textarea name="company_info" class="form-control" id="company_info" cols="5" rows="5">{{ $app->company_info ?? ''}}</textarea>
            </div>
            <div class="mb-2">
                <label for="Tax_number" class="form-label">رقم السجل الضريبي</label>
                <input type="text" id="Tax_number" name="Tax_number" value="{{ $app->Tax_number ?? ''}}" class="form-control">
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-success waves-effect waves-float waves-light mt-2">
                حفظ البيانات
            </button>
        </div>
    </form>

</div>
@endsection

