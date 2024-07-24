<?php

//index.php

include '../database_connection.php';
include '../function.php';

if (!is_admin_login()) {
    header('location:../admin_login.php');
}

include '../header.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Location Rack Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .container-fluid {
            padding: 20px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .col-md-3 {
            flex: 0 0 23%;
            max-width: 23%;
            padding: 0 15px;
        }

        .card {
            border: none;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-body {
            padding: 20px;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .text-white {
            color: #fff;
        }

        .bg-primary {
            background-color: #007bff !important;
        }

        .bg-warning {
            background-color: #ffc107 !important;
        }

        .bg-danger {
            background-color: #dc3545 !important;
        }

        .bg-success {
            background-color: #28a745 !important;
        }

        /* .size {
            font-size: 2.5rem;
            height : 0px; */
        

        /* .color1 {
            background-color: blue;
        }

        .color2 {
            background-color: yellow;
        }

        .color3 {
            background-color: red;
        }

        .color4 {
            background-color: green;
        }

        .color5 {
            background-color: green;
        } */
    </style>
</head>
<body>
    <h1 style="left:20px; position: relative;">Dashboard</h1>
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-3 color1">
            <div class="card bg-primary text-white animate__animated animate__fadeInUp">
                <div class="card-body">
                    <h1 class="text-center size"><?php echo Count_total_issue_book_number($connect); ?></h1>
                    <h5 class="text-center">Total Book Issue</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 color2">
            <div class="card bg-warning text-white animate__animated animate__fadeInUp">
                <div class="card-body">
                    <h1 class="text-center"><?php echo Count_total_returned_book_number($connect); ?></h1>
                    <h5 class="text-center">Total Book Returned</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 color3">
            <div class="card bg-danger text-white animate__animated animate__fadeInUp">
                <div class="card-body">
                    <h1 class="text-center"><?php echo Count_total_not_returned_book_number($connect); ?></h1>
                    <h5 class="text-center">Total Book Not Returned</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 color4">
            <div class="card bg-success text-white animate__animated animate__fadeInUp">
                <div class="card-body">
                    <h1 class="text-center size"><?php echo Count_total_fines_received($connect); ?></h1>
                    <h5 class="text-center size">Total Fines Received</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 color5">
            <div class="card bg-primary text-white animate__animated animate__fadeInUp">
                <div class="card-body">
                    <h1 class="text-center size"><?php echo Count_total_book_number($connect); ?></h1>
                    <h5 class="text-center size">Total Book</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white animate__animated animate__fadeInUp">
                <div class="card-body">
                    <h1 class="text-center size"><?php echo Count_total_author_number($connect); ?></h1>
                    <h5 class="text-center size">Total Author</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 color">
            <div class="card bg-danger text-white animate__animated animate__fadeInUp">
                <div class="card-body">
                    <h1 class="text-center size"><?php echo Count_total_category_number($connect); ?></h1>
                    <h5 class="text-center size">Total Category</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 color">
            <div class="card bg-success text-white animate__animated animate__fadeInUp">
                <div class="card-body">
                    <h1 class="text-center size"><?php echo Count_total_location_rack_number($connect); ?></h1>
                    <h5 class="text-center size">Total Location Rack</h5>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
<?php
include '../footer.php';
?>
