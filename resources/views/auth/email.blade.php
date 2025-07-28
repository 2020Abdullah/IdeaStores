<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>إعادة تعيين كلمة المرور</title>
</head>
<body style="direction: rtl; font-family: Arial, sans-serif;">

    <h2>مرحباً {{ $user->name ?? 'مستخدم' }}</h2>

    <p>لقد تلقينا طلباً لإعادة تعيين كلمة المرور الخاصة بحسابك.</p>

    <p>اضغط على الزر التالي لإعادة تعيين كلمة المرور:</p>

    <p style="text-align: center;">
        <a href="{{ $link }}" style="padding: 10px 20px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 5px;">
            إعادة تعيين كلمة المرور
        </a>
    </p>

    <p>إذا لم تطلب هذا، لا داعي لاتخاذ أي إجراء.</p>

</body>
</html>
