<?php

// category.php

include '../database_connection.php';
include '../function.php';

if (!is_admin_login()) {
    header('location:../admin_login.php');
}

$message = '';
$error = '';

if (isset($_POST['add_category'])) {
    $formdata = array();

    if (empty($_POST['category_name'])) {
        $error .= '<li>Category Name is required</li>';
    } else {
        $formdata['category_name'] = trim($_POST['category_name']);
    }

    if ($error == '') {
        $query = "
        SELECT * FROM lms_category 
        WHERE category_name = '" . $formdata['category_name'] . "'
        ";

        $statement = $connect->prepare($query);
        $statement->execute();

        if ($statement->rowCount() > 0) {
            $error = '<li>Category Name Already Exists</li>';
        } else {
            $data = array(
                ':category_name' => $formdata['category_name'],
                ':category_status' => 'Enable',
                ':category_created_on' => get_date_time($connect)
            );

            $query = "
            INSERT INTO lms_category 
            (category_name, category_status, category_created_on) 
            VALUES (:category_name, :category_status, :category_created_on)
            ";

            $statement = $connect->prepare($query);
            $statement->execute($data);

            header('location:category.php?msg=add');
        }
    }
}

if (isset($_POST["edit_category"])) {
    $formdata = array();

    if (empty($_POST["category_name"])) {
        $error .= '<li>Category Name is required</li>';
    } else {
        $formdata['category_name'] = $_POST['category_name'];
    }

    if ($error == '') {
        $category_id = convert_data($_POST['category_id'], 'decrypt');

        $query = "
        SELECT * FROM lms_category 
        WHERE category_name = '" . $formdata['category_name'] . "' 
        AND category_id != '" . $category_id . "'
        ";

        $statement = $connect->prepare($query);
        $statement->execute();

        if ($statement->rowCount() > 0) {
            $error = '<li>Category Name Already Exists</li>';
        } else {
            $data = array(
                ':category_name' => $formdata['category_name'],
                ':category_updated_on' => get_date_time($connect),
                ':category_id' => $category_id
            );

            $query = "
            UPDATE lms_category 
            SET category_name = :category_name, 
            category_updated_on = :category_updated_on  
            WHERE category_id = :category_id
            ";

            $statement = $connect->prepare($query);
            $statement->execute($data);

            header('location:category.php?msg=edit');
        }
    }
}

if (isset($_GET["action"], $_GET["code"], $_GET["status"]) && $_GET["action"] == 'delete') {
    $category_id = $_GET["code"];
    $status = $_GET["status"];
    $data = array(
        ':category_status' => $status,
        ':category_updated_on' => get_date_time($connect),
        ':category_id' => $category_id
    );
    $query = "
    UPDATE lms_category 
    SET category_status = :category_status, 
    category_updated_on = :category_updated_on 
    WHERE category_id = :category_id
    ";

    $statement = $connect->prepare($query);
    $statement->execute($data);

    header('location:category.php?msg=' . strtolower($status) . '');
}

$query = "
SELECT * FROM lms_category 
    ORDER BY category_name ASC
";

$statement = $connect->prepare($query);
$statement->execute();

include '../header.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Management</title>
    <link rel="stylesheet" href="category.css">
</head>
<body>
<div class="container-fluid py-4">
    <h1>Category Management</h1>
    <?php 

    if (isset($_GET['action'])) {
        if ($_GET['action'] == 'add') {
    ?>

    <ol class="breadcrumb mt-4 mb-4 bg-light p-2 border" style="list-style: none;">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="category.php">Category Management</a></li>
        <li class="breadcrumb-item active">Add Category</li>
    </ol>
    <div class="row">
        <div class="col-md-6">
            <?php 

            if ($error != '') {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">' . $error . '</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }

            ?>
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user-plus"></i> Add New Category
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Category Name</label>
                            <input type="text" name="category_name" id="category_name" class="form-control" />
                        </div>
                        <div class="mt-4 mb-0">
                            <input type="submit" name="add_category" value="Add" class="btn btn-success" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php 
        } else if ($_GET["action"] == 'edit') {
            $category_id = convert_data($_GET["code"], 'decrypt');

            if ($category_id > 0) {
                $query = "
                SELECT * FROM lms_category 
                WHERE category_id = '$category_id'
                ";

                $category_result = $connect->query($query);

                foreach ($category_result as $category_row) {
                ?>

    <ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="category.php">Category Management</a></li>
        <li class="breadcrumb-item active">Edit Category</li>
    </ol>
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user-edit"></i> Edit Category Details
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">Category Name</label>
                            <input type="text" name="category_name" id="category_name" class="form-control" value="<?php echo $category_row['category_name']; ?>" />
                        </div>
                        <div class="mt-4 mb-0">
                            <input type="hidden" name="category_id" value="<?php echo $_GET['code']; ?>" />
                            <input type="submit" name="edit_category" class="btn btn-primary" value="Edit" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

                <?php 
                }
            }
        }
    } else {	
    ?>
    <ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Category Management</li>
    </ol>

    <?php 

    if (isset($_GET['msg'])) {
        if ($_GET['msg'] == 'add') {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">New Category Added<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        }

        if ($_GET["msg"] == 'edit') {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Category Data Edited <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        }
        if ($_GET["msg"] == 'disable') {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Category Status Change to Disable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        }

        if ($_GET['msg'] == 'enable') {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Category Status Change to Enable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        }
    }

    ?>
    <div class="card mb-4">
        <div class="card-header">
            <div class="row">
                <div class="col col-md-6">
                    <i class="fas fa-table me-1"></i> Category Management
                </div>
                <div class="col col-md-6" align="right">
                    <a href="category.php?action=add" class="btn btn-success btn-sm">Add</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3" style="display:flex;">
                <div class="col-sm-6">
                    <label for="entriesPerPage" class="form-label">Entries per page</label>
                    <select id="entriesPerPage" class="form-select">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="col-sm-6 text-end" style="position: relative;
                    right: -50em;">
                    <label for="searchBox" class="form-label" >Search</label>
                    <input type="text" id="searchBox" class="form-control" placeholder="Search...">
                </div>
            </div>
            <table id="datatablesSimple" class="table">
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th>Status</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Category Name</th>
                        <th>Status</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </tfoot>
                <tbody>
                <?php
                if ($statement->rowCount() > 0) {
                    foreach ($statement->fetchAll() as $row) {
                        $status = '';
                        if ($row['category_status'] == 'Enable') {
                            $status = '<div class="badge bg-success">Enable</div>';
                        } else {
                            $status = '<div class="badge bg-danger">Disable</div>';
                        }
                        echo '
                        <tr>
                            <td>' . $row["category_name"] . '</td>
                            <td>' . $status . '</td>
                            <td><a href="category.php?action=edit&code=' . convert_data($row["category_id"]) . '" class="btn btn-sm btn-primary">Edit</a></td>
                            <td><button type="button" name="delete_button" class="btn btn-danger btn-sm delete_button" data-id="' . convert_data($row["category_id"]) . '" data-status="' . $row["category_status"] . '">Delete</button></td>
                        </tr>
                        ';
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php } ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var delete_buttons = document.querySelectorAll('.delete_button');
        delete_buttons.forEach(function (button) {
            button.addEventListener('click', function () {
                var id = this.getAttribute('data-id');
                var status = this.getAttribute('data-status');
                var new_status = (status == 'Enable') ? 'Disable' : 'Enable';
                if (confirm("Are you sure you want to " + new_status + " this Category?")) {
                    window.location.href = "category.php?action=delete&code=" + id + "&status=" + new_status;
                }
            });
        });

        // Implement search functionality
        document.getElementById('searchBox').addEventListener('keyup', function () {
            var searchText = this.value.toLowerCase();
            var tableRows = document.querySelectorAll('#datatablesSimple tbody tr');
            tableRows.forEach(function (row) {
                var rowData = row.textContent.toLowerCase();
                row.style.display = rowData.includes(searchText) ? '' : 'none';
            });
        });

        // Implement entries per page functionality
        document.getElementById('entriesPerPage').addEventListener('change', function () {
            var entriesPerPage = parseInt(this.value);
            var tableRows = document.querySelectorAll('#datatablesSimple tbody tr');
            tableRows.forEach(function (row, index) {
                row.style.display = (index < entriesPerPage) ? '' : 'none';
            });
        });

        // Initialize entries per page functionality
        document.getElementById('entriesPerPage').dispatchEvent(new Event('change'));
    });
</script>
</body>
</html>
