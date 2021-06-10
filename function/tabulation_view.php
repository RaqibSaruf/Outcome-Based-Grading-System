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
    <title>Tabualtion sheet</title>

    <link rel="stylesheet" href="../bootstrap/bootstrap.css">
    <link rel="stylesheet" href="../bootstrap/bootstrap-grid.css">
    
    <script src="../bootstrap/bootstrap.js"></script>
</head>
<body class="bg-dark">
    <div class="container bg-primary">
        <div class="d-flex justify-content-end">
            <a href="../home.php" class="btn btn-warning mt-2 mt-md-3">Back to Home</a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered mt-3">
                <thead class="bg-light">
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>

<?php

$total_marks=0;

$sql="SELECT a_id,name,exam_in_taken,(co1+co2+co3+co4)/exam_in_taken as ratio FROM ".$assessment_table_name;
$result = mysqli_query($conn, $sql);
if(mysqli_num_rows($result) > 0){
    while($row = mysqli_fetch_assoc($result)){
        $a_id=$row['a_id'];
        $a_name = $row['name'];
        $exam_in=$row['exam_in_taken'];
        $ratio=$row['ratio'];
        $obtained=$exam_in * $ratio ;
	    $total_marks=$total_marks+$obtained;	
?>
                        <th><?php echo $a_name." (".$obtained.")"; ?></th>
<?php
    }
}
?>
                        <th>Total (<?php echo $total_marks; ?>)</th>
                        <th class="td_class1">Letter Grade</th>
                    </tr>
                </thead>
<?php

$sql="SELECT * FROM ".$student_table_name."	order by student_id";
$result = mysqli_query($conn, $sql);
if(mysqli_num_rows($result) > 0){
    while($row = mysqli_fetch_assoc($result)){
        $s_id = $row['s_id'];
        $student_name=$row['student_name'];
        $student_id=$row['student_id'];
        
        $grand_total=0;
		
?>
                <tbody class="text-light">
                    <tr>
                        <td><?php echo $student_id; ?></td>
			            <td><?php echo $student_name; ?></td>

<?php

$sql1="SELECT a_id,(co1+co2+co3+co4)/exam_in_taken as ratio from ".$assessment_table_name;
$result1 = mysqli_query($conn, $sql1);
if(mysqli_num_rows($result1) > 0){
    while($row1 = mysqli_fetch_assoc($result1)){
        $a_id=$row1['a_id'];
        $ratio=$row1['ratio'];
        
        $sql2="SELECT * FROM ".$marks_table_name." where s_id='$s_id' and a_id='$a_id'";
        $result2 = mysqli_query($conn, $sql2);
        if(mysqli_num_rows($result2) > 0){
            while($row2 = mysqli_fetch_assoc($result2)){
                $co1=$row2['co1'];
			    $co2=$row2['co2'];
			    $co3=$row2['co3'];
			    $co4=$row2['co4'];
            }
            $total=(float)$co1+$co2+$co3+$co4;
			$obtained=$total*$ratio;

			$grand_total=$grand_total+$obtained;
        }
?>
			            <td><?php echo $obtained; ?></td>

<?php

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
			            <td><?php echo $grand_total; ?></td>
			            <td><?php echo $letter_grade; ?></td>
                    </tr>
<?php
    }
}
?>

                    <tr class="bg-warning text-dark">
                        <td colspan="2">Class Average</td>

<?php

$total_avg_obtained=0;

$sql="SELECT a_id,name,(co1+co2+co3+co4)/exam_in_taken as ratio FROM ".$assessment_table_name;
$result = mysqli_query($conn, $sql);
if(mysqli_num_rows($result) > 0){
    while($row = mysqli_fetch_assoc($result)){
        $a_id=$row['a_id'];
        $ratio=$row['ratio'];

        $sql1="SELECT sum(co1) as total_co1,sum(co2) as total_co2,sum(co3) as total_co3,sum(co4) as total_co4,count(a_id) as num FROM ".$marks_table_name." WHERE a_id='$a_id' group by a_id";
        $result1 = mysqli_query($conn, $sql1);
        if(mysqli_num_rows($result1) > 0){
            while($row1 = mysqli_fetch_assoc($result1)){
                $total_co1=$row1['total_co1'];
                $total_co2=$row1['total_co2'];    
                $total_co3=$row1['total_co3'];    
                $total_co4=$row1['total_co4'];

                $obtained=$total_co1 + $total_co2 + $total_co3 +$total_co4;
                $total_num=$row1['num'];
                $avg_obtained=($obtained*$ratio)/$total_num;

                $total_avg_obtained=$total_avg_obtained+$avg_obtained;

?>
                        <td><?php echo round($avg_obtained,2); ?></td>

<?php
            }
        }
    }
}

?>
                        <td><?php echo round($total_avg_obtained,2); ?></td>

<?php

$letter_grade="";
if($total_avg_obtained>=($total_marks * 0.97))$letter_grade="A+";
else if($total_avg_obtained>=($total_marks * 0.9) && $total_avg_obtained<($total_marks * 0.97))$letter_grade="A";
else if($total_avg_obtained>=($total_marks * 0.87) && $total_avg_obtained<($total_marks * 0.9))$letter_grade="A-";
else if($total_avg_obtained>=($total_marks * 0.83) && $total_avg_obtained<($total_marks * 0.87))$letter_grade="B+";
else if($total_avg_obtained>=($total_marks * 0.8) && $total_avg_obtained<($total_marks * 0.83))$letter_grade="B";
else if($total_avg_obtained>=($total_marks * 0.77) && $total_avg_obtained<($total_marks * 0.8))$letter_grade="B-";
else if($total_avg_obtained>=($total_marks * 0.73) && $total_avg_obtained<($total_marks * 0.77))$letter_grade="C+";
else if($total_avg_obtained>=($total_marks * 0.7) && $total_avg_obtained<($total_marks * 0.73))$letter_grade="C";
else if($total_avg_obtained>=($total_marks * 0.67) && $total_avg_obtained<($total_marks * 0.7))$letter_grade="C-";
else if($total_avg_obtained>=($total_marks * 0.63) && $total_avg_obtained<($total_marks * 0.67))$letter_grade="D+";
else if($total_avg_obtained>=($total_marks * 0.6) && $total_avg_obtained<($total_marks * 0.63))$letter_grade="D";
else if($total_avg_obtained < $total_marks * 0.6)$letter_grade="F";
else $letter_grade="I";

?>

                        <td><?php echo $letter_grade; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
    
</body>
</html>



<?php
}
mysqli_close($conn);
} } 

?>























