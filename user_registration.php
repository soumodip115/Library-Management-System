<?php

// user_registration.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include 'database_connection.php';
include 'function.php';

if (is_user_login()) {
    header('location:issue_book_details.php');
}

$message = '';
$success = '';

if (isset($_POST["register_button"])) {
    $formdata = array();

    if (empty($_POST["user_email_address"])) {
        $message .= '<li>Email Address is required</li>';
    } else {
        if (!filter_var($_POST["user_email_address"], FILTER_VALIDATE_EMAIL)) {
            $message .= '<li>Invalid Email Address</li>';
        } else {
            $formdata['user_email_address'] = trim($_POST['user_email_address']);
        }
    }

    if (empty($_POST["user_password"])) {
        $message .= '<li>Password is required</li>';
    } else {
        $formdata['user_password'] = trim($_POST['user_password']);
    }

    if (empty($_POST['user_name'])) {
        $message .= '<li>User Name is required</li>';
    } else {
        $formdata['user_name'] = trim($_POST['user_name']);
    }

    if (empty($_POST['user_address'])) {
        $message .= '<li>User Address Detail is required</li>';
    } else {
        $formdata['user_address'] = trim($_POST['user_address']);
    }

    if (empty($_POST['user_contact_no'])) {
        $message .= '<li>User Contact Number Detail is required</li>';
    } else {
        $formdata['user_contact_no'] = trim($_POST['user_contact_no']);
    }

    if (!empty($_FILES['user_profile']['name'])) {
        $img_name = $_FILES['user_profile']['name'];
        $img_type = $_FILES['user_profile']['type'];
        $tmp_name = $_FILES['user_profile']['tmp_name'];
        $fileinfo = @getimagesize($tmp_name);
        $width = $fileinfo[0];
        $height = $fileinfo[1];

        $image_size = $_FILES['user_profile']['size'];

        $img_explode = explode(".", $img_name);

        $img_ext = strtolower(end($img_explode));

        $extensions = ["jpeg", "png", "jpg"];

        if (in_array($img_ext, $extensions)) {
            if ($image_size <= 2000000) {
                if ($width == '225' && $height == '225') {
                    $new_img_name = time() . '-' . rand() . '.' . $img_ext;
                    if (move_uploaded_file($tmp_name, "upload/" . $new_img_name)) {
                        $formdata['user_profile'] = $new_img_name;
                    }
                } else {
                    $message .= '<li>Image dimension should be within 225 X 225</li>';
                }
            } else {
                $message .= '<li>Image size exceeds 2MB</li>';
            }
        } else {
            $message .= '<li>Invalid Image File</li>';
        }
    } else {
        $message .= '<li>Please Select Profile Image</li>';
    }

    if ($message == '') {
        $data = array(
            ':user_email_address' => $formdata['user_email_address']
        );

        $query = "
		SELECT * FROM lms_user 
        WHERE user_email_address = :user_email_address
		";

        $statement = $connect->prepare($query);
        $statement->execute($data);

        if ($statement->rowCount() > 0) {
            $message = '<li>Email Already Register</li>';
        } else {
            $user_verificaton_code = md5(uniqid());

            $user_unique_id = 'U' . rand(10000000, 99999999);

            $data = array(
                ':user_name' => $formdata['user_name'],
                ':user_address' => $formdata['user_address'],
                ':user_contact_no' => $formdata['user_contact_no'],
                ':user_profile' => $formdata['user_profile'],
                ':user_email_address' => $formdata['user_email_address'],
                ':user_password' => $formdata['user_password'],
                ':user_verificaton_code' => $user_verificaton_code,
                ':user_verification_status' => 'No',
                ':user_unique_id' => $user_unique_id,
                ':user_status' => 'Enable',
                ':user_created_on' => get_date_time($connect)
            );

            $query = "
			INSERT INTO lms_user 
            (user_name, user_address, user_contact_no, user_profile, user_email_address, user_password, user_verificaton_code, user_verification_status, user_unique_id, user_status, user_created_on) 
            VALUES (:user_name, :user_address, :user_contact_no, :user_profile, :user_email_address, :user_password, :user_verificaton_code, :user_verification_status, :user_unique_id, :user_status, :user_created_on)
			";

            $statement = $connect->prepare($query);
            $statement->execute($data);

            require 'vendor/autoload.php';

            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';  //Here you have to define GMail SMTP
            $mail->SMTPAuth = true;
            $mail->Username = 'xxxx';  //Here you can use your Gmail Email Address
            $mail->Password = 'xxxx';  //Here you can use your Gmail Address Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 80;

            $mail->setFrom('tutorial@webslesson.info', 'Webslesson');
            $mail->addAddress($formdata['user_email_address'], $formdata['user_name']);
            $mail->isHTML(true);

            $mail->Subject = 'Registration Verification for Library Management System';
            $mail->Body = '
			 <p>Thank you for registering for Library Management System Demo & your Unique ID is <b>' . $user_unique_id . '</b> which will be used for issue book.</p>

                <p>This is a verification email, please click the link to verify your email address.</p>
                <p><a href="' . base_url() . 'verify.php?code=' . $user_verificaton_code . '">Click to Verify</a></p>
                <p>Thank you...</p>
			';

            $mail->send();

            $success = 'Verification Email sent to ' . $formdata['user_email_address'] . ', so before login first verify your email';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
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
        .registration-container {
            width: 100%;
            max-width: 500px;
            padding: 20px;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
            color: #fff;
        }
        .registration-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .registration-header h2 {
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
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
        }
				.footer{
            position: absolute;
            display: flex;
            flex-direction: column-reverse;
            align-self: last baseline;
            /* left: 50px; */
            text-align: center;
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
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .text-muted {
            font-size: 0.875em;
            color: #6c757d;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

<div class="registration-container">
    <div class="registration-header">
        <h2>New User Registration</h2>
    </div>
    <?php 
    if ($message != '') {
        echo '<div class="alert alert-danger"><ul>'.$message.'</ul></div>';
    }

    if ($success != '') {
        echo '<div class="alert alert-success">'.$success.'</div>';
    }
    ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Email address</label>
            <input type="text" name="user_email_address" id="user_email_address" />
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="user_password" id="user_password" />
        </div>
        <div class="form-group">
            <label>User Name</label>
            <input type="text" name="user_name" id="user_name" />
        </div>
        <div class="form-group">
            <label>User Contact No.</label>
            <input type="text" name="user_contact_no" id="user_contact_no" />
        </div>
        <div class="form-group">
            <label>User Address</label>
            <textarea name="user_address" id="user_address"></textarea>
        </div>
        <div class="form-group">
            <label>User Photo</label><br />
            <input type="file" name="user_profile" id="user_profile" />
            <br />
            <span class="text-muted">Only .jpg & .png image allowed. Image size must be 225 x 225</span>
        </div>
        <div class="form-group">
            <input type="submit" name="register_button" class="btn-primary" value="Register" />
        </div>
    </form>
</div>

</body>
</html>

<?php 
include 'footer.php';
?>
