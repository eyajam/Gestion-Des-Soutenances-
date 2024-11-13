<!DOCTYPE html>
<html>
<head>
<h1>Unsupervised Students List</h1>
</head>
<body>
<ul>
    @foreach ($students as $student)
        <li>CIN : {{ $student->cin }} - {{ $student->first_name }} {{ $student->last_name }} - {{ $student->specialty }}</li>
    @endforeach
</ul>
</body>

</html>