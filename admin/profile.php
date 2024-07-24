<?php

//profile.php

include '../database_connection.php';
include '../function.php';

if(!is_admin_login()) {
    header('location:../admin_login.php');
}

$message = '';
$error = '';

if(isset($_POST['edit_admin'])) {

    $formdata = array();

    if(empty($_POST['admin_email'])) {
        $error .= '<li>Email Address is required</li>';
    } else {
        if(!filter_var($_POST["admin_email"], FILTER_VALIDATE_EMAIL)) {
            $error .= '<li>Invalid Email Address</li>';
        } else {
            $formdata['admin_email'] = $_POST['admin_email'];
        }
    }

    if(empty($_POST['admin_password'])) {
        $error .= '<li>Password is required</li>';
    } else {
        $formdata['admin_password'] = $_POST['admin_password'];
    }

    if($error == '') {
        $admin_id = $_SESSION['admin_id'];

        $data = array(
            ':admin_email'      =>  $formdata['admin_email'],
            ':admin_password'   =>  $formdata['admin_password'],
            ':admin_id'         =>  $admin_id
        );

        $query = "
        UPDATE lms_admin 
            SET admin_email = :admin_email,
            admin_password = :admin_password 
            WHERE admin_id = :admin_id
        ";

        $statement = $connect->prepare($query);

        $statement->execute($data);

        $message = 'User Data Edited';
    }
}

$query = "
    SELECT * FROM lms_admin 
    WHERE admin_id = '".$_SESSION["admin_id"]."'
";

$result = $connect->query($query);

include '../header.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Profile</title>
    <style>
        .container-fluid {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .breadcrumb {
            background-color: #f1f1f1;
            padding: 10px;
            border-radius: 5px;
            display: flex;
            align-items: center;
        }

        .breadcrumb a {
            text-decoration: none;
            color: #007bff;
            margin-right: 5px;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .breadcrumb-item::after {
            content: "/";
            margin-left: 5px;
        }

        .breadcrumb-item:last-child::after {
            content: "";
        }

        .card {
            border-radius: 5px;
            transition: transform 0.3s ease;
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e0e0e0;
        }

        .card-body {
            background-color: #ffffff;
            padding: 20px;
        }

        .form-label {
            font-weight: bold;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        .alert-dismissible .btn-close {
            padding: 1rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid px-4">
        <h1 class="mt-4">Profile</h1>
        <ol class="breadcrumb mt-4 mb-4 bg-light p-2 border" style="list-style-type: none;">
            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
            <li class="breadcrumb-item active">Profile</li>
        </ol>
        <div class="row">
            <div class="col-md-6">
                <?php 

                if($error != '') {
                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">'.$error.'</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                }

                if($message != '') {
                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">'.$message.' <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                }

                ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-user-edit"></i> Edit Profile Details
                    </div>
                    <div class="card-body">

                    <?php 

                    foreach($result as $row) {
                    ?>

                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="text" name="admin_email" id="admin_email" class="form-control" value="<?php echo $row['admin_email']; ?>" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="admin_password" id="admin_password" class="form-control" value="<?php echo $row['admin_password']; ?>" />
                            </div>
                            <div class="mt-4 mb-0">
                                <input type="submit" name="edit_admin" class="btn btn-primary" value="Edit" />
                            </div>
                        </form>

                    <?php 
                    }

                    ?>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        // JavaScript for button click animation
        document.querySelector('.btn-primary').addEventListener('click', function() {
            this.style.transform = 'scale(1.1)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 300);
        });
    </script>
</body>
</html>

<?php 

include '../footer.php';

?>
