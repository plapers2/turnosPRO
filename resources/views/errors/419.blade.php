<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="3;url={{ route('login') }}">
    <title>Sesión expirada</title>
</head>

<body>
    <script>
        window.location.href = "{{ route('login') }}";
    </script>
</body>

</html>