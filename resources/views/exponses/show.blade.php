@extends('layouts.app')

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">{{ $expenseItem->name }}</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('expenses.items') }}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="#">عرض الحركات</a>
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col">
                <div class="card-balance">
                    <h3>مجموع المصروفات</h3>
                    <h4>{{ number_format($expenseItem->exponses->sum('amount'), 2) }} EGP</h4>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">حركات البند : {{ $expenseItem->name }}</h3>
        <div class="card-action">
            <button href="#"
            class="paymentBtn btn btn-icon btn-primary waves-effect waves-float waves-light"
            data-bs-toggle="modal" data-bs-target="#paymentModel"
            >
                <i data-feather='credit-card'></i>
                <span>حركة دفع</span>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>المصدر</th>
                        <th>البيان</th>
                        <th>المبلغ</th>
                        <th>الكود المرجعي</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenseItem->exponses as $ex)
                        <tr>
                            @php
                                $typeName = class_basename($ex->expenseable_type);
                            @endphp
                            <td>{{ $ex->date }}</td>
                            <td>
                                @if ($typeName === 'Supplier_invoice')
                                    <span>فاتورة شراء</span>
                                @elseif($typeName === 'Wallet')
                                    <span>{{ $ex->expenseable->name }}</span>
                                @else 
                                    <span>فاتورة بيع</span>
                                @endif
                            </td>
                            <td>{{ $ex->note }}</td>
                            <td>{{ $ex->amount }}</td>
                            <td>
                                @if ($ex->source_code !== null)
                                    @if ($typeName === 'Supplier_invoice')
                                        <a href="{{ route('supplier.invoice.show', $ex->source_code) }}">
                                            {{ $ex->source_code }}
                                        </a>
                                    @else 
                                        <a href="{{ route('customer.invoice.show', $ex->source_code) }}">
                                            {{ $ex->source_code }}
                                        </a>
                                    @endif
                                @else
                                    _
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">لا توجد اى حركات لهذا البند.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $exponses->links() }}
    </div>
</div>

<!-- model payment -->
<div class="modal fade text-start modal-success" id="paymentModel" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('expenses.item.payment') }}" class="formSubmit" method="POST">
                @csrf
                <input type="hidden" value="{{ $expenseItem->id }}" name="expenseItemId">
                <div class="modal-body">
                    <!-- اختيار الخزنة -->
                    <div class="mb-2">
                        <label class="form-label">اختر الخزنة *</label>
                        <select name="warehouse_id" class="form-control warehouse_id">
                            <option value="">-- اختر الخزنة --</option>
                            @foreach ($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- اختيار محفظة -->
                    <div class="mb-2">
                        <label class="form-label">اختر محفظة *</label>
                        <select name="wallet_id" class="form-control wallet_id">
                            <option value="">...</option>
                        </select>
                    </div>

                    <!-- المبلغ -->
                    <div class="mb-2">
                        <label class="form-label">المبلغ *</label>
                        <input type="number" class="form-control amount" value="0" name="amount" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">البيان</label>
                        <textarea name="notes" class="form-control" cols="5" rows="5"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btnSubmit btn btn-success waves-effect waves-float waves-light">
                        حفظ البيانات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@section('js')
<script>
      $(document).on('change', '.warehouse_id', function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('getWallets') }}",
                method: 'POST',
                data: {
                    warehouse_id: $(this).val() 
                },
                success: function (response) {
                    $('.wallet_id').empty();
                    $('.wallet_id').append(`<option value="">-- اختر محفظة --</option>`)
                    $.each(response.data, function(index, item){
                        $('.wallet_id').append(`<option value="${item.id}" data-balance="${item.current_balance}" data-method="${item.method}">${item.name}</option>`)
                    })
                },
                error: function(xhr){
                    console.log(xhr);
                },
            });
        })

        $(document).on('submit', '.formSubmit', function(e){
            e.preventDefault();
            let warehouse = $(this).find('.warehouse_id').val();
            let wallet = $(this).find('.wallet_id').val();
            let amount = $(this).find('.amount').val();
            if (!warehouse || !wallet || !amount || amount <= 0) {
                toastr.info('يرجى ملء جميع الحقول المطلوبة بشكل صحيح!');
                return; // إيقاف الإرسال
            }
            else {
                $(this).find('.btnSubmit').prop('disabled', true).addClass('disabled');
                this.submit();
            }
        });
</script>
@endsection