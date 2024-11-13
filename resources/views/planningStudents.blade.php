<!DOCTYPE html>
<html>
<head>
    <title>Defense Schedule</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 0.5px solid rgb(218, 218, 218);
            
        }
        th, td {
            padding: 2px;
            text-align: left;
            font-family: 'lato';
        }
    </style>
</head>
<body>

    <h1>Defense Schedule</h1>

    <table>
        <thead>
            <tr style="background:#ff8600; color:#fff; font-size:12px;">
                <th>Date</th>
                <th>Heure</th>
                <th>Salle</th>
                <th>Etudiants</th>
                <th>Encadrant</th>
                <th>President</th>
                <th>Rapporteur</th>
            </tr>
        </thead>
        <tbody>
            @foreach($scheduleStudents as $defense)
                <tr style="background:#f6a230; color:#fff; font-size:12px">
                    <td>{{ $defense['date']}}</td>
                    <td>{{ $defense['time'] }}</td>
                    <td>{{ $defense['classroom'] }}</td>
                    <td>{{ $defense['students'] }}</td>
                    <td>{{ $defense['encadrant'] }}</td>
                    <td>{{ $defense['president'] }}</td>
                    <td>{{ $defense['rapporteur'] }}</td>
                </tr>
            @endforeach
        </tbody>  
    </table>

</body>
</html>
