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
<div class="card">
    <div class="card-header">
        <h3 class="card-title">إدارة النسخ الاحتياطي</h3>
    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @elseif(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
    
        <form action="{{ route('backup.create') }}" method="POST">
            @csrf
            <button class="btn btn-relief-success">إنشاء نسخة احتياطية وتحميلها</button>
        </form>
    
        <hr>
    
        <form action="{{ route('backup.restore') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label>تحميل ملف نسخة احتياطية (.sql)</label>
                <input type="file" name="backup_file" accept=".sql" class="form-control" required>
            </div>
            <button class="btn btn-relief-primary">استعادة النسخة</button>
        </form>
    </div>

</div>
@endsection
