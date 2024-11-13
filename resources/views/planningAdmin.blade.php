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
        .column-date { width: 6%; }
    </style>
</head>
<body>

    <h1>Defense Schedule</h1>

    <table>
        <thead>
            <tr style="background:#52A063; color:#fff; font-size:12px;">
                <th>Cin</th>
                <th>Etudiants</th>
                <th>Status</th>
                <th>Encadrant</th>
                <th>President</th>
                <th>Email President</th>
                <th>Rapporteur</th>
                <th>Email Rapporteur</th>
            </tr>
        </thead>
        <tbody>
            @foreach($scheduleAdmin as $defense)
                <tr style="background:#c6da81; color:#fff; font-size:12px">
                    <td>{{ $defense['cin'] }}</td>
                    <td>{{ $defense['students'] }}</td>
                    <td>{{ $defense['status'] }}</td>
                    <td>{{ $defense['encadrant'] }}</td>
                    <td>{{ $defense['president'] }}</td>
                    <td>{{ $defense['email-president'] }}</td>
                    <td>{{ $defense['rapporteur'] }}</td>
                    <td>{{ $defense['email-rapporteur'] }}</td>
                    
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
