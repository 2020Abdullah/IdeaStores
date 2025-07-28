@extends('layouts.app')

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">الحسابات</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="#">عرض الحسابات المالية</a>
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title">الحسابات المالية</h3>
        <div class="card-action">
            <button type="button" class="btn btn-success waves-effect waves-float waves-light" data-bs-toggle="modal" data-bs-target="#addAccount">
                إضافة حساب مالي 
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <th>اسم الحساب</th>
                    <th>نوع الحساب</th>
                    <th>رصيد الربحية </th>
                    <th>الرصيد الحالي</th>
                    <th>إجراء</th>
                </tr>
                @foreach ($accounts as $account)
                    <tr>
                        <td>{{ $account->accountable->name ?? '---' }}</td>
                        <td>
                            @if ($account->type == 'warehouse')
                                <span>خزنة</span>
                            @elseif($account->type == 'partner')
                                <span>شريك</span>
                            @elseif($account->type == 'place')
                                <span>المكان</span>
                            @elseif($account->type == 'owner')
                                <span>صاحب المكان</span>
                            @else
                                <span>المكن</span>
                            @endif
                        </td>
                        <td>{{ number_format($account->total_profit_balance) }} EGP</td>
                        <td>{{ number_format($account->current_balance) }} EGP</td>
                        <td>
                            <a href="{{ route('account.show', $account->id) }}" class="btn btn-info waves-effect">
                                <i data-feather='eye'></i>
                                <span>كشف حساب</span>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </table>            
        </div>
    </div>
</div>

@include('accounts.models')
@endsection

