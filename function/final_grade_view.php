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
    <title>Final grade sheet</title>

    <link rel="stylesheet" href="../bootstrap/bootstrap.css">
    <link rel="stylesheet" href="../bootstrap/bootstrap-grid.css">
    
    <script src="../bootstrap/bootstrap.js"></script>
</head>
<body class="bg-dark">
    <div class="container bg-primary">
        <div class="d-flex justify-content-end">
            <a href="../home.php" class="btn btn-warning mt-2 mt-md-3">Back to Home</a>
        </div>
<?php

$total_marks=0;
   
    $conn = mysqli_connect('localhost', 'root', '','obgs_cse480');
        if(!$conn){
            die("connection failed");
        }else{

            $student_id = $student_name = $grand_total = $letter_grade ="";


            $sql="SELECT a_id,exam_in_taken,(co1+co2+co3+co4)/exam_in_taken as ratio from ".$assessment_table_name;
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0){
                while($row = mysqli_fetch_assoc($result)){
                    $a_id = $row['a_id'];
                    $exam_in=$row['exam_in_taken'];
                    $ratio=$row['ratio'];
                    $obtained=$exam_in * $ratio ;
                    $total_marks=$total_marks+$obtained;
                }
            }

?>
        <div class="table-responsive">
            <table class="table table-bordered mt-3">
                <thead class="bg-light">
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Grand Total (<?php echo $total_marks; ?>)</th>
                        <th>Letter Grade</th>
                    </tr>
                </thead>

<?php 

$sql="SELECT * FROM ".$student_table_name." order by student_id";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0){
    while($row = mysqli_fetch_assoc($result)){
        $s_id = $row['s_id'];
        $student_name=$row['student_name'];
        $student_id=$row['student_id'];

        $grand_total= $ratio= $co1= $co2= $co3=$co4 = $total = $obtained =0;
        

        $sql1="SELECT a_id,(co1+co2+co3+co4)/exam_in_taken as ratio FROM ".$assessment_table_name;
        $result1 = mysqli_query($conn, $sql1);
        if (mysqli_num_rows($result1) > 0){
            while($row1 = mysqli_fetch_assoc($result1)){
                $a_id=$row1['a_id'];
                $ratio=$row1['ratio'];
                
                $sql2="SELECT * FROM ".$marks_table_name." WHERE s_id='$s_id' AND a_id='$a_id'";
                $result2 = mysqli_query($conn, $sql2);
                if (mysqli_num_rows($result2) > 0){
                    while($row2 = mysqli_fetch_assoc($result2)){
                        $co1=$row2['co1'];
			            $co2=$row2['co2'];
			            $co3=$row2['co3'];
			            $co4=$row2['co4'];
                    }
                }

                $total=(float)$co1+$co2+$co3+$co4;
			    $obtained=$total*$ratio;
			    $grand_total=$grand_total+$obtained;

            }
        }
       
        $letter_grade="";
		if($grand_total>=($total_marks * 0.97))$letter_grade="A+";
		else if($grand_total>=($total_marks * 0.9) && $grand_total<($total_marks * 0.97))$letter_grade="A";
		else if($grand_total>=($total_marks * 0.87) && $grand_total<($total_marks * 0.9))$letter_grade="A-";
		else if($grand_total>=($total_marks * 0.83) && $grand_total<($total_marks * 0.87))$letter_grade="B+";
		else if($grand_total>=($total_marks * 0.8) && $grand_total<($total_marks * 0.83))$letter_grade="B";
		else if($grand_total>=($total_marks * 0.77) && $grand_total<($total_marks * 0.8))$letter_grade="B-";
		else if($grand_total>=($total_marks * 0.73) && $grand_total<($total_marks * 0.77))$letter_grade="C+";
		else if($grand_total>=($total_marks * 0.7) && $grand_total<($total_marks * 0.73))$letter_grade="C";
		else if($grand_total>=($total_marks * 0.67) && $grand_total<($total_marks * 0.7))$letter_grade="C-";
		else if($grand_total>=($total_marks * 0.63) && $grand_total<($total_marks * 0.67))$letter_grade="D+";
		else if($grand_total>=($total_marks * 0.6) && $grand_total<($total_marks * 0.63))$letter_grade="D";
		else if($grand_total < $total_marks * 0.6)$letter_grade="F";
		else $letter_grade="I";
?>
                <tbody class="text-light">
                    <tr>
                        <td><?php echo $student_id; ?></td>
			            <td><?php echo $student_name; ?></td>
			            <td><?php echo round($grand_total,2); ?></td>
			            <td><?php echo $letter_grade; ?></td>
                    </tr>
                </tbody>
<?php
    }
}
?>
            </table>
        </div>

    </div>
    
</body>
</html>



<?php }
mysqli_close($conn);

} } ?>
























