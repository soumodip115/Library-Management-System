<?php

//user.php

include '../database_connection.php';
include '../function.php';

if(!is_admin_login())
{
    header('location:../admin_login.php');
}

if(isset($_GET["action"], $_GET['status'], $_GET['code']) && $_GET["action"] == 'delete')
{
    $user_id = $_GET["code"];
    $status = $_GET["status"];

    $data = array(
        ':user_status'       => $status,
        ':user_updated_on'   => get_date_time($connect),
        ':user_id'           => $user_id
    );

    $query = "
    UPDATE lms_user 
    SET user_status = :user_status, 
    user_updated_on = :user_updated_on 
    WHERE user_id = :user_id
    ";

    $statement = $connect->prepare($query);
    $statement->execute($data);

    header('location:user.php?msg='.strtolower($status).'');
}

$query = "
    SELECT * FROM lms_user 
    ORDER BY user_id DESC
";

$statement = $connect->prepare($query);
$statement->execute();

include '../header.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>User Management</title>
    <!-- <link rel="stylesheet" href="author.css"> -->
    <!-- <link rel="stylesheet" href="category.css"> -->

    <style>
        /* Container styling */
        .container-fluid {
            padding: 20px;
            min-height: 700px;
        }

        /* Heading styling */
        h1 {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 40px; /* Increased margin to move it slightly down */
        }

        /* Breadcrumb styling */
        .breadcrumb {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            padding: 10px 15px;
            margin-bottom: 20px;
            width: 78.2em;
        }

        .breadcrumb a {
            text-decoration: none;
            color: #007bff;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        /* Card styling */
        .card {
            margin-bottom: 20px;
        }

        .card-header {
            font-weight: bold;
            font-size: 1.2rem;
        }

        .card-body {
            padding: 20px;
        }

        /* Table styling */
        #datatablesSimple {
            width: 100%;
            border-collapse: collapse;
        }

        #datatablesSimple th, #datatablesSimple td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        #datatablesSimple th {
            background-color: #f2f2f2;
        }

        /* Button styling */
        .btn {
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none; /* Remove underline from edit and add buttons */
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            color: white;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        /* Badge styling */
        .badge {
            padding: 5px 10px;
            border-radius: 3px;
        }

        .badge-success {
            background-color: #28a745;
        }

        .badge-danger {
            background-color: #dc3545;
        }

        /* Alert styling */
        .alert {
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
        }

        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
    </style>
</head>
<body>
<div class="container-fluid py-4" style="min-height: 700px;">
    <h1>User Management</h1>
    <ol class="breadcrumb mt-4 mb-4 bg-light p-2 border" style="list-style-type: none;">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">User Management</li>
    </ol>
    <?php 

    if(isset($_GET["msg"]))
    {
        if($_GET["msg"] == 'disable')
        {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Category Status Change to Disable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        }

        if($_GET["msg"] == 'enable')
        {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Category Status Change to Enable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        }
    }

    ?>
    <div class="card mb-4">
        <div class="card-header">
            <div class="row">
                <div class="col col-md-6">
                    <i class="fas fa-table me-1"></i> User Management
                </div>
                <div class="col col-md-6" align="right">
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="dataTables_wrapper dt-bootstrap4">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <div class="dataTables_length" id="datatablesSimple_length">
                            <label>Show 
                                <select name="datatablesSimple_length" aria-controls="datatablesSimple" class="form-control form-control-sm">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select> entries
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <div id="datatablesSimple_filter" class="dataTables_filter">
                            <label>Search:
                                <input type="search" class="form-control form-control-sm" placeholder="" aria-controls="datatablesSimple">
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <table id="datatablesSimple" class="display dataTable" style="width:100%" role="grid" aria-describedby="datatablesSimple_info">
                            <thead>
                                <tr role="row">
                                    <th>Image</th>
                                    <th>User Unique ID</th>
                                    <th>User Name</th>
                                    <th>Email Address</th>
                                    <th>Password</th>
                                    <th>Contact No.</th>
                                    <th>Address</th>
                                    <th>Email Verified</th>
                                    <th>Status</th>
                                    <th>Created On</th>
                                    <th>Updated On</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Image</th>
                                    <th>User Unique ID</th>
                                    <th>User Name</th>
                                    <th>Email Address</th>
                                    <th>Password</th>
                                    <th>Contact No.</th>
                                    <th>Address</th>
                                    <th>Email Verified</th>
                                    <th>Status</th>
                                    <th>Created On</th>
                                    <th>Updated On</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                            <tbody>
                            <?php 
                            if($statement->rowCount() > 0)
                            {
                                foreach($statement->fetchAll() as $row)
                                {
                                    $user_status = '';
                                    if($row['user_status'] == 'Enable')
                                    {
                                        $user_status = '<div class="badge bg-success">Enable</div>';
                                    }
                                    else
                                    {
                                        $user_status = '<div class="badge bg-danger">Disable</div>';
                                    }
                                    echo '
                                    <tr>
                                        <td><img src="../upload/'.$row["user_profile"].'" class="img-thumbnail" width="75" /></td>
                                        <td>'.$row["user_unique_id"].'</td>
                                        <td>'.$row["user_name"].'</td>
                                        <td>'.$row["user_email_address"].'</td>
                                        <td>'.$row["user_password"].'</td>
                                        <td>'.$row["user_contact_no"].'</td>
                                        <td>'.$row["user_address"].'</td>
                                        <td>'.$row["user_verification_status"].'</td>
                                        <td>'.$user_status.'</td>
                                        <td>'.$row["user_created_on"].'</td>
                                        <td>'.$row["user_updated_on"].'</td>
                                        <td><button type="button" name="delete_button" class="btn btn-danger btn-sm" onclick="delete_data(`'.$row["user_id"].'`, `'.$row["user_status"].'`)">Delete</td>
                                    </tr>
                                    ';
                                }
                            }
                            else
                            {
                                echo '
                                <tr>
                                    <td colspan="12" class="text-center">No Data Found</td>
                                </tr>
                                ';
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-5">
                        <div class="dataTables_info" id="datatablesSimple_info" role="status" aria-live="polite">Showing 1 to 10 of 18 entries</div>
                    </div>
                    <div class="col-sm-12 col-md-7">
                        <div class="dataTables_paginate paging_simple_numbers" id="datatablesSimple_paginate">
                            <ul class="pagination">
                                <li class="paginate_button page-item previous disabled" id="datatablesSimple_previous">
                                    <a href="#" aria-controls="datatablesSimple" data-dt-idx="0" tabindex="0" class="page-link">Previous</a>
                                </li>
                                <li class="paginate_button page-item active">
                                    <a href="#" aria-controls="datatablesSimple" data-dt-idx="1" tabindex="0" class="page-link">1</a>
                                </li>
                                <li class="paginate_button page-item ">
                                    <a href="#" aria-controls="datatablesSimple" data-dt-idx="2" tabindex="0" class="page-link">2</a>
                                </li>
                                <li class="paginate_button page-item next" id="datatablesSimple_next">
                                    <a href="#" aria-controls="datatablesSimple" data-dt-idx="3" tabindex="0" class="page-link">Next</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function delete_data(code, status)
    {
        var new_status = 'Enable';
        if(status == 'Enable')
        {
            new_status = 'Disable';
        }
        if(confirm("Are you sure you want to "+new_status+" this User?"))
        {
            window.location.href = "user.php?action=delete&code="+code+"&status="+new_status+"";
        }
    }
</script>
</body>
</html>
<?php 
include '../footer.php';
?>
