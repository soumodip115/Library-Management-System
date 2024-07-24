<?php
// admin_login.php
include 'database_connection.php';
include 'function.php';

$message = '';

if(isset($_POST["login_button"])) {
    $formdata = array();

    if(empty($_POST["admin_email"])) {
        $message .= '<li>Email Address is required</li>';
    } else {
        if(!filter_var($_POST["admin_email"], FILTER_VALIDATE_EMAIL)) {
            $message .= '<li>Invalid Email Address</li>';
        } else {
            $formdata['admin_email'] = $_POST['admin_email'];
        }
    }

    if(empty($_POST['admin_password'])) {
        $message .= '<li>Password is required</li>';
    } else {
        $formdata['admin_password'] = $_POST['admin_password'];
    }

    if($message == '') {
        $data = array(
            ':admin_email' => $formdata['admin_email']
        );

        $query = "
        SELECT * FROM lms_admin 
        WHERE admin_email = :admin_email
        ";

        $statement = $connect->prepare($query);
        $statement->execute($data);

        if($statement->rowCount() > 0) {
            foreach($statement->fetchAll() as $row) {
                if($row['admin_password'] == $formdata['admin_password']) {
                    $_SESSION['admin_id'] = $row['admin_id'];
                    header('location:admin/index.php');
                } else {
                    $message = '<li>Wrong Password</li>';
                }
            }
        } else {
            $message = '<li>Wrong Email Address</li>';
        }
    }
}

// include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('book-library.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .footer{
            position: absolute;
            display: flex;
            flex-direction: column-reverse;
            align-self: last baseline;
            /* left: 50px; */
            text-align: center;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            background: rgba(0, 0, 0, 0.85);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
            color: #fff;
        }
        .login-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .login-header h2 {
            margin: 0;
            font-size: 2rem;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
        }
        .btn-primary {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s, transform 0.3s;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
        .alert {
            margin-bottom: 20px;
            color: #f44336;
            background: rgba(255, 0, 0, 0.1);
            padding: 10px;
            border-radius: 5px;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <?php 
        if($message != '') {
            echo '<div class="alert"><ul>'.$message.'</ul></div>';
        }
        ?>
        <div class="login-header">
            <h2>Admin Login</h2>
        </div>
        <div class="login-body">
            <form method="POST">
                <div class="form-group">
                    <label for="admin_email">Email Address</label>
                    <input type="email" name="admin_email" id="admin_email" class="form-control" required />
                </div>
                <div class="form-group">
                    <label for="admin_password">Password</label>
                    <input type="password" name="admin_password" id="admin_password" class="form-control" required />
                </div>
                <div class="form-group">
                    <input type="submit" name="login_button" class="btn btn-primary" value="Login" />
                </div>
            </form>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
