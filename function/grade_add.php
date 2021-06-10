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
        $assessment_table_name ="assessments_". $_SESSION['user_id']."_". $_SESSION['username']. "_" . $course_code ."_".$section. "_" . $temp[0]. "_". $temp[1];
        $marks_table_name ="marks_". $_SESSION['user_id']."_". $_SESSION['username']. "_" . $course_code ."_".$section. "_" . $temp[0]. "_". $temp[1];

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grade Sheet</title>

    <link rel="stylesheet" href="../bootstrap/bootstrap.css">
    <link rel="stylesheet" href="../bootstrap/bootstrap-grid.css">
    
    <script src="../bootstrap/bootstrap.js"></script>
</head>
<body class="bg-dark">
    <div class="container-fluid bg-primary">
        <div class="d-flex justify-content-end">
            <a href="../home.php" class="btn btn-warning mt-2 mt-md-3">Back to Home</a>
        </div>

        <form action="grade_add.php" method="POST">
            <div class="row p-3">
                <div class="col-md-4">
                

                <select class="form-control" id="select" name="assessment">
                    <option value="-1">Select one</option>

<?php

$conn = mysqli_connect('localhost', 'root', '','obgs_cse480');
if(!$conn){
    die("connection failed");
}else{
    $sql2="SELECT * from ".$assessment_table_name;
    $result = mysqli_query($conn, $sql2);
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)){

?>

                    <option value="<?php echo $row['a_id']; ?>"><?php echo $row['name']; ?></option>

<?php

        }
    }
}
mysqli_close($conn);
?>
                </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="form-control btn btn-danger" name="get_table">Show Table</button>
                </div>
            </div>              
        </form>



<?php


    
    


?>
<?php
if(isset($_POST['get_table'])){
    $a_id = $_POST['assessment'];
    $conn = mysqli_connect('localhost', 'root', '','obgs_cse480');
    if(!$conn){
        die("connection failed");
    }else{
        $sql3="SELECT name,co1,co2,co3,co4,exam_in_taken,(co1+co2+co3+co4)/exam_in_taken as ratio FROM ".$assessment_table_name." WHERE a_id=$a_id";

        $result = mysqli_query($conn, $sql3);
        if (mysqli_num_rows($result) == 1) {
            while($row = mysqli_fetch_assoc($result)){
                $assessment_name = $row['name'];
                $co1 = $row['co1'];
                $co2 = $row['co2'];
                $co3 = $row['co3'];
                $co4 = $row['co4'];
                $exam_in_taken = $row['exam_in_taken'];
                $ratio = $row['ratio'];

                $obtained = $exam_in_taken * $ratio;
                $t_co1 = round($co1/$ratio,2);
                $t_co2 = round($co2/$ratio,2);
                $t_co3 = round($co3/$ratio,2);
                $t_co4 = round($co4/$ratio,2);

        }
    }
}
mysqli_close($conn);
?>

        <div class="table-responsive">
        <table class="table table-bordered mt-3">
            <h5 class="bg-warning p-2"><?php echo $assessment_name; ?></h5>
            <thead class="bg-light">
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>CO1 (<?php echo $t_co1; ?>)</th>
                    <th>CO2 (<?php echo $t_co2; ?>)</th>
                    <th>CO3 (<?php echo $t_co3; ?>)</th>
                    <th>CO4 (<?php echo $t_co4; ?>)</th>
                    <th>Total (<?php echo $exam_in_taken; ?>)</th>
                    <th>Obtained (<?php echo $obtained; ?>)</th>
                    <th>presence</th>
                </tr>
            </thead>
            <tbody>
            <form action="grade_add.php" method="POST">
<?php

$conn = mysqli_connect('localhost', 'root', '','obgs_cse480');
if(!$conn){
    die("connection failed");
}else{
    $sql4 = "SELECT * FROM ".$student_table_name." ORDER BY student_id";
    $result = mysqli_query($conn, $sql4);
    if (mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_assoc($result)){
            $s_id = $row['s_id'];
            $student_name = $row['student_name'];
            $student_id = $row['student_id'];

            $sql5="SELECT * FROM ".$marks_table_name." WHERE s_id='$s_id' and a_id='$a_id'";
            $result1 = mysqli_query($conn, $sql5);
            if (mysqli_num_rows($result1) == 0){
                $temp_co1 = 0;
                $temp_co2 = 0;
                $temp_co3 = 0;
                $temp_co4 = 0;
                $temp_presence = 'P';

                $sql6 = "INSERT INTO ".$marks_table_name."(s_id,a_id,co1,co2,co3,co4,present) VALUES('$s_id','$a_id','$temp_co1', '$temp_co2', '$temp_co3', '$temp_co4', '$temp_presence')";
                mysqli_query($conn, $sql6);
            }

            $sql7="SELECT * FROM ".$marks_table_name." WHERE s_id='$s_id' and a_id='$a_id'";
            $result2 = mysqli_query($conn, $sql7);
            if (mysqli_num_rows($result2) > 0){
                while($row1 = mysqli_fetch_assoc($result2)){
                    $total = (float)$row1['co1']+$row1['co2']+$row1['co3']+$row1['co4'];
                    $obtained = round($total*$ratio,2);
?>
                <tr>
                    
                    <td>
                        <input type="text" class="form-control" name="student_id_<?php echo $row1['id']; ?>" value="<?php echo $student_id; ?>" readonly>
                    </td>
                    <td>
                        <input type="text" class="form-control" name="student_name_<?php echo $row1['id']; ?>" value="<?php echo $student_name; ?>" readonly>
                    </td>
                    <td>
                        <input type="text" class="form-control" name="co1_<?php echo $row1['id']; ?>" value="<?php echo $row1['co1']; ?>">
                    </td>
                    <td>
                        <input type="text" class="form-control" name="co2_<?php echo $row1['id']; ?>" value="<?php echo $row1['co2']; ?>">
                    </td>
                    <td>
                        <input type="text" class="form-control" name="co3_<?php echo $row1['id']; ?>" value="<?php echo $row1['co3']; ?>">
                    </td>
                    <td>
                        <input type="text" class="form-control" name="co4_<?php echo $row1['id']; ?>" value="<?php echo $row1['co4']; ?>">
                    </td>
                    <td>
                        <input type="text" class="form-control" name="total_<?php echo $row1['id']; ?>" value="<?php echo $total; ?>">
                    </td>
                    <td>
                        <input type="text" class="form-control" name="obtain_<?php echo $row1['id']; ?>" value="<?php echo $obtained; ?>">
                    </td>
                    <td>
                        <select class="form-control" id="select1" name="pre_<?php echo $row1['id']; ?>">

                        <?php if($row1['present'] == 'P'){ ?>
                            <option value="<?php echo $row1['present']; ?>">P</option>
                            <option value="A">A</option>
                        <?php }else{ ?>
                            <option value="<?php echo $row1['present']; ?>">A</option>
                            <option value="P">P</option>
                        <?php } ?>
                        </select>
                    </td>
                </tr>

<?php
                }
            }
        }
?>  
                <input type="hidden" name="assessment_id" value="<?php echo $a_id; ?>">
                <tr>
                    <td colspan="9">
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-success" name="submit">Submit</button>
                        </div>                        
                    </td>
                </tr>
<?php
    }
}


mysqli_close($conn);

?>
                
                
                </form>
                
            </tbody>
        </table>
        </div> 


<?php


}
?>

    </div>
    
</body>
</html>



<?php 

if(isset($_POST['submit'])){

    $conn = mysqli_connect('localhost', 'root', '','obgs_cse480');
    if(!$conn){
        die("connection failed");
    }else{
        $sql = "SELECT * FROM ".$student_table_name." ORDER BY student_id";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_assoc($result)){
                $s_id = $row['s_id'];
                $a_id = $_POST['assessment_id'];

                $sql1="SELECT * FROM ".$marks_table_name." WHERE s_id='$s_id' and a_id='$a_id'";
                $result1 = mysqli_query($conn, $sql1);
                if (mysqli_num_rows($result1) > 0){
                    while($row1 = mysqli_fetch_assoc($result1)){
                        $id = $row1['id'];
                        $co1 = $_POST['co1_'.$id];
                        $co2 = $_POST['co2_'.$id];
                        $co3 = $_POST['co3_'.$id];
                        $co4 = $_POST['co4_'.$id];
                        $present = $_POST['pre_'.$id];

                        $sql = "UPDATE ".$marks_table_name." SET co1='$co1',co2='$co2', co3='$co3', co4='$co4', present='$present' WHERE id='$id'";
                        mysqli_query($conn, $sql);
                    }
                }
            }
        }
    }
    mysqli_close($conn);

    echo "<script> alert('Successfully added');</script>";

}





} } ?>















