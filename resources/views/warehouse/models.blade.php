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

<!-- model Add balance-->
<div class="modal fade text-start modal-success" id="addBalance" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إضافة خزنة فرعية</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('warehouse.store') }}" method="POST">
                @csrf
                <input type="hidden" name="method" class="method">
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
                    <div class="mb-2">
                        <label class="form-label" for="balance_start">رصيد أول المدة</label>
                        <input type="number" class="form-control" name="balance_start" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success waves-effect waves-float waves-light">حفظ البيانات</button>
                </div>
            </form>
        </div>
    </div>
</div>