<!DOCTYPE html>
<html>

<head>
    <style>
        #customers {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        #customers td,
        #customers th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        #customers tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #customers tr:hover {
            background-color: #ddd;
        }

        #customers th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #5f6865;
            color: white;
        }
    </style>
</head>

<body>
    <table id="customers">
        <thead>
        </thead>
        <tbody>
            <tr>
                <td>name</td>
                <td>{{ $contactUs->name }}</td>
            </tr>

            <tr>
                <td>E-mail </td>
                <td>{{ $contactUs->email }}</td>
            </tr>
            <tr>
                <td>phone </td>
                <td>{{ $contactUs->phone }}</td>
            </tr>


            <tr>
                <td>address </td>
                <td>{{ $contactUs->address }}</td>
            </tr>

            <tr>
                <td>subject </td>
                <td>{{ $contactUs->subject }}</td>
            </tr>

            <tr>
                <td>message </td>
                <td>{{ $contactUs->message }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>
