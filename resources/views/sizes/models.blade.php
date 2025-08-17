<!-- model Add size -->
<div class="modal fade text-start modal-success" id="addSize" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إضافة مقاس</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('size.store') }}" class="formSubmit" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">المقاس</label>
                        <input type="number" class="form-control width" name="width">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btnSubmit btn btn-success waves-effect waves-float waves-light">حفظ البيانات</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- model edit size -->
<div class="modal fade text-start modal-success" id="editSize" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تعديل المقاس</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('size.update') }}" class="formSubmit" method="POST">
                @csrf
                <input type="hidden" name="id" class="id">
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">المقاس</label>
                        <input type="number" class="form-control width" name="width" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btnSubmit btn btn-success waves-effect waves-float waves-light">حفظ البيانات</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- model delete size -->
<div class="modal fade text-start modal-danger" id="delSize" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تحذير !</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('size.delete') }}"  class="submitDel" method="POST">
                @csrf
                <input type="hidden" name="id" class="id">
                <div class="modal-body">
                    <label class="form-label">هل أنت متأكد من حذف هذا العنصر ؟</label>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btnSubmit btn btn-danger waves-effect waves-float waves-light">تأكيد الحذف</button>
                </div>
            </form>
        </div>
    </div>
</div>
