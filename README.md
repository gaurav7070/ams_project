AMS Project(Account Management System)

This is a Laravel-based Account Management System (AMS) that uses PostgreSQL as the database. Follow the steps below to set up and run the project.

🚀 Installation Guide

1️⃣ Clone the Repository

git clone <repository-url>
cd <project-directory>

2️⃣ Configure the .env File

Update the database connection details in the .env file:

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=ams_project
DB_USERNAME=postgres
DB_PASSWORD=123456

3️⃣ Clear and Rebuild Configurations

Run the following commands to refresh the configuration and cache:

php artisan config:clear       # Clear config cache
php artisan cache:clear        # Clear application cache
php artisan route:clear        # Clear route cache
php artisan config:cache       # Rebuild config cache
php artisan route:cache        # Rebuild route cache
php artisan optimize:clear     # Clear all caches

4️⃣ Run Database Migrations

php artisan migrate

Run optimize:clear again to ensure a clean state:

php artisan optimize:clear

5️⃣ Start the Development Server

php artisan serve

The project will now be accessible at:
🔗 http://127.0.0.1:8000/

👤 Creating an Account & Logging In

1️⃣ Create a new account using the registration form.2️⃣ Login using your login_id and password (set during account creation).

❓ Need Help?

If you have any questions, feel free to reach out.

👤 Gaurav Kumar


API (url and payload)
Account Endpoints:
- POST /api/accounts -> Create a new account
url: http://127.0.0.1:8000/api/register
payload:
{
    "first_name": "John",
    "last_name": "Doe",
    "login_id": "john_doe123",
    "email": "john@example.com",
    "password": "12345678",
    "password_confirmation": "12345678",
    "account_type": "Business",
    "currency": "GBP"
}

- GET /api/accounts/{account_number} -> Fetch account details
url: http://127.0.0.1:8000/api/account-details
payload:
{
    "account_number": "880596845771"
}



- PUT /api/accounts/{account_number} -> Update account details
url: http://127.0.0.1:8000/api/updateAccountAndUser
payload:{
    "account_number": "880596845771",
    "account_name": "New Account Name",
    "account_type": "Business",
    "currency": "GBP",
    "balance": 5000.00,
    "modified_by": "admin_user",
    "first_name": "Updated First Name",
    "last_name": "Updated Last Name",
    "email": "updated@example.com",
    "login_id": "updated_login_id"
}




- DELETE /api/accounts/{account_number} -> Permanent account
url: http://127.0.0.1:8000/api/deleteUser
payload:
{
    "account_number": "880596845771"
}


Transaction Endpoints:

- POST /api/transactions -> Log a transaction (Credit/Debit)
url: http://127.0.0.1:8000/api/updateTransaction
payload:
{
    "account_number": "123456789012",
    "transaction_id": "abcd-1234-efgh-5678",
    "type": "debit",
    "amount": 250.00,
    "description": "Payment for services",
    "modified_by": "admin123"
}


- GET /api/transactions?account_id=X&from=YYYY-MM-DD&to=YYYY-MM-DD -> Get transactions
url: http://127.0.0.1:8000/api/transactions
payload :{
    "account_number": "880596845771",
    "from": "2025-03-28 00:00:03",
    "to": "2025-03-28 23:59:59"
}
