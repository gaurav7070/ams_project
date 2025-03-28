<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 50px;
            background: url("{{ asset('images/image2.jpg') }}") no-repeat center center fixed;
            background-size: cover;
        }

        .container {
            width: 300px;
            margin: auto;
            background: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            text-decoration: none;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .btn-logout {
            background-color: red;
        }

        .btn-logout:hover {
            background-color: darkred;
        }

        /* Popup Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            border-radius: 10px;
            width: 60%;
            text-align: left;
        }

        .close {
            color: red;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #f4f4f4;
        }

        .download-btn {
            float: right;
            background-color: green;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .btn-submit {
            background-color: #28a745;
        }

        .btn-submit:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Dashboard</h2>
        <button class="btn" onclick="openProfileModal()">Profile</button>
        <button class="btn" onclick="openTransactionModal()">Transaction</button>
        <button class="btn" onclick="openAddTransactionModal()">Add Transaction</button>
        <button class="btn" onclick="openComplaintModal()">Complaint</button>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-logout">Logout</button>
        </form>
    </div>

    <!-- Profile Popup Modal -->
    <div id="profileModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeProfileModal()">&times;</span>
            <h3>User Profile</h3>

            <form id="profileForm" method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name">
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name">
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email">
                </div>

                <div class="form-group">
                    <label for="account_number">Account Number:</label>
                    <input type="text" id="account_number" name="account_number" readonly>
                </div>

                <button type="submit" class="btn btn-submit">Update Profile</button>
            </form>
        </div>
    </div>


    <!-- Transaction Popup Modal -->
    <div id="transactionModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeTransactionModal()">&times;</span>
            <h3>Transactions</h3>
            <a href="{{ route('download.pdf') }}" class="download-btn">Download PDF</a>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Transaction ID</th>
                        <th>Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($transactions) && $transactions->count() > 0)
                    @foreach($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->id }}</td>
                        <td>{{ $transaction->transaction_id }}</td>
                        <td>{{ $transaction->amount }}</td>
                        <td>{{ $transaction->transaction_date }}</td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="4">No transactions available.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Transaction Popup Modal -->
    <div id="addTransactionModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAddTransactionModal()">&times;</span>
            <h3>Add Transaction</h3>
            <form method="POST" action="{{ route('transaction.store') }}">
                @csrf
                <div class="form-group">
                    <label for="account_id">Account ID:</label>
                    <input type="text" id="account_id" name="account_id" required readonly>
                </div>
                <div class="form-group">
                    <label for="transaction_type">Transaction Type:</label>
                    <select id="transaction_type" name="transaction_type" required>
                        <option value="Credit">Credit (Add Money)</option>
                        <option value="Debit">Debit (Deduct Money)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="amount">Amount:</label>
                    <input type="number" id="amount" name="amount" min="0.01" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="description">Description (Optional):</label>
                    <textarea id="description" name="description" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-submit">Save Transaction</button>
            </form>
        </div>
    </div>


    <!-- Complaint Popup Modal -->
    <div id="complaintModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeComplaintModal()">&times;</span>
            <h3>Register Complaint</h3>
            <form method="POST" action="{{ route('complaint.store') }}">
                @csrf
                <div class="form-group">
                    <label for="transaction_id">Transaction ID:</label>
                    <input type="text" id="transaction_id" name="transaction_id" required>
                </div>
                <div class="form-group">
                    <label for="complaint_description">Complaint Description:</label>
                    <textarea id="complaint_description" name="complaint_description" rows="4" required></textarea>
                </div>
                <div class="form-group">
                    <label for="transaction_date">Transaction Date:</label>
                    <input type="date" id="transaction_date" name="transaction_date" required>
                </div>
                <button type="submit" class="btn btn-submit">Save Complaint</button>
            </form>
        </div>
    </div>

    <script>
        function openProfileModal() {
            fetch("{{ route('profile.get') }}")
                .then(response => response.json())
                .then(data => {
                    document.getElementById("first_name").value = data.user.first_name;
                    document.getElementById("last_name").value = data.user.last_name;
                    document.getElementById("email").value = data.user.email;
                    document.getElementById("account_number").value = data.account.account_number;

                    document.getElementById("profileModal").style.display = "block";
                })
                .catch(error => console.error('Error fetching profile:', error));
        }

        function closeProfileModal() {
            document.getElementById("profileModal").style.display = "none";
        }

        function openTransactionModal() {
            fetch("{{ route('transactions') }}")
                .then(response => response.json())
                .then(data => {
                    let transactionTableBody = document.querySelector("#transactionModal tbody");
                    transactionTableBody.innerHTML = ""; 

                    if (data.transactions.length > 0) {
                        data.transactions.forEach(transaction => {
                            let row = `
                        <tr>
                            <td>${transaction.id}</td>
                            <td>${transaction.account_id}</td>
                            <td>${transaction.amount}</td>
                            <td>${new Date(transaction.created_at).toLocaleString('en-IN', { 
    day: '2-digit', 
    month: '2-digit', 
    year: 'numeric', 
    hour: '2-digit', 
    minute: '2-digit', 
    hour12: true 
})}</td>

                        </tr>
                    `;
                            transactionTableBody.innerHTML += row;
                        });
                    } else {
                        transactionTableBody.innerHTML = "<tr><td colspan='4'>No transactions available.</td></tr>";
                    }

                    document.getElementById("transactionModal").style.display = "block";
                })
                .catch(error => console.error('Error fetching transactions:', error));
        }


        function closeTransactionModal() {
            document.getElementById("transactionModal").style.display = "none";
        }

        function openAddTransactionModal() {
            fetch("{{ route('profile.get') }}")
                .then(response => response.json())
                .then(data => {
                    document.getElementById("account_id").value = data.account.id;
                    document.getElementById("addTransactionModal").style.display = "block";
                })
                .catch(error => console.error('Error fetching account data:', error));
        }

        function closeaddTransactionModal() {
            document.getElementById("addTransactionModal").style.display = "none";
        }

        function openComplaintModal() {
            document.getElementById("complaintModal").style.display = "block";
        }

        function closeComplaintModal() {
            document.getElementById("complaintModal").style.display = "none";
        }
    </script>
</body>

</html>