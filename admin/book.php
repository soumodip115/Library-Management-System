<?php

//book.php

include '../database_connection.php';
include '../function.php';

if(!is_admin_login())
{
    header('location:../admin_login.php');
}

$message = '';
$error = '';

if(isset($_POST["add_book"]))
{
    $formdata = array();

    if(empty($_POST["book_name"]))
    {
        $error .= '<li>Book Name is required</li>';
    }
    else
    {
        $formdata['book_name'] = trim($_POST["book_name"]);
    }

    if(empty($_POST["book_category"]))
    {
        $error .= '<li>Book Category is required</li>';
    }
    else
    {
        $formdata['book_category'] = trim($_POST["book_category"]);
    }

    if(empty($_POST["book_author"]))
    {
        $error .= '<li>Book Author is required</li>';
    }
    else
    {
        $formdata['book_author'] = trim($_POST["book_author"]);
    }

    if(empty($_POST["book_location_rack"]))
    {
        $error .= '<li>Book Location Rack is required</li>';
    }
    else
    {
        $formdata['book_location_rack'] = trim($_POST["book_location_rack"]);
    }

    if(empty($_POST["book_isbn_number"]))
    {
        $error .= '<li>Book ISBN Number is required</li>';
    }
    else
    {
        $formdata['book_isbn_number'] = trim($_POST["book_isbn_number"]);
    }
    if(empty($_POST["book_no_of_copy"]))
    {
        $error .= '<li>Book No. of Copy is required</li>';
    }
    else
    {
        $formdata['book_no_of_copy'] = trim($_POST["book_no_of_copy"]);
    }

    if($error == '')
    {
        $data = array(
            ':book_category'        =>    $formdata['book_category'],
            ':book_author'            =>    $formdata['book_author'],
            ':book_location_rack'    =>    $formdata['book_location_rack'],
            ':book_name'            =>    $formdata['book_name'],
            ':book_isbn_number'        =>    $formdata['book_isbn_number'],
            ':book_no_of_copy'        =>    $formdata['book_no_of_copy'],
            ':book_status'            =>    'Enable',
            ':book_added_on'        =>    get_date_time($connect)
        );

        $query = "
        INSERT INTO lms_book 
        (book_category, book_author, book_location_rack, book_name, book_isbn_number, book_no_of_copy, book_status, book_added_on) 
        VALUES (:book_category, :book_author, :book_location_rack, :book_name, :book_isbn_number, :book_no_of_copy, :book_status, :book_added_on)
        ";

        $statement = $connect->prepare($query);
        $statement->execute($data);

        header('location:book.php?msg=add');
    }
}

if(isset($_POST["edit_book"]))
{
    $formdata = array();

    if(empty($_POST["book_name"]))
    {
        $error .= '<li>Book Name is required</li>';
    }
    else
    {
        $formdata['book_name'] = trim($_POST["book_name"]);
    }

    if(empty($_POST["book_category"]))
    {
        $error .= '<li>Book Category is required</li>';
    }
    else
    {
        $formdata['book_category'] = trim($_POST["book_category"]);
    }

    if(empty($_POST["book_author"]))
    {
        $error .= '<li>Book Author is required</li>';
    }
    else
    {
        $formdata['book_author'] = trim($_POST["book_author"]);
    }

    if(empty($_POST["book_location_rack"]))
    {
        $error .= '<li>Book Location Rack is required</li>';
    }
    else
    {
        $formdata['book_location_rack'] = trim($_POST["book_location_rack"]);
    }

    if(empty($_POST["book_isbn_number"]))
    {
        $error .= '<li>Book ISBN Number is required</li>';
    }
    else
    {
        $formdata['book_isbn_number'] = trim($_POST["book_isbn_number"]);
    }
    if(empty($_POST["book_no_of_copy"]))
    {
        $error .= '<li>Book No. of Copy is required</li>';
    }
    else
    {
        $formdata['book_no_of_copy'] = trim($_POST["book_no_of_copy"]);
    }

    if($error == '')
    {
        $data = array(
            ':book_category'        =>    $formdata['book_category'],
            ':book_author'            =>    $formdata['book_author'],
            ':book_location_rack'    =>    $formdata['book_location_rack'],
            ':book_name'            =>    $formdata['book_name'],
            ':book_isbn_number'        =>    $formdata['book_isbn_number'],
            ':book_no_of_copy'        =>    $formdata['book_no_of_copy'],
            ':book_updated_on'        =>    get_date_time($connect),
            ':book_id'                =>    $_POST["book_id"]
        );
        $query = "
        UPDATE lms_book 
        SET book_category = :book_category, 
        book_author = :book_author, 
        book_location_rack = :book_location_rack, 
        book_name = :book_name, 
        book_isbn_number = :book_isbn_number, 
        book_no_of_copy = :book_no_of_copy, 
        book_updated_on = :book_updated_on 
        WHERE book_id = :book_id
        ";

        $statement = $connect->prepare($query);
        $statement->execute($data);

        header('location:book.php?msg=edit');
    }
}

if(isset($_GET["action"], $_GET["code"], $_GET["status"]) && $_GET["action"] == 'delete')
{
    $book_id = $_GET["code"];
    $status = $_GET["status"];

    $data = array(
        ':book_status'        =>    $status,
        ':book_updated_on'    =>    get_date_time($connect),
        ':book_id'            =>    $book_id
    );

    $query = "
    UPDATE lms_book 
    SET book_status = :book_status, 
    book_updated_on = :book_updated_on 
    WHERE book_id = :book_id
    ";

    $statement = $connect->prepare($query);
    $statement->execute($data);

    header('location:book.php?msg='.strtolower($status).'');
}

include '../header.php';

$query = "SELECT * FROM lms_book ORDER BY book_id DESC";
$statement = $connect->prepare($query);
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Location Rack Management</title>
    <link rel="stylesheet" type="text/css" href="author.css">
    <link rel="stylesheet" href="category.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
</head>
<style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 20px;
        }
        .breadcrumb {
            background: none;
            padding: 0;
        }
        .breadcrumb-item a {
            text-decoration: none;
            color: #007bff;
        }
        .breadcrumb-item.active {
            color: #6c757d;
        }
        .card {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 0.25rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #ddd;
            padding: 10px 15px;
            font-size: 1.25rem;
            font-weight: 500;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .card-body {
            padding: 15px;
        }
        .table {
            width: 100%;
            margin: 0;
            border-collapse: collapse;
        }
        .table th, .table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .table th {
            background-color: #f2f2f2;
        }
        .btn {
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 3px;
            font-size: 0.875rem;
            display: inline-block;
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
        }
        .btn-success {
					background-color: #28a745;
          color: white;
         position: absolute;
         right: 29px;
		     top: 14.5em;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .btn:hover {
            opacity: 0.8;
        }
        .badge {
            display: inline-block;
            padding: 0.25em 0.4em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }
        .badge-success {
            background-color: #28a745;
        }
        .badge-danger {
            background-color: #dc3545;
        }
        .alert {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        .dataTables_wrapper {
            margin: 20px 0;
        }
        .dataTables_length, .dataTables_filter, .dataTables_info, .dataTables_paginate {
            margin-bottom: 10px;
        }
        .dataTables_paginate ul.pagination {
            justify-content: flex-end;
        }
        .dataTables_paginate .page-link {
            margin: 0 5px;
            padding: 5px 10px;
            border-radius: 3px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
        }
        .dataTables_paginate .page-link:hover {
            background-color: #0056b3;
        }
    </style>
<body>

<div class="container-fluid py-4" style="min-height: 700px;">
    <h1>Book Management</h1>
    <?php 
    if(isset($_GET["action"]))
    {
        if($_GET["action"] == 'add')
        {
    ?>

    <ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="book.php">Book Management</a></li>
        <li class="breadcrumb-item active">Add Book</li>
    </ol>

    <?php 

    if($error != '')
    {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">'.$error.'</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }

    ?>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user-plus"></i> Add New Book
        </div>
        <div class="card-body">
            <form method="post">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Book Name</label>
                            <input type="text" name="book_name" id="book_name" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Select Author</label>
                            <select name="book_author" id="book_author" class="form-control">
                                <?php echo fill_author_list($connect); ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Select Category</label>
                            <select name="book_category" id="book_category" class="form-control">
                                <?php echo fill_category_list($connect); ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Book Location Rack</label>
                            <select name="book_location_rack" id="book_location_rack" class="form-control">
                                <?php echo fill_location_rack_list($connect); ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">ISBN Number</label>
                            <input type="text" name="book_isbn_number" id="book_isbn_number" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">No. of Copy</label>
                            <input type="number" name="book_no_of_copy" id="book_no_of_copy" class="form-control" />
                        </div>
                    </div>
                </div>
                <div class="mt-4 mb-0">
                    <input type="submit" name="add_book" class="btn btn-success" value="Add" />
                </div>
            </form>
        </div>
    </div>

    <?php 
        }
        else if($_GET["action"] == 'edit')
        {
            if(isset($_GET["code"]))
            {
                $query = "
                SELECT * FROM lms_book 
                WHERE book_id = '".$_GET["code"]."'
                ";

                $book_result = $connect->query($query);
                foreach($book_result as $book_row)
                {
                ?>

                <ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="book.php">Book Management</a></li>
                    <li class="breadcrumb-item active">Edit Book</li>
                </ol>

                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-user-edit"></i> Edit Book Details
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Book Name</label>
                                        <input type="text" name="book_name" id="book_name" class="form-control" value="<?php echo $book_row['book_name']; ?>" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Select Author</label>
                                        <select name="book_author" id="book_author" class="form-control">
                                            <?php echo fill_author_list($connect); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Select Category</label>
                                        <select name="book_category" id="book_category" class="form-control">
                                            <?php echo fill_category_list($connect); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Book Location Rack</label>
                                        <select name="book_location_rack" id="book_location_rack" class="form-control">
                                            <?php echo fill_location_rack_list($connect); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">ISBN Number</label>
                                        <input type="text" name="book_isbn_number" id="book_isbn_number" class="form-control" value="<?php echo $book_row['book_isbn_number']; ?>" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">No. of Copy</label>
                                        <input type="number" name="book_no_of_copy" id="book_no_of_copy" class="form-control" value="<?php echo $book_row['book_no_of_copy']; ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 mb-0">
                                <input type="hidden" name="book_id" value="<?php echo $book_row['book_id']; ?>" />
                                <input type="submit" name="edit_book" class="btn btn-primary" value="Edit" />
                            </div>
                        </form>
                    </div>
                </div>

                <?php
                }
            }
        }
    }
    else
    {
    ?>

    <ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Book Management</li>
    </ol>

    <?php 
    if(isset($_GET["msg"]))
    {
        if($_GET["msg"] == 'add')
        {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">New Book Added Successfully <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        }

        if($_GET["msg"] == 'edit')
        {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Book Data Updated Successfully <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        }

        if($_GET["msg"] == 'delete')
        {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Book Data Deleted Successfully <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        }

        if($_GET["msg"] == 'enable')
        {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Book Status Changed to Enable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        }

        if($_GET["msg"] == 'disable')
        {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Book Status Changed to Disable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        }
    }
    ?>

    <div class="card mb-4">
        <div class="card-header">
            <div class="row">
                <div class="col col-md-6">
                    <i class="fas fa-table me-1"></i> Book Management
                </div>
                <div class="col col-md-6" align="right">
                    <a href="book.php?action=add" class="btn btn-success btn-sm">Add</a>
                </div>
            </div>
        </div>
        <div class="card-body">
             <!-- <div class="row mb-3">
                <div class="col-sm-6">
                    <div class="dataTables_length" id="dataTable_length">
                        <label>
                            Show 
                            <select name="dataTable_length" aria-controls="dataTable" class="custom-select custom-select-sm form-control form-control-sm">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select> 
                            entries
                        </label>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div id="dataTable_filter" class="dataTables_filter">
                        <label>
                            Search:
                            <input type="search" class="form-control form-control-sm" placeholder="" aria-controls="dataTable">
                        </label>
                    </div>
                </div> -->
            <!-- </div>  -->
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Book Name</th>
                            <th>ISBN Number</th>
                            <th>Category</th>
                            <th>Author</th>
                            <th>No. of Copies</th>
                            <th>Location Rack</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if(!empty($result))
                        {
                            foreach($result as $row)
                            {
                                $status = ($row['book_status'] == 'Enable') ? '<div class="badge bg-success">Enable</div>' : '<div class="badge bg-danger">Disable</div>';
                                echo '
                                <tr>
                                    <td>'.$row["book_name"].'</td>
                                    <td>'.$row["book_isbn_number"].'</td>
                                    <td>'.$row["book_category"].'</td>
                                    <td>'.$row["book_author"].'</td>
                                    <td>'.$row["book_no_of_copy"].'</td>
                                    <td>'.$row["book_location_rack"].'</td>
                                    <td>'.$status.'</td>
                                    <td>
                                        <a href="book.php?action=edit&code='.$row["book_id"].'" class="btn btn-sm btn-primary">Edit</a>
                                        <a href="book.php?action=delete&code='.$row["book_id"].'&status='.$row["book_status"].'" class="btn btn-sm btn-danger">Delete</a>
                                    </td>
                                </tr>
                                ';
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <!-- <div class="row mt-3">
                <div class="col-sm-12 col-md-5">
                    <div class="dataTables_info" id="dataTable_info" role="status" aria-live="polite">
                        Showing 1 to 10 of 57 entries
                    </div>
                </div>
                <div class="col-sm-12 col-md-7">
                    <div class="dataTables_paginate paging_simple_numbers" id="dataTable_paginate">
                        <ul class="pagination">
                            <li class="paginate_button page-item previous disabled" id="dataTable_previous">
                                <a href="#" aria-controls="dataTable" data-dt-idx="0" tabindex="0" class="page-link">Previous</a>
                            </li>
                            <li class="paginate_button page-item active">
                                <a href="#" aria-controls="dataTable" data-dt-idx="1" tabindex="0" class="page-link">1</a>
                            </li>
                            <li class="paginate_button page-item">
                                <a href="#" aria-controls="dataTable" data-dt-idx="2" tabindex="0" class="page-link">2</a>
                            </li>
                            <li class="paginate_button page-item">
                                <a href="#" aria-controls="dataTable" data-dt-idx="3" tabindex="0" class="page-link">3</a>
                            </li>
                            <li class="paginate_button page-item">
                                <a href="#" aria-controls="dataTable" data-dt-idx="4" tabindex="0" class="page-link">4</a>
                            </li>
                            <li class="paginate_button page-item">
                                <a href="#" aria-controls="dataTable" data-dt-idx="5" tabindex="0" class="page-link">5</a>
                            </li>
                            <li class="paginate_button page-item">
                                <a href="#" aria-controls="dataTable" data-dt-idx="6" tabindex="0" class="page-link">6</a>
                            </li>
                            <li class="paginate_button page-item next" id="dataTable_next">
                                <a href="#" aria-controls="dataTable" data-dt-idx="7" tabindex="0" class="page-link">Next</a>
                            </li>
                        </ul>
                    </div>
                </div> -->
            </div>
        </div>
    </div>

    <?php 
    }
    ?>
</div>

<script>
$(document).ready(function() {
    $('#dataTable').DataTable();
});
</script>

<?php include '../footer.php'; ?>
</body>
</html>
