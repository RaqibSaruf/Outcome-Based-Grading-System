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
    <title>OBE2</title>

    <link rel="stylesheet" href="../bootstrap/bootstrap.css">
    <link rel="stylesheet" href="../bootstrap/bootstrap-grid.css">
    
    <script src="../bootstrap/bootstrap.js"></script>
</head>
<body class="bg-dark">
    <div class="container-fluid bg-primary">
        <div class="d-flex justify-content-end">
            <a href="../home.php" class="btn btn-warning mt-2 mt-md-3">Back to Home</a>
        </div>

<?php

$total_marks=0;
$sql="SELECT a_id,name,exam_in_taken,(co1+co2+co3+co4)/exam_in_taken as ratio FROM ".$assessment_table_name;
$result = mysqli_query($conn, $sql);                
    if (mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_assoc($result)){
		    $a_id=$row['a_id'];
		    $exam_in=$row['exam_in_taken'];
		    $ratio=$row['ratio'];

		    $obtained=$exam_in * $ratio ;
	        $total_marks=$total_marks+$obtained;
    }
}		
?>

        <div class="table-responsive text-center">
            <table class="table table-bordered mt-3">
                <h4>Course Achivement measurement Sheet</h4>
                <thead class="bg-light">
                    <tr>
			            <th colspan="2" rowspan="2"></th>
			            <th colspan="4">CO Distribution</th>
			            <th colspan="8">Individual CO Achivement(Thershold value is 70%)</th>
			            <th rowspan="3">Letter Grade</th>
		            </tr>
                    <tr>			
			            <th>CO1</th>
			            <th>CO2</th>
			            <th>CO3</th>
			            <th>CO4</th>
			            <th>CO1</th>
			            <th>CO2</th>
			            <th>CO3</th>
			            <th>CO4</th>
			            <th>CO1</th>
			            <th>CO2</th>
			            <th>CO3</th>
			            <th>CO4</th>
                    </tr>
<?php 
$t1=$t2=$t3=$t4=0;
$sql="SELECT co1,co2,co3,co4 FROM ".$assessment_table_name;
$result = mysqli_query($conn, $sql);                
if (mysqli_num_rows($result) > 0){
    while($row = mysqli_fetch_assoc($result)){
        $t1=$t1+$row['co1'];
        $t2=$t2+$row['co2'];
        $t3=$t3+$row['co3'];
        $t4=$t4+$row['co4'];
    }
}
?>
                    <tr>
			            <th>Student ID</th>
			            <th>Student Name</th>
			            <th><?php echo $t1; ?></th>
			            <th><?php echo $t2; ?></th>
			            <th><?php echo $t3; ?></th>
			            <th><?php echo $t4; ?></th>
			            <th>100%</th>
			            <th>100%</th>
			            <th>100%</th>
			            <th>100%</th>
			            <th colspan="4">If CO achived(>=70%)then 1,else 0</th>
		            </tr>
                </thead>
                <tbody class="text-light">
<?php 

$sql="SELECT * FROM ".$student_table_name."	ORDER BY student_id";
$result = mysqli_query($conn, $sql);                
if (mysqli_num_rows($result) > 0){
    while($row = mysqli_fetch_assoc($result)){
		$s_id = $row['s_id'];
		$student_name=$row['student_name'];
        $student_id=$row['student_id'];
        
        $t_a_co1=$t_a_co2=$t_a_co3=$t_a_co4=$total_co1=$total_co2=$total_co3=$total_co4=$grand_total=0;
        $co1=$co2=$co3=$co4=0;
?>
		            <tr>
			            <td class="td_class2"><?php echo $student_id; ?></td>
			            <td class="td_class2"><?php echo $student_name; ?></td>
<?php

$sql="SELECT a_id,(co1+co2+co3+co4)/exam_in_taken as ratio,co1,co2,co3,co4 FROM ".$assessment_table_name;
$result1 = mysqli_query($conn, $sql);                
if (mysqli_num_rows($result1) > 0){
    while($row1 = mysqli_fetch_assoc($result1)){
		$a_id=$row1['a_id'];
		$ratio=$row1['ratio'];
		$a_co1=$row1['co1'];
		$a_co2=$row1['co2'];
		$a_co3=$row1['co3'];
        $a_co4=$row1['co4'];
        
		$t_a_co1=$t_a_co1+$a_co1;
		$t_a_co2=$t_a_co2+$a_co2;
		$t_a_co3=$t_a_co3+$a_co3;
        $t_a_co4=$t_a_co4+$a_co4;      

	    $sql="SELECT * FROM ".$marks_table_name." WHERE s_id='$s_id' and a_id='$a_id'";
	    $result2 = mysqli_query($conn, $sql);                
        if (mysqli_num_rows($result2) > 0){
            while($row2 = mysqli_fetch_assoc($result2)){
			    $co1=$row2['co1'];
			    $co2=$row2['co2'];
			    $co3=$row2['co3'];
			    $co4=$row2['co4'];
            }
        }else{
            echo "No data found";
        }
        
        $total=(float)$co1+$co2+$co3+$co4;
		$obtained=$total*$ratio;
		$total_co1=$total_co1+($co1*$ratio);
		$total_co2=$total_co2+($co2*$ratio);
		$total_co3=$total_co3+($co3*$ratio);
		$total_co4=$total_co4+($co4*$ratio);

        $grand_total=$grand_total+$obtained;

        if($t_a_co1 == 0) $t_a_co1= 1;
        if($t_a_co2 == 0) $t_a_co2= 1;
        if($t_a_co3 == 0) $t_a_co3= 1;
        if($t_a_co4 == 0) $t_a_co4= 1;

        $achived_co1=($total_co1 /$t_a_co1)*100;
		$achived_co2=($total_co2 /$t_a_co2)*100;
		$achived_co3=($total_co3 /$t_a_co3)*100;
		$achived_co4=($total_co4 /$t_a_co4)*100;
        
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

    }
}

?>
			            <td><?php echo $total_co1; ?></td>
			            <td><?php echo $total_co2; ?></td>
			            <td><?php echo $total_co3; ?></td>
			            <td><?php echo $total_co4; ?></td>
			            <td><?php echo round($achived_co1,2); ?></td>
			            <td><?php echo round($achived_co2,2); ?></td>
			            <td><?php echo round($achived_co3,2); ?></td>
			            <td><?php echo round($achived_co4,2); ?></td>
<?php 
	$mark1=$mark2=$mark3=$mark4=0;
	
	if($achived_co1 >= 70)$mark1=1;
	else $mark1=0;
	if($achived_co2 >= 70)$mark2=1;
	else $mark2=0;
	if($achived_co3 >= 70)$mark3=1;
	else $mark3=0;
	if($achived_co4 >= 70)$mark4=1;
	else $mark4=0;
?>
			            <td><?php echo $mark1; ?></td>
			            <td><?php echo $mark2; ?></td>
			            <td><?php echo $mark3; ?></td>
			            <td><?php echo $mark4; ?></td>
			            <td><?php echo $letter_grade; ?></td>	
		            </tr>
<?php 
}
}
?>
             </tbody>
            </table>
        </div>

    </div>
    
</body>
</html>

<?php }
mysqli_close($conn);

} } ?>























