<table class="table table-bordered">
    <tr>
        <th>رقم العميل</th>
        <th>اسم العميل</th>
        <th>اسم الشركة</th>
        <th>رقم الهاتف</th>
        <th>رقم الواتساب</th>
        <th>حالة العميل</th>
        <th>تاريخ التواصل</th>
        <th>تحديث الحالة</th>
        <th>إجراءات</th>
    </tr>
    @forelse ($customers as $c)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>
                <a href="{{ route('customer.show', $c->id) }}">
                    {{ $c->name }}
                </a>
            </td>
            <td>{{ $c->business_name }}</td>
            <td>{{ $c->phone }}</td>
            <td>{{ $c->whatsUp }}</td>
            <td>
                @if ($c->statue == 0)
                    <span>محتمل</span>
                @elseif($c->statue == 1)
                    <span>مؤكد</span>
                @else 
                    <span>سقط</span>
                @endif
            </td>
            <td>{{ $c->date }}</td>
            <td>{{ $c->last_Interaction_date == null ? 'لا يوجد' : $c->last_Interaction_date}}</td>
            <td class="d-flex gap-1">
                <a href="{{ route('customer.edit', $c->id) }}" class="btn btn-icon btn-success waves-effect waves-float waves-light editBtn">
                    <i data-feather='edit'></i>
                </a>
                <a href="#" data-id="{{ $c->id }}" class="btn btn-icon btn-danger waves-effect waves-float waves-light delBtn" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i data-feather='trash-2'></i>
                </a>
            </td>
        </tr>
    @empty
        <tr class="text-center">
            <td colspan="8">لا يوجد عملاء حتي الآن</td>
        </tr>
    @endforelse
</table>