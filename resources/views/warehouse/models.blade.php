<!-- model Add warehouse-->
<div class="modal fade text-start modal-success" id="addWarehouse" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إضافة خزنة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('warehouse.store') }}" class="formSubmit" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label" for="name">اسم الخزنة</label>
                        <input type="text" class="form-control" name="name">
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="name">هل هي افتراضية (يدفع منها المصروفات تلقائياً)</label>
                        <select name="is_default" class="form-select">
                            <option value="1">نعم</option>
                            <option value="0">لا</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btnSubmit btn btn-success waves-effect waves-float waves-light">حفظ البيانات</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- model edit warehouse-->
<div class="modal fade text-start modal-success" id="editWarehouse" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إضافة خزنة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('warehouse.update') }}" class="formSubmit" method="POST">
                @csrf
                <input type="hidden" name="warehouse_id" class="warehouse_id">
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label" for="name">اسم الخزنة</label>
                        <input type="text" class="form-control name" name="name">
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="name">هل هي افتراضية (يدفع منها المصروفات تلقائياً)</label>
                        <select name="is_default" class="form-select is_default">
                            <option value="0" selected>لا</option>
                            <option value="1">نعم</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btnSubmit btn btn-success waves-effect waves-float waves-light">حفظ البيانات</button>
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
            <form action="{{ route('warehouse.transfer') }}" class="formSubmit" method="POST">
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
                    <button type="submit" class="btnSubmit btn btn-success waves-effect waves-float waves-light">حفظ البيانات</button>
                </div>
            </form>
        </div>
    </div>
</div>