<!-- model Add cate -->
<div class="modal fade text-start modal-success" id="addcate" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إضافة تصنيف جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('category.store') }}" class="formSubmit" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">اسم التصنيف</label>
                        <input type="text" class="form-control" name="name" placeholder="مثال: سيور">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">نوع التصنيف</label>
                        <select name="parent_id" class="form-select parent_id">
                            <option value="">تصنيف رئيسي</option>
                            @foreach ($category_list as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
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

<!-- model edit cate -->
<div class="modal fade text-start modal-success" id="editcate" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تعديل التصنيف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('category.update') }}" class="formSubmit" method="POST">
                @csrf
                <input type="hidden" name="id" class="id">
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">اسم التصنيف</label>
                        <input type="text" class="form-control name" name="name" placeholder="مثال: سيور">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">نوع التصنيف</label>
                        <select name="parent_id" class="form-select">
                            <option value="">تصنيف رئيسي</option>
                            @foreach ($category_list as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
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

<!-- model delete cate -->
<div class="modal fade text-start modal-danger" id="delcate" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تحذير !</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('category.delete') }}" class="submitDel" method="POST">
                @csrf
                <input type="hidden" name="id" class="id">
                <div class="modal-body">
                    <label class="form-label">هل أنت متأكد من حذف هذا التصنيف سيتم حذف جميع أنواعه أيضاً ؟</label>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger waves-effect waves-float waves-light">تأكيد الحذف</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- model import data -->
<div class="modal fade text-start modal-success" id="importData" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">استيراد البيانات</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('import.categories') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" class="id">
                <div class="modal-body">
                    <label class="form-label">استيراد من شيت اكسيل</label>
                    <input type="file" class="form-control" name="file" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger waves-effect waves-float waves-light">استيراد البيانات</button>
                </div>
            </form>
        </div>
    </div>
</div>