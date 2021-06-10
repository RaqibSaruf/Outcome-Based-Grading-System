<?php
session_start();
if(isset($_SESSION['login'])){
    $instructor_id= $_SESSION['user_id'];

    if(isset($_GET['course_id'])){        
        unset($_SESSION['course_id']); 
        $_SESSION['course_id'] = $_GET['course_id'];                    
    }


    if(isset($_SESSION['course_id'])){
        $course_id = $_SESSION['course_id'];
        $conn = mysqli_connect('localhost', 'root', '','obgs_cse480');
            if(!$conn){
                die("connection failed");
            }else{
                $sql1="SELECT * FROM courses WHERE id='$course_id' AND instructor_id='$instructor_id'";
                $result = mysqli_query($conn, $sql1);
                
                if (mysqli_num_rows($result) == 1){
                    while($row = mysqli_fetch_assoc($result)){
                        $course_code = $row['course_code'];
                        $section = $row['section'];
                        $semester = $row['semester'];
                    }
                }
            }
        mysqli_close($conn);

        $temp=explode("'",$semester);
        $student_table_name ="student_list_". $_SESSION['user_id']."_". $_SESSION['username']. "_" . $course_code ."_".$section. "_" . $temp[0]. "_". $temp[1];

    $s_nameErr = $s_idErr= "";


    if(isset($_GET['delete'])){
        $dlt_id = $_GET['delete'];
        $conn = mysqli_connect('localhost', 'root', '','obgs_cse480');
            if(!$conn){
                die("connection failed");
            }else{
                $sql= "DELETE FROM ".$student_table_name." WHERE s_id='$dlt_id'";
                if(mysqli_query($conn,$sql)){
                    echo "<script> alert('Student deleted successfully');</script>";
                }else{
                    echo "<script> alert('Error deleting Assessment');</script>";
                }
            }
        mysqli_close($conn);
    }


    if($_SERVER["REQUEST_METHOD"]=="POST"){


        if(isset($_POST['edit_student'])){

            $conn = mysqli_connect('localhost', 'root', '','obgs_cse480');
                if(!$conn){
                    die("connection failed");
                }else{
                    $id=$_POST['id'];
                    $s_name = $_POST['s_name'];
                    $student_id = $_POST['s_id'];
                    $sql5 = "UPDATE ".$student_table_name." SET student_name='$s_name', student_id='$student_id' WHERE s_id='$id'";
                    
                    if (mysqli_query($conn, $sql5)) {
                        echo "<script> alert('Student Updated successfully');</script>";
                    } else {
                        echo "Error updating record: " . mysqli_error($conn);
                    }
                }
            mysqli_close($conn);
        }


        if(isset($_POST['add'])){
            $s_name = $s_id = "";

            if( empty($_POST['s_name'])){
                $s_nameErr="Student name is required";
            }else{
                if(!preg_match("/^[a-zA-Z(\.)?(\s)?]{4,30}$/",$_POST["s_name"])){
                    $s_nameErr = "Min 4 and Max 30 letter is allowed";
                }else{
                    $s_name = $_POST['s_name'];
                }
                
            }
            if( empty($_POST['s_id'])){
                $s_idErr="Student id is required";
            }else{
                if(!preg_match("/^[0-9]{4}[\-][0-9]{1}[\-][0-9]{2}[\-][0-9]{3}$/",$_POST["s_id"])){
                    $s_idErr = "Invalid id";
                }else{
                    $s_id = $_POST['s_id'];
                }
                
            }
                

                if(empty($s_nameErr) && empty($s_idErr)){
                    $conn = mysqli_connect('localhost', 'root', '','obgs_cse480');
                    if(!$conn){
                        die("connection failed");
                    }else{                                               
                        $sql = "INSERT INTO ".$student_table_name."(student_name,student_id) values('$s_name','$s_id')";
                        if (mysqli_query($conn, $sql)) {
                            echo "<script> alert('Student Added successfully');</script>";
                        } else {
                            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                        }
                        mysqli_close($conn);
                    }
                }
        }
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>

    <link rel="stylesheet" href="../bootstrap/bootstrap.css">
    <link rel="stylesheet" href="../bootstrap/bootstrap-grid.css">
    
    <script src="../bootstrap/bootstrap.js"></script>
</head>
<body class="bg-dark">
    <div class="container">
        <div class="d-flex justify-content-end">
            <a href="../home.php" class="btn btn-warning mt-2 mt-md-3">Back to Home</a>
        </div>
<?php
    if(isset($_GET['edit'])){

    $edit_id = $_GET['edit'];
    $conn = mysqli_connect('localhost', 'root', '','obgs_cse480');
    if(!$conn){
        die("connection failed");
    }else{
        $sql4 = "SELECT * FROM ".$student_table_name." WHERE s_id='$edit_id'";
        $result = mysqli_query($conn, $sql4);
        if (mysqli_num_rows($result) == 1){
            while($row = mysqli_fetch_assoc($result)){
?>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="p-3 text-light">
            <h5 class="mt-3">Edit Student</h5>
            <div class="row">
                <input type="hidden" name="id" value="<?php echo $row['s_id']; ?>">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="s_name">Student Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="s_name" name="s_name" value="<?php echo $row['student_name']; ?>">
                        <span class="text-danger"><?php echo $s_nameErr; ?></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="s_id">Student ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="s_id" name="s_id" value="<?php echo $row['student_id']; ?>">
                        <span class="text-danger"><?php echo $s_idErr; ?></span>
                    </div>
                </div> 
                <div class="col-md-1">
                <br>
                    <button type="submit" class="form-control btn btn-info mt-md-2" name="edit_student">Edit</button>
                </div>
            </div>
        </form>


<?php
        }
        }
    }
    mysqli_close($conn);
    }else{

?>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="p-3 text-light">
            <h5 class="mt-3">Add Student</h5>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="s_name">Student Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="s_name" name="s_name" placeholder="Name">
                        <span class="text-danger"><?php echo $s_nameErr; ?></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="s_id">Student ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="s_id" name="s_id" placeholder="Student ID">
                        <span class="text-danger"><?php echo $s_idErr; ?></span>
                    </div>
                </div>              
                
                <div class="col-md-1">
                <br>
                    <button type="submit" class="form-control btn btn-success mt-md-2" name="add">Add</button>
                </div>
            </div>
        </form>
<?php } ?>

        <div class="row mt-3 text-center">
            <div class="col-md-4 border border-1 border-dark bg-light">
                <h6>Student name</h6>
            </div>
            <div class="col-md-3 border border-1 border-dark bg-light">
                <h6>Student ID</h6>
            </div>            
            <div class="col-md-1 border border-1 border-dark bg-light">
                <h6>Edit</h6>
            </div>
            <div class="col-md-1 border border-1 border-dark bg-light">
                <h6>Delete</h6>
            </div>
        </div>
<?php

        $conn = mysqli_connect('localhost', 'root', '','obgs_cse480');
            if(!$conn){
                die("connection failed");
            }else{
                $sql2="SELECT * FROM ".$student_table_name;
                $result = mysqli_query($conn, $sql2);
                
                if (mysqli_num_rows($result) > 0){
                    while($row = mysqli_fetch_assoc($result)){
?>
        <div class="row text-center">
            <div class="col-md-4 border border-1 border-dark bg-light">
                <p><?php echo $row['student_name']; ?></p>
            </div>
            <div class="col-md-3 border border-1 border-dark bg-light">
                <p><?php echo $row['student_id']; ?></p>
            </div>
            <div class="col-md-1 border border-1 border-dark bg-light">
                <a class="btn btn-sm btn-info" href="student_add.php?edit=<?php echo $row['s_id']; ?>">Edit</a>
            </div>
            <div class="col-md-1 border border-1 border-dark bg-light">
                <a class="btn btn-sm btn-danger" href="student_add.php?delete=<?php echo $row['s_id']; ?>">Delete</a>
            </div>
        </div>                   
<?php
                    }
                }
            }
        mysqli_close($conn);

?>


    </div>
    
</body>
</html>



<?php } } ?>























