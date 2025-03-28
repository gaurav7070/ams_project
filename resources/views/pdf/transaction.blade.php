<!DOCTYPE html>
<html>
<head>
    <title>Transaction Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Transaction Report</h2>
    <table>
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>Amount</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->id }}</td>
                    <td>{{ $transaction->amount }}</td>
                    <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d-m-Y H:i A') }}</td>       
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
