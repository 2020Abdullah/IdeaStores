<!-- model Add account -->
<div class="modal fade text-start modal-success" id="addAccount" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إضافة حساب جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('category.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">اسم الحساب</label>
                        <input type="text" class="form-control" name="name" placeholder="اسم الحساب">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">نوع الحساب</label>
                        <select name="account_type" class="form-select">
                            <option value="partner">حساب شريك</option>
                            <option value="place">المكان</option>
                            <option value="owner">صاحب المكان</option>
                            <option value="machine">المكن</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success waves-effect waves-float waves-light">حفظ الحساب</button>
                </div>
            </form>
        </div>
    </div>
</div>

