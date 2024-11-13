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
            <tr style="background:#5966b1; color:#fff; font-size:12px;">
                <th class="column-date">Date</th>
                <th>Salle</th>
                <th>Cin</th>
                <th>Etudiants</th>
                <th>Titre</th>
                <th>Emails</th>
                <th>Spécialité</th>
                <th>Encadrant</th>
                <th>President</th>
                <th>Rapporteur</th>
            </tr>
        </thead>
        <tbody>
            @foreach($scheduleTeachers as $defense)
                <tr style="background:#76b3d0ca; color:#fff; font-size:12px">
                    <td>{{ $defense['date'] }}<br>
                       <strong>{{ $defense['time'] }}</strong></td>
                    <td>{{ $defense['classroom'] }}</td>
                    <td>{{ $defense['cins'] }}</td>
                    <td>{{ $defense['students'] }}</td>
                    <td>{{ $defense['title'] }}</td>
                    <td>{{ $defense['emails'] }}</td>
                    <td>{{ $defense['specialty'] }}</td>
                    <td>{{ $defense['encadrant'] }}<br>
                        {{ $defense['email-encadrant'] }}
                    </td>
                    <td>{{ $defense['president'] }}<br>
                        {{ $defense['email-president'] }}
                    </td>
                    <td>{{ $defense['rapporteur'] }}<br>
                        {{ $defense['email-rapporteur'] }}</td>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
