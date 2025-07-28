<table>
    <thead>
        <tr>
            <th>اسم المورد</th>
            <th>الرصيد</th>
            <th>رقم الهاتف</th>
        </tr>
    </thead>
    <tbody>
        @foreach($suppliers as $supplier)
            <tr>
                <td>{{ $supplier->name }}</td>
                <td>{{ optional($supplier->account)->total_capital_balance ?? 0 }}</td>
                <td>{{ $supplier->phone ?? 0 }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
