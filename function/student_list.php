<?php
session_start();
if(isset($_SESSION['login'])){
    $instructor_id= $_SESSION['user_id'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List</title>

    <link rel="stylesheet" href="../bootstrap/bootstrap.css">
    <link rel="stylesheet" href="../bootstrap/bootstrap-grid.css">
    
    <script src="../bootstrap/bootstrap.js"></script>
</head>
<body class="bg-dark">
    <div class="container bg-primary">
        <div class="d-flex justify-content-end">
            <a href="../home.php" class="btn btn-warning mt-2 mt-md-3">Back to Home</a>
        </div>
            <h3 class="mt-3 text-center"><strong>Student List</strong></h3>
         <div class="row mt-3 p-3">
<?php

$conn = mysqli_connect('localhost', 'root', '','obgs_cse480');
if(!$conn){
    die("connection failed");
}else{
    $sql1 = "SELECT * FROM courses WHERE instructor_id='$instructor_id' ORDER BY created_time DESC";

    $result = mysqli_query($conn,$sql1);
    if(mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_assoc($result)){ 

?>
            <div class="col-md-4">
                <div class="card bg-info text-center">
                    <a class="card-body text-light" href="student_add.php?course_id=<?php echo $row['id']; ?>">
                    <h6><strong>Course Code: </strong><?php echo $row['course_code']; ?></h6>
                    <h6><strong>Section: </strong><?php echo $row['section']; ?></h6>
                    <h6><strong>Semester: </strong><?php echo $row['semester']; ?></h6>
                    </a>
                </div>
            </div>
<?php
            }
        }else{
            echo "No Course Found";
        }
    }
    mysqli_close($conn);

?>
         </div>
    </div>
    
</body>
</html>



<?php } ?>























