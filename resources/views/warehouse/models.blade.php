<!-- model Add warehouse-->
<div class="modal fade text-start modal-success" id="addWarehouse" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إضافة خزنة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('warehouse.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label" for="name">اسم الخزنة</label>
                        <input type="text" class="form-control" name="name">
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="name">نوع الخزنة</label>
                        <select name="type" class="form-select">
                            <option value="toridat">خزنة توريدات</option>
                            <option value="la7amat">خزنة لحامات</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success waves-effect waves-float waves-light">حفظ البيانات</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- model transfer balance-->
<div class="modal fade text-start modal-success" id="transfer" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تحويل رصيد إلي حساب آخر</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('warehouse.transfer') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">من حساب الخزنة</label>
                        <select name="warehouse_id_from" class="form-select">
                            <option value="" selected>اختر خزنة ...</option>
                            @foreach ($warehouse_list as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">من حساب المحفظة</label>
                        <select name="wallet_id_from" class="form-select">
                            <option value="" selected>اختر محفظة ...</option>
                            @foreach ($wallets_list as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">إلي حساب الخزنة</label>
                        <select name="warehouse_id_to" class="form-select">
                            <option value="" selected>اختر خزنة ...</option>
                            @foreach ($warehouse_list as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">إلي حساب المحفظة</label>
                        <select name="wallet_id_to" class="form-select">
                            <option value="" selected>اختر محفظة ...</option>
                            @foreach ($wallets_list as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">المبلغ</label>
                        <input type="text" class="form-control" name="balance">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">البيان</label>
                        <textarea name="notes" class="form-control" cols="5" rows="5"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success waves-effect waves-float waves-light">حفظ البيانات</button>
                </div>
            </form>
        </div>
    </div>
</div>