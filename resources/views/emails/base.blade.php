<!DOCTYPE html>
<html>
<head>
    <title>Verify Email</title>
</head>
<body>
    <h1>Message from {{ config('app.name') }}</h1>
    <p>{{message}}</p>
    <p>
        Thanks from,
        <a href="{{ config('app.url') }}">  {{ config('app.name') }}</a>
    </p>

</body>
</html>
