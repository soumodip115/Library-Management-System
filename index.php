<?php
include 'database_connection.php';
include 'function.php';

if (is_user_login()) {
    header('location:issue_book_details.php');
}

// include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: url('library.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: white;
        }

        .content {
            text-align: center;
            padding: 20px;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 15px;
            margin-bottom: 20px;
            animation: fadeIn 2s ease-in-out;
        }
        
        .box-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            animation: slideUp 2s ease-in-out;
        }

        .box {
            padding: 20px;
            border: 2px solid white;
            border-radius: 10px;
            width: 250px;
            text-align: center;
            background: rgba(0, 0, 0, 0.5);
        }

        .box h2 {
            margin-bottom: 20px;
        }
        .footer{
            background-color: #26282a;
        }
        .custom-btn {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 10px;
            text-decoration: none;
            color: #fff;
            border: 2px solid;
            border-radius: 5px;
            transition: background 0.3s, transform 0.3s;
        }

        .custom-btn.admin {
            border-color: #fff;
        }

        .custom-btn.admin:hover {
            background: #fff;
            color: #343a40;
            transform: scale(1.1);
        }

        .custom-btn.login {
            border-color: #6c757d;
        }

        .custom-btn.login:hover {
            background: #6c757d;
            color: #fff;
            transform: scale(1.1);
        }

        .custom-btn.signup {
            border-color: #007bff;
        }

        .custom-btn.signup:hover {
            background: #007bff;
            color: #fff;
            transform: scale(1.1);
        }

         footer {
            background: rgba(0, 0, 0, 0.7);
            color: white;
            text-align: center;
            padding: 10px 0;
            position: absolute;
            bottom: 0;
            width: 100%;
        } 

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                transform: translateY(100%);
            }
            to {
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="content">
        <h1 class="display-5 fw-bold">Library Management System</h1>
        <p class="fs-4">This is a simple Library Management System used to maintain the record of the library. This Library Management System has been made by using PHP script, MySQL Database, Vanilla JavaScript, and Bootstrap 5 framework. This is a PHP Project on Online Library Management System.</p>
    </div>

    <div class="box-container">
        <div class="box">
            <h2>Admin Login</h2>
            <a href="admin_login.php" class="custom-btn admin">Admin Login</a>
        </div>
        <div class="box">
            <h2>User Login</h2>
            <a href="user_login.php" class="custom-btn login">User Login</a>
            <a href="user_registration.php" class="custom-btn signup">User Sign Up</a>
        </div>
    </div>

    <footer>
        <?php include 'footer.php'; ?>
    </footer>
</body>
</html>
