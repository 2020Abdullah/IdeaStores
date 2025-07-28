<!-- model Add -->
<div class="modal fade text-start modal-success" id="addUnits" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إضافة وحدة قياس</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('units.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">اسم الوحدة</label>
                        <input type="text" class="form-control" name="name" placeholder="مثال: متر">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">رمز الوحدة</label>
                        <input type="text" class="form-control" name="symbol" placeholder="مثال: سم">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success waves-effect waves-float waves-light">حفظ البيانات</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- model edit -->
<div class="modal fade text-start modal-success" id="editUnits" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تعديل وحدة قياس</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('units.update') }}" method="POST">
                @csrf
                <input type="hidden" name="id" class="id">
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">اسم الوحدة</label>
                        <input type="text" class="form-control name" name="name" placeholder="مثال: متر">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">رمز الوحدة</label>
                        <input type="text" class="form-control symbol" name="symbol" placeholder="مثال: سم">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success waves-effect waves-float waves-light">حفظ البيانات</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- model delete -->
<div class="modal fade text-start modal-danger" id="delUnits" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تحذير !</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('units.delete') }}" method="POST">
                @csrf
                <input type="hidden" name="id" class="id">
                <div class="modal-body">
                    <label class="form-label">هل أنت متأكد من حذف وحدة القياس ؟</label>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger waves-effect waves-float waves-light">تأكيد الحذف</button>
                </div>
            </form>
        </div>
    </div>
</div>