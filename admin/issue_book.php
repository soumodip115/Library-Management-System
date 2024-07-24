<?php

//issue_book.php

include '../database_connection.php';
include '../function.php';

if(!is_admin_login())
{
    header('location:../admin_login.php');
}

$error = '';

if(isset($_POST["issue_book_button"]))
{
    $formdata = array();

    if(empty($_POST["book_id"]))
    {
        $error .= '<li>Book ISBN Number is required</li>';
    }
    else
    {
        $formdata['book_id'] = trim($_POST['book_id']);
    }

    if(empty($_POST["user_id"]))
    {
        $error .= '<li>User Unique Number is required</li>';
    }
    else
    {
        $formdata['user_id'] = trim($_POST['user_id']);
    }

    if($error == '')
    {
        // Check Book Available or Not
        $query = "
        SELECT * FROM lms_book 
        WHERE book_isbn_number = '".$formdata['book_id']."'
        ";
        $statement = $connect->prepare($query);
        $statement->execute();

        if($statement->rowCount() > 0)
        {
            foreach($statement->fetchAll() as $book_row)
            {
                // check book is available or not
                if($book_row['book_status'] == 'Enable' && $book_row['book_no_of_copy'] > 0)
                {
                    // Check User is exist
                    $query = "
                    SELECT user_id, user_status FROM lms_user 
                    WHERE user_unique_id = '".$formdata['user_id']."'
                    ";
                    $statement = $connect->prepare($query);
                    $statement->execute();

                    if($statement->rowCount() > 0)
                    {
                        foreach($statement->fetchAll() as $user_row)
                        {
                            if($user_row['user_status'] == 'Enable')
                            {
                                // Check User Total issue of Book
                                $book_issue_limit = get_book_issue_limit_per_user($connect);
                                $total_book_issue = get_total_book_issue_per_user($connect, $formdata['user_id']);

                                if($total_book_issue < $book_issue_limit)
                                {
                                    $total_book_issue_day = get_total_book_issue_day($connect);
                                    $today_date = get_date_time($connect);
                                    $expected_return_date = date('Y-m-d H:i:s', strtotime($today_date. ' + '.$total_book_issue_day.' days'));

                                    $data = array(
                                        ':book_id' => $formdata['book_id'],
                                        ':user_id' => $formdata['user_id'],
                                        ':issue_date_time' => $today_date,
                                        ':expected_return_date' => $expected_return_date,
                                        ':return_date_time' => '',
                                        ':book_fines' => 0,
                                        ':book_issue_status' => 'Issue'
                                    );

                                    $query = "
                                    INSERT INTO lms_issue_book 
                                    (book_id, user_id, issue_date_time, expected_return_date, return_date_time, book_fines, book_issue_status) 
                                    VALUES (:book_id, :user_id, :issue_date_time, :expected_return_date, :return_date_time, :book_fines, :book_issue_status)
                                    ";
                                    $statement = $connect->prepare($query);
                                    $statement->execute($data);

                                    $query = "
                                    UPDATE lms_book 
                                    SET book_no_of_copy = book_no_of_copy - 1, 
                                    book_updated_on = '".$today_date."' 
                                    WHERE book_isbn_number = '".$formdata['book_id']."' 
                                    ";
                                    $connect->query($query);
                                    header('location:issue_book.php?msg=add');
                                }
                                else
                                {
                                    $error .= 'User has already reached Book Issue Limit, First return pending book';
                                }
                            }
                            else
                            {
                                $error .= '<li>User Account is Disable, Contact Admin</li>';
                            }
                        }
                    }
                    else
                    {
                        $error .= '<li>User not Found</li>';
                    }
                }
                else
                {
                    $error .= '<li>Book not Available</li>';
                }
            }
        }
        else
        {
            $error .= '<li>Book not Found</li>';
        }
    }
}

if(isset($_POST["book_return_button"]))
{
    if(isset($_POST["book_return_confirmation"]))
    {
        $data = array(
            ':return_date_time' => get_date_time($connect),
            ':book_issue_status' => 'Return',
            ':issue_book_id' => $_POST['issue_book_id']
        );  

        $query = "
        UPDATE lms_issue_book 
        SET return_date_time = :return_date_time, 
        book_issue_status = :book_issue_status 
        WHERE issue_book_id = :issue_book_id
        ";
        $statement = $connect->prepare($query);
        $statement->execute($data);

        $query = "
        UPDATE lms_book 
        SET book_no_of_copy = book_no_of_copy + 1 
        WHERE book_isbn_number = '".$_POST["book_isbn_number"]."'
        ";
        $connect->query($query);
        header("location:issue_book.php?msg=return");
    }
    else
    {
        $error = 'Please first confirm return book received by click on checkbox';
    }
}   

$query = "
SELECT * FROM lms_issue_book 
ORDER BY issue_book_id DESC
";
$statement = $connect->prepare($query);
$statement->execute();

include '../header.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Issue Book Management</title>
    <link rel="stylesheet" type="text/css" href="author.css">
    <link rel="stylesheet" href="category.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
</head>
<body>
<div class="container-fluid py-4" style="min-height: 700px;">
    <h1>Issue Book Management</h1>
    <?php 
    if(isset($_GET["action"]))
    {
        if($_GET["action"] == 'add')
        {
    ?>
    <ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="issue_book.php">Issue Book Management</a></li>
        <li class="breadcrumb-item active">Issue New Book</li>
    </ol>
    <div class="row">
        <div class="col-md-6">
            <?php 
            if($error != '')
            {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">'.$error.'</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }
            ?>
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user-plus"></i> Issue New Book
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">Book ISBN Number</label>
                            <input type="text" name="book_id" id="book_id" class="form-control" />
                            <span id="book_isbn_result"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">User Unique ID</label>
                            <input type="text" name="user_id" id="user_id" class="form-control" />
                            <span id="user_unique_id_result"></span>
                        </div>
                        <div class="mt-4 mb-0">
                            <input type="submit" name="issue_book_button" class="btn btn-success" value="Issue" />
                        </div>  
                    </form>
                    <script>
                    var book_id = document.getElementById('book_id');
                    book_id.onkeyup = function()
                    {
                        if(this.value.length > 2)
                        {
                            var form_data = new FormData();
                            form_data.append('action', 'search_book_isbn');
                            form_data.append('request', this.value);
                            fetch('action.php', {
                                method:"POST",
                                body:form_data
                            }).then(function(response){
                                return response.json();
                            }).then(function(responseData){
                                var html = '<div class="list-group" style="position:absolute; width:93%">';
                                if(responseData.length > 0)
                                {
                                    for(var count = 0; count < responseData.length; count++)
                                    {
                                        html += '<a href="#" class="list-group-item list-group-item-action"><span onclick="get_text(this)">'+responseData[count].isbn_no+'</span> - <span class="text-muted">'+responseData[count].book_name+'</span></a>';
                                    }
                                }
                                else
                                {
                                    html += '<a href="#" class="list-group-item list-group-item-action">No Book Found</a>';
                                }
                                html += '</div>';
                                document.getElementById('book_isbn_result').innerHTML = html;
                            });
                        }
                        else
                        {
                            document.getElementById('book_isbn_result').innerHTML = '';
                        }
                    }

                    var user_id = document.getElementById('user_id');
                    user_id.onkeyup = function()
                    {
                        if(this.value.length > 2)
                        {
                            var form_data = new FormData();
                            form_data.append('action', 'search_user_id');
                            form_data.append('request', this.value);
                            fetch('action.php', {
                                method:"POST",
                                body:form_data
                            }).then(function(response){
                                return response.json();
                            }).then(function(responseData){
                                var html = '<div class="list-group" style="position:absolute; width:93%">';
                                if(responseData.length > 0)
                                {
                                    for(var count = 0; count < responseData.length; count++)
                                    {
                                        html += '<a href="#" class="list-group-item list-group-item-action"><span onclick="get_text(this)">'+responseData[count].user_unique_id+'</span> - <span class="text-muted">'+responseData[count].user_name+'</span></a>';
                                    }
                                }
                                else
                                {
                                    html += '<a href="#" class="list-group-item list-group-item-action">No User Found</a>';
                                }
                                html += '</div>';
                                document.getElementById('user_unique_id_result').innerHTML = html;
                            });
                        }
                        else
                        {
                            document.getElementById('user_unique_id_result').innerHTML = '';
                        }
                    }

                    function get_text(event)
                    {
                        var string = event.textContent;
                        document.getElementById('book_id').value = string;
                        document.getElementById('book_isbn_result').innerHTML = '';
                        document.getElementById('user_id').value = string;
                        document.getElementById('user_unique_id_result').innerHTML = '';
                    }
                    </script>
                </div>
            </div>
        </div>
    </div>
    <?php 
        }
        else if($_GET["action"] == 'return')
        {
            if(isset($_GET["code"]))
            {
                $query = "
                SELECT * FROM lms_issue_book 
                WHERE issue_book_id = '".$_GET["code"]."'
                ";
                $statement = $connect->prepare($query);
                $statement->execute();
                $row = $statement->fetch(PDO::FETCH_ASSOC);
                ?>
    <ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="issue_book.php">Issue Book Management</a></li>
        <li class="breadcrumb-item active">Return Book</li>
    </ol>
    <div class="row">
        <div class="col-md-6">
            <?php 
            if($error != '')
            {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">'.$error.'</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }
            ?>
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user-plus"></i> Return Book
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">Book ISBN Number</label>
                            <input type="text" name="book_isbn_number" value="<?php echo $row['book_id']; ?>" class="form-control" readonly />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">User Unique ID</label>
                            <input type="text" name="user_id" value="<?php echo $row['user_id']; ?>" class="form-control" readonly />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Return Confirmation</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="book_return_confirmation" id="book_return_confirmation">
                                <label class="form-check-label" for="book_return_confirmation">
                                    I confirm that the book is returned by User.
                                </label>
                            </div>
                        </div>
                        <div class="mt-4 mb-0">
                            <input type="hidden" name="issue_book_id" value="<?php echo $row['issue_book_id']; ?>" />
                            <input type="submit" name="book_return_button" class="btn btn-primary" value="Return" />
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
    else
    {
    ?>
    <ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Issue Book Management</li>
    </ol>
    <?php 
    if(isset($_GET["msg"]))
    {
        if($_GET["msg"] == 'add')
        {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">New Book Issued Successfully <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        }

        if($_GET["msg"] == 'return')
        {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Book Returned Successfully <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        }
    }
    ?>
    <div class="card mb-4">
        <div class="card-header">
            <div class="row">
                <div class="col col-md-6">
                    <i class="fas fa-table me-1"></i> Issue Book Management
                </div>
                <div class="col col-md-6" align="right">
                    <a href="issue_book.php?action=add" class="btn btn-success btn-sm">Issue Book</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table id="issue_book_table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Book ISBN Number</th>
                        <th>User Unique ID</th>
                        <th>Issue Date</th>
                        <th>Expected Return Date</th>
                        <th>Return Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if($statement->rowCount() > 0)
                {
                    foreach($statement->fetchAll() as $row)
                    {
                        $status = '';
                        if($row['book_issue_status'] == 'Issue')
                        {
                            $status = '<span class="badge bg-danger">Issue</span>';
                        }

                        if($row['book_issue_status'] == 'Return')
                        {
                            $status = '<span class="badge bg-primary">Return</span>';
                        }
                        echo '
                        <tr>
                            <td>'.$row["book_id"].'</td>
                            <td>'.$row["user_id"].'</td>
                            <td>'.$row["issue_date_time"].'</td>
                            <td>'.$row["expected_return_date"].'</td>
                            <td>'.$row["return_date_time"].'</td>
                            <td>'.$status.'</td>
                            <td>
                                <a href="issue_book.php?action=return&code='.$row["issue_book_id"].'" class="btn btn-sm btn-primary">Return</a>
                            </td>
                        </tr>
                        ';
                    }
                }
                else
                {
                    echo '
                    <tr>
                        <td colspan="7" class="text-center">No Data Found</td>
                    </tr>
                    ';
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php 
    }
    ?>
</div>
<script>
$(document).ready(function() {
    $('#issue_book_table').DataTable({
        "pageLength": 10,
        "lengthMenu": [5, 10, 25, 50, 100],
        "order": [],
        "columnDefs": [
            { "orderable": false, "targets": [6] }
        ]
    });
});
</script>
<?php 
include '../footer.php';
?>
</body>
</html>
