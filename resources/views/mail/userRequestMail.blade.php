<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
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
            <tr>
                <th>Company Name</th>
                <th>{{ $user->name ?? 'N/A' }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>E-mail </td>
                <td>{{ $user->email ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Address</td>
                <td>{{ $user->address_line_one ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Phone </td>
                <td>{{ $user->phone ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Country</td>
                <td>{{ $user->country->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>City</td>
                <td>{{ $user->city ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>State</td>
                <td>{{ $user->state ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Website</td>
                <td>{{ $user->website ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Members Count</td>
                <td>{{ $user->members_count ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Postal Code</td>
                <td>{{ $user->postal_code ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Year Business Was Established</td>
                <td>{{ $user->business_est ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Profile</td>
                <td>{{ $user->profile ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>FPP</td>
                <td>{{ $user->fpp ?? 'N/A' }}</td>
            </tr>

            @foreach (DB::table('contact_people')->where('user_id', $user->id)->get() as $contactPerson)
                <tr>
                    <td colspan="2"><strong>Contact Person</strong></td>
                </tr>
                <tr>
                    <td>Title</td>
                    <td>{{ $contactPerson->title }}</td>
                </tr>
                <tr>
                    <td>Full Name</td>
                    <td>{{ $contactPerson->first_name }} {{ $contactPerson->last_name }}</td>
                </tr>
                <tr>
                    <td>Job Title</td>
                    <td>{{ $contactPerson->job_title }}</td>
                </tr>
                <tr>
                    <td>E-mail</td>
                    <td>{{ $contactPerson->email }}</td>
                </tr>
                <tr>
                    <td>Direct Phone</td>
                    <td>{{ $contactPerson->phone_number }}</td>
                </tr>
                <tr>
                    <td>Cell Number</td>
                    <td>{{ $contactPerson->cell_number }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
