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
        $assessment_table_name ="assessments_". $_SESSION['user_id']."_". $_SESSION['username']. "_" . $course_code ."_".$section. "_" . $temp[0]. "_". $temp[1];

    $a_nameErr = $exam_inErr= "";


    if(isset($_GET['delete'])){
        $dlt_id = $_GET['delete'];
        $conn = mysqli_connect('localhost', 'root', '','obgs_cse480');
            if(!$conn){
                die("connection failed");
            }else{
                $sql= "DELETE FROM ".$assessment_table_name." WHERE a_id='$dlt_id'";
                if(mysqli_query($conn,$sql)){
                    echo "<script> alert('Assessment deleted successfully');</script>";
                }else{
                    echo "<script> alert('Error deleting Assessment');</script>";
                }
            }
        mysqli_close($conn);
    }


    if($_SERVER["REQUEST_METHOD"]=="POST"){


        if(isset($_POST['edit_assessment'])){

            $conn = mysqli_connect('localhost', 'root', '','obgs_cse480');
                if(!$conn){
                    die("connection failed");
                }else{
                    $id=$_POST['id'];
                    $a_name = $_POST['a_name'];
                    $co1 = $_POST['co1'];
                    $co2 = $_POST['co2'];
                    $co3 = $_POST['co3'];
                    $co4 = $_POST['co4'];
                    $exam_in = $_POST['exam_in'];
                    $sql5 = "UPDATE ".$assessment_table_name." SET name='$a_name', co1='$co1',co2='$co2',co3='$co3',co4='$co4', exam_in_taken='$exam_in' WHERE a_id='$id'";
                    
                    if (mysqli_query($conn, $sql5)) {
                        echo "<script> alert('Assessment Updated successfully');</script>";
                    } else {
                        echo "Error updating record: " . mysqli_error($conn);
                    }
                }
            mysqli_close($conn);
        }


        if(isset($_POST['add'])){
            $a_name = $co1 = $co2 = $co3 = $co4 = $exam_in = "";

            if( empty($_POST['a_name'])){
                $a_nameErr="Assessment name is required";
            }else{
                $a_name = $_POST['a_name'];
            }
            if( empty($_POST['exam_in'])){
                $exam_inErr="required";
            }else{
                $exam_in = $_POST['exam_in'];
            }

                $co1 = $_POST['co1'];
                $co2 = $_POST['co2'];
                $co3 = $_POST['co3'];
                $co4 = $_POST['co4'];
                

                if(empty($a_nameErr) && empty($exam_inErr)){
                    $conn = mysqli_connect('localhost', 'root', '','obgs_cse480');
                    if(!$conn){
                        die("connection failed");
                    }else{                                               
                        $sql = "INSERT INTO ".$assessment_table_name."(name,co1,co2,co3,co4,exam_in_taken) values('$a_name','$co1','$co2','$co3','$co4','$exam_in')";
                        if (mysqli_query($conn, $sql)) {
                            echo "<script> alert('Assessment Added successfully');</script>";
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
    <title>Add Assessments</title>

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
        $sql4 = "SELECT * FROM ".$assessment_table_name." WHERE a_id='$edit_id'";
        $result = mysqli_query($conn, $sql4);
        if (mysqli_num_rows($result) == 1){
            while($row = mysqli_fetch_assoc($result)){
?>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="p-3 text-light">
            <h5 class="mt-3">Edit assessment</h5>
            <div class="row">
                <input type="hidden" name="id" value="<?php echo $row['a_id']; ?>">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="a_name">Assessment Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="a_name" name="a_name" value="<?php echo $row['name']; ?>">
                        <span class="text-danger"><?php echo $a_nameErr; ?></span>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label for="co1">CO1</label>
                        <input type="text" class="form-control" id="co1" name="co1" value="<?php echo $row['co1']; ?>">
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label for="co2">CO2</label>
                        <input type="text" class="form-control" id="co2" name="co2" value="<?php echo $row['co2']; ?>">
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label for="co3">CO3</label>
                        <input type="text" class="form-control" id="co3" name="co3" value="<?php echo $row['co3']; ?>">
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label for="co4">CO4</label>
                        <input type="text" class="form-control" id="co4" name="co4" value="<?php echo $row['co4']; ?>">
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label for="exam_in">Exam In <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="exam_in" name="exam_in" value="<?php echo $row['exam_in_taken']; ?>">
                        <span class="text-danger"><?php echo $exam_inErr; ?></span>
                    </div>
                </div>
                <div class="col-md-1">
                <br>
                    <button type="submit" class="form-control btn btn-info mt-md-2" name="edit_assessment">Edit</button>
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
            <h5 class="mt-3">Add assessment</h5>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="a_name">Assessment Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="a_name" name="a_name" placeholder="Assessment name">
                        <span class="text-danger"><?php echo $a_nameErr; ?></span>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label for="co1">CO1</label>
                        <input type="text" class="form-control" id="co1" name="co1" value="0">
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label for="co2">CO2</label>
                        <input type="text" class="form-control" id="co2" name="co2" value="0">
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label for="co3">CO3</label>
                        <input type="text" class="form-control" id="co3" name="co3" value="0">
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label for="co4">CO4</label>
                        <input type="text" class="form-control" id="co4" name="co4" value="0">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="exam_in">Exam In <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="exam_in" name="exam_in" placeholder="0">
                        <span class="text-danger"><?php echo $exam_inErr; ?></span>
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
            <div class="col-md-3 border border-1 border-dark bg-success">
                <h5>Assessment name</h5>
            </div>
            <div class="col-md-1 border border-1 border-dark bg-success">
                <h5>CO1</h5>
            </div>
            <div class="col-md-1 border border-1 border-dark bg-success">
                <h5>CO2</h5>
            </div>
            <div class="col-md-1 border border-1 border-dark bg-success">
                <h5>CO3</h5>
            </div>
            <div class="col-md-1 border border-1 border-dark bg-success">
                <h5>CO4</h5>
            </div>
            <div class="col-md-1 border border-1 border-dark bg-success">
                <h5>Weight</h5>
            </div>
            <div class="col-md-1 border border-1 border-dark bg-success">
                <h5>Exam In</h5>
            </div>
            <div class="col-md-1 border border-1 border-dark bg-success">
                <h5>Ratio</h5>
            </div>
            <div class="col-md-1 border border-1 border-dark bg-success">
                <h5>Edit</h5>
            </div>
            <div class="col-md-1 border border-1 border-dark bg-success">
                <h5>Delete</h5>
            </div>
        </div>
<?php

        $conn = mysqli_connect('localhost', 'root', '','obgs_cse480');
            if(!$conn){
                die("connection failed");
            }else{
                $sql2="SELECT * FROM ".$assessment_table_name;
                $result = mysqli_query($conn, $sql2);
                
                if (mysqli_num_rows($result) > 0){
                    while($row = mysqli_fetch_assoc($result)){
?>
        <div class="row text-center">
            <div class="col-md-3 border border-1 border-dark bg-light">
                <p><?php echo $row['name']; ?></p>
            </div>
            <div class="col-md-1 border border-1 border-dark bg-light">
                <p><?php echo $row['co1']; ?></p>
            </div>
            <div class="col-md-1 border border-1 border-dark bg-light">
                <p><?php echo $row['co2']; ?></p>
            </div>
            <div class="col-md-1 border border-1 border-dark bg-light">
                <p><?php echo $row['co3']; ?></p>
            </div>
            <div class="col-md-1 border border-1 border-dark bg-light">
                <p><?php echo $row['co4']; ?></p>
            </div>
            <div class="col-md-1 border border-1 border-dark bg-light">
                <p><?php $total = ($row['co1']+$row['co2']+$row['co3']+$row['co4']);
                    echo $total;
                ?></p>
            </div>
            <div class="col-md-1 border border-1 border-dark bg-light">
                <p><?php echo $row['exam_in_taken']; ?></p>
            </div>
            <div class="col-md-1 border border-1 border-dark bg-light">
                <p><?php echo $total/$row['exam_in_taken']; ?></p>
            </div>
            <div class="col-md-1 border border-1 border-dark bg-light">
                <a class="btn btn-sm btn-info" href="assessment_add.php?edit=<?php echo $row['a_id']; ?>">Edit</a>
            </div>
            <div class="col-md-1 border border-1 border-dark bg-light">
                <a class="btn btn-sm btn-danger" href="assessment_add.php?delete=<?php echo $row['a_id']; ?>">Delete</a>
            </div>
        </div>                   
<?php
                    }
                }



                $sql3=" SELECT sum(co1) as total_co1,sum(co2) as total_co2,sum(co3) as total_co3,sum(co4) as total_co4 from ".$assessment_table_name;
                $result = mysqli_query($conn, $sql3);
                if (mysqli_num_rows($result) > 0){
                    while($row3 = mysqli_fetch_assoc($result)){
                        $total_co1=$row3['total_co1'];
                        $total_co2=$row3['total_co2'];
                        $total_co3=$row3['total_co3'];
                        $total_co4=$row3['total_co4'];
                        $total_weight=$total_co1+$total_co2+$total_co3+$total_co4;

?>

        <div class="row text-center">
            <div class="col-md-3 border border-1 border-dark bg-warning">
                <h6>Total</h6>
            </div>
            <div class="col-md-1 border border-1 border-dark bg-warning">
                <h6><?php echo $total_co1; ?></h6>
            </div>
            <div class="col-md-1 border border-1 border-dark bg-warning">
                <h6><?php echo $total_co2; ?></h6>
            </div>
            <div class="col-md-1 border border-1 border-dark bg-warning">
                <h6><?php echo $total_co3; ?></h6>
            </div>
            <div class="col-md-1 border border-1 border-dark bg-warning">
                <h6><?php echo $total_co4; ?></h6>
            </div>
            <div class="col-md-1 border border-1 border-dark bg-warning">
                <h6><?php echo $total_weight; ?></h6>
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























