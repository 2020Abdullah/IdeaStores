<!-- add task -->
<div class="modal modal-slide-in fade" id="addTask" aria-modal="true" role="dialog">
    <div class="modal-dialog">
        <form method="POST" class="modal-content pt-0" action="{{ route('addTask') }}" autocomplete="off">
            @csrf
            <input type="hidden" name="customer_id" value="{{ $customer->id }}">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
            <div class="modal-header mb-1">
                <h5 class="modal-title" id="exampleModalLabel">إضافة تاسك جديد</h5>
            </div>
            <div class="modal-body flex-grow-1">
                <div class="col-md-12 col-sm-12 mb-2">
                    <label class="form-label">اسم العميل</label>
                    <input type="text" class="form-control disabled" readonly value="{{ $customer->name }}">
                </div>
                <div class="col-md-12 col-sm-12 mb-2">
                    <label class="form-label">رقم الهاتف</label>
                    <input type="text" class="form-control disabled" readonly value="{{ $customer->phone }}">
                </div>
                <div class="col-md-12 col-sm-12 mb-2">
                    <label class="form-label">طلب العميل</label>
                    <textarea class="form-control disabled" cols="5" rows="5" readonly>{{ $customer->notes }}</textarea>
                </div>
                <div class="mb-1">
                    <label class="form-label">اسم التاسك</label>
                    <input type="text" class="form-control" name="name">
                </div>
                <div class="mb-1">
                    <label class="form-label">نوع التاسك</label>
                    <select name="task_type" class="form-select" id="task_type">
                        <option value="1">متابعة عملية الشراء</option>
                        <option value="2">متابعة خدمة ما بعد البيع</option>
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label">طريقة المتابعة</label>
                    <div class="form-check form-check-primary mb-1 mt-1">
                        <input type="checkbox" name="follow_type[]" value="phone" class="form-check-input" id="phone">
                        <label class="form-check-label" for="phone">الهاتف</label>
                    </div>
                    <div class="form-check form-check-primary">
                        <input type="checkbox" name="follow_type[]" value="whatsUp" class="form-check-input" id="whatsUp">
                        <label class="form-check-label" for="whatsUp">واتساب</label>
                    </div>
                </div>
                <div class="mb-1">
                    <label class="form-label">تاريخ المتابعة</label>
                    <input type="date" class="form-control" name="follow_date">
                </div>
                <div class="mb-1">
                    <label class="form-label">ملاحظات</label>
                    <textarea name="notes" class="form-control" id="notes" cols="5" rows="5"></textarea>
                </div>
                <button type="submit" class="btn btn-success me-1 data-submit waves-effect waves-float waves-light">حفظ البيانات</button>
                <button type="reset" class="btn btn-outline-secondary waves-effect" data-bs-dismiss="modal">إلغاء</button>
            </div>
        </form>
    </div>
</div>

<!-- edit task -->
<div class="modal modal-slide-in fade" id="editTask" aria-modal="true" role="dialog">
    <div class="modal-dialog">
        <form method="POST" class="modal-content pt-0" action="{{ route('updateTask') }}" autocomplete="off">
            @csrf
            <input type="hidden" name="task_id">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
            <div class="modal-header mb-1">
                <h5 class="modal-title" id="exampleModalLabel">تعديل التاسك</h5>
            </div>
            <div class="modal-body flex-grow-1">
                <div class="col-md-12 col-sm-12 mb-2">
                    <label class="form-label">اسم العميل</label>
                    <input type="text" class="form-control disabled" readonly value="{{ $customer->name }}">
                </div>
                <div class="col-md-12 col-sm-12 mb-2">
                    <label class="form-label">رقم الهاتف</label>
                    <input type="text" class="form-control disabled" readonly value="{{ $customer->phone }}">
                </div>
                <div class="col-md-12 col-sm-12 mb-2">
                    <label class="form-label">طلب العميل</label>
                    <textarea class="form-control disabled" cols="5" rows="5" readonly>{{ $customer->notes }}</textarea>
                </div>
                <div class="mb-1">
                    <label class="form-label">اسم التاسك</label>
                    <input type="text" class="form-control" name="name">
                </div>
                <div class="mb-1">
                    <label class="form-label">نوع التاسك</label>
                    <select name="task_type" class="form-select" id="task_type">
                        <option value="1">متابعة عملية الشراء</option>
                        <option value="2">متابعة خدمة ما بعد البيع</option>
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label">طريقة المتابعة</label>
                    <div class="form-check form-check-primary mb-1 mt-1">
                        <input type="checkbox" name="follow_type[]" value="phone" class="form-check-input" id="phone">
                        <label class="form-check-label" for="phone">الهاتف</label>
                    </div>
                    <div class="form-check form-check-primary">
                        <input type="checkbox" name="follow_type[]" value="whatsUp" class="form-check-input" id="whatsUp">
                        <label class="form-check-label" for="whatsUp">واتساب</label>
                    </div>
                </div>
                <div class="mb-1">
                    <label class="form-label">تاريخ المتابعة</label>
                    <input type="date" class="form-control" name="follow_date">
                </div>
                <div class="mb-1">
                    <label class="form-label">ملاحظات</label>
                    <textarea name="notes" class="form-control" id="notes" cols="5" rows="5"></textarea>
                </div>
                <button type="submit" class="btn btn-success me-1 data-submit waves-effect waves-float waves-light">حفظ البيانات</button>
                <button type="reset" class="btn btn-outline-secondary waves-effect" data-bs-dismiss="modal">إلغاء</button>
            </div>
        </form>
    </div>
</div>

<!-- delete task -->
<div class="modal fade" id="deleteTask" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">تنبيه هام !</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('deleteTask') }}">
                @csrf
                <input type="hidden" name="id" class="id">
                <div class="modal-body">
                    <div class="modal-body">
                        <div class="mb-1">
                            <p>هل تريد بالفعل حذف هذا التاسك ؟</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger me-1 data-submit waves-effect waves-float waves-light">تأكيد</button>
                    <button type="reset" class="btn btn-outline-secondary waves-effect" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- add order -->
<div class="modal modal-slide-in fade" id="addOrder" aria-modal="true" role="dialog">
    <div class="modal-dialog">
        <form method="POST" class="modal-content pt-0" action="{{ route('order.store') }}" autocomplete="off">
            @csrf
            <input type="hidden" name="customer_id" value="{{ $customer->id }}">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
            <div class="modal-header mb-1">
                <h5 class="modal-title">إضافة طلب جديد</h5>
            </div>
            <div class="modal-body flex-grow-1">
                <div class="mb-1">
                    <label class="form-label">الطلب</label>
                    <textarea name="order_info" class="form-control" id="order_info" cols="5" rows="5"></textarea>
                </div>
                <div class="mb-1">
                    <label class="form-label">طريقة الإستلام</label>
                    <select name="Receipt_method" class="form-select" id="Receipt_method">
                        <option value="shipping">شحن</option>
                        <option value="store">استلام من مخازننا</option>
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label">تاريخ التوريد (اختيارى)</label>
                    <input type="date" class="form-control" name="order_date">
                </div>
                <div class="mb-1">
                    <label class="form-label">إجمالي السعر</label>
                    <input type="number" class="form-control" name="total_price">
                </div>
                <div class="mb-1">
                    <label class="form-label">حالة الطلب</label>
                    <select name="statue" class="form-select" id="statue">
                        <option value="progress">قيد التنفيذ</option>
                        <option value="complete">مكتمل</option>
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label">العمولة</label>
                    <input type="number" class="form-control" name="commission">
                </div>
                <div class="mb-1">
                    <label class="form-label">ملاحظات</label>
                    <textarea name="notes" class="form-control" id="notes" cols="5" rows="5"></textarea>
                </div>
                <button type="submit" class="btn btn-success me-1 data-submit waves-effect waves-float waves-light">حفظ البيانات</button>
                <button type="reset" class="btn btn-outline-secondary waves-effect" data-bs-dismiss="modal">إلغاء</button>
            </div>
        </form>
    </div>
</div>

<!-- edit order -->
<div class="modal modal-slide-in fade" id="editOrder" aria-modal="true" role="dialog">
    <div class="modal-dialog">
        <form method="POST" class="modal-content pt-0" action="{{ route('order.update') }}" autocomplete="off">
            @csrf
            <input type="hidden" name="customer_id" value="{{ $customer->id }}">
            <input type="hidden" name="id" class="id">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
            <div class="modal-header mb-1">
                <h5 class="modal-title">تعديل بيانات الطلب</h5>
            </div>
            <div class="modal-body flex-grow-1">
                <div class="mb-1">
                    <label class="form-label">الطلب</label>
                    <textarea name="order_info" class="form-control" cols="5" rows="5"></textarea>
                </div>
                <div class="mb-1">
                    <label class="form-label">طريقة الإستلام</label>
                    <select name="Receipt_method" class="form-select Receipt_method">
                        <option value="shipping">شحن</option>
                        <option value="store">استلام من مخازننا</option>
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label">تاريخ التوريد (اختيارى)</label>
                    <input type="date" class="form-control" name="order_date">
                </div>
                <div class="mb-1">
                    <label class="form-label">إجمالي السعر</label>
                    <input type="number" class="form-control" name="total_price">
                </div>
                <div class="mb-1">
                    <label class="form-label">حالة الطلب</label>
                    <select name="statue" class="form-select" id="statue">
                        <option value="progress">قيد التنفيذ</option>
                        <option value="complete">مكتمل</option>
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label">العمولة</label>
                    <input type="number" class="form-control" name="commission">
                </div>
                <div class="mb-1">
                    <label class="form-label">ملاحظات</label>
                    <textarea name="notes" class="form-control" id="notes" cols="5" rows="5"></textarea>
                </div>
                <button type="submit" class="btn btn-success me-1 data-submit waves-effect waves-float waves-light">حفظ البيانات</button>
                <button type="reset" class="btn btn-outline-secondary waves-effect" data-bs-dismiss="modal">إلغاء</button>
            </div>
        </form>
    </div>
</div>

<!-- delete order -->
<div class="modal fade modal-danger text-start" id="deleteOrder" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">تنبيه هام !</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('order.delete') }}">
                @csrf
                <input type="hidden" name="id" class="id">
                <div class="modal-body">
                    <div class="mb-1">
                        <p>هل تريد بالفعل حذف هذا الطلب ؟</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger me-1 data-submit waves-effect waves-float waves-light">تأكيد</button>
                    <button type="reset" class="btn btn-outline-secondary waves-effect" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- show notes order -->
<div class="modal fade modal-info text-start" id="showNotes" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ملاحظات</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <textarea class="form-control notes" name="notes" cols="5" rows="5">لا يوجد</textarea>
            </div>
            <div class="modal-footer">
                <button type="reset" class="btn btn-outline-secondary waves-effect" data-bs-dismiss="modal">حسناً</button>
            </div>
        </div>
    </div>
</div>
