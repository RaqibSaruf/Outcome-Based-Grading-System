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

        <div class="row">
            <div class="col-md-3">
                <div class="row">
                    <div class="col-12">
                        <a class="btn btn-warning w-100 m-2 p-2 text-center font-weight-bold" style="height: 100px; font-size: 26px;" href="summary_table.php?content=grade_distribution">Grade Distribution</a>
                    </div>
                    <div class="col-12">
                        <a class="btn btn-warning w-100 m-2 p-2 text-center font-weight-bold" style="height: 100px; font-size: 26px;" href="summary_table.php?content=co_attainment">CO Attainment</a>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
            
<?php
if(isset($_GET['content'])){
    if($_GET['content'] == 'grade_distribution'){

        $total_marks=0;
        $sql="SELECT a_id,exam_in_taken,(co1+co2+co3+co4)/exam_in_taken as ratio FROM ".$assessment_table_name;
        $result = mysqli_query($conn, $sql);                
        if (mysqli_num_rows($result) > 0 ){
            while($row = mysqli_fetch_assoc($result)){
		        $a_id=$row['a_id'];
		        $exam_in=$row['exam_in_taken'];
		        $ratio=$row['ratio'];

		        $obtained=$exam_in * $ratio ;
	            $total_marks=$total_marks+$obtained;		
            }
        }

        $A_plus=0;$A=0;$A_minus=0;$B_plus=0;$B=0;$B_minus=0;$C_plus=0;$C=0;$C_minus=0;$D_plus=0;$D=0;$F=0;$I=0;
        $total_student=0;
	
        $sql="SELECT * FROM ".$student_table_name." ORDER BY student_id";
        $result = mysqli_query($conn, $sql);                
        if (mysqli_num_rows($result) > 0 ){
            while($row = mysqli_fetch_assoc($result)){
			    $s_id = $row['s_id'];
			    $student_name=$row['student_name'];
			    $student_id=$row['student_id'];
		        $grand_total=0;

        $sql="SELECT a_id,(co1+co2+co3+co4)/exam_in_taken as ratio FROM ".$assessment_table_name;
        $result1 = mysqli_query($conn, $sql);                
        if (mysqli_num_rows($result1) > 0 ){
            while($row1 = mysqli_fetch_assoc($result1)){
		        $a_id=$row1['a_id'];
                $ratio=$row1['ratio'];
                
        $co1=$co2=$co3=$co4=0;

        $sql="SELECT * FROM ".$marks_table_name." WHERE s_id='$s_id' AND a_id='$a_id'";
        $result2 = mysqli_query($conn, $sql);                
        if (mysqli_num_rows($result2) > 0 ){
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
		if($grand_total>=($total_marks * 0.97))$A_plus ++;
		else if($grand_total>=($total_marks * 0.9) && $grand_total<($total_marks * 0.97))$A ++;
		else if($grand_total>=($total_marks * 0.87) && $grand_total<($total_marks * 0.9))$A_minus ++;
		else if($grand_total>=($total_marks * 0.83) && $grand_total<($total_marks * 0.87))$B_plus ++;
		else if($grand_total>=($total_marks * 0.8) && $grand_total<($total_marks * 0.83))$B ++;
		else if($grand_total>=($total_marks * 0.77) && $grand_total<($total_marks * 0.8))$B_minus ++;
		else if($grand_total>=($total_marks * 0.73) && $grand_total<($total_marks * 0.77))$C_plus ++;
		else if($grand_total>=($total_marks * 0.7) && $grand_total<($total_marks * 0.73))$C ++;
		else if($grand_total>=($total_marks * 0.67) && $grand_total<($total_marks * 0.7))$C_minus ++;
		else if($grand_total>=($total_marks * 0.63) && $grand_total<($total_marks * 0.67))$D_plus ++;
		else if($grand_total>=($total_marks * 0.6) && $grand_total<($total_marks * 0.63))$D ++;
		else if($grand_total < $total_marks * 0.6)$F ++;
		else $I ++;
		$total_student ++;
	    }	
    }
	
$per_A_plus=$A_plus/$total_student*100;
$per_A=$A/$total_student*100;
$per_A_minus=$A_minus/$total_student*100;
$per_B_plus=$B_plus/$total_student*100;
$per_B=$B/$total_student*100;
$per_B_minus=$B_minus/$total_student*100;
$per_C_plus=$C_plus/$total_student*100;
$per_C=$C/$total_student*100;
$per_C_minus=$C_minus/$total_student*100;
$per_D_plus=$D_plus/$total_student*100;
$per_D=$D/$total_student*100;
$per_F=$F/$total_student*100;
$per_I=$I/$total_student*100;



?>       
                <div class="table-responsive">
                    <table class="table table-bordered mt-3">
                        <thead>
                            <tr>                                
                                <th>Total Student</th>
                                <th>A+</th>
                                <th>A</th>
                                <th>A-</th>
                                <th>B+</th>
                                <th>B</th>
                                <th>B-</th>
                                <th>C+</th>
                                <th>C</th>
                                <th>C-</th>
                                <th>D+</th>
                                <th>D</th>
                                <th>F</th>
                                <th>I</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo $total_student; ?></td>
                                <td><?php echo $per_A_plus; ?>%</td>
                                <td><?php echo $per_A; ?>%</td>
                                <td><?php echo $per_A_minus; ?>%</td>
                                <td><?php echo $per_B_plus; ?>%</td>
                                <td><?php echo $per_B; ?>%</td>
                                <td><?php echo $per_B_minus; ?>%</td>
                                <td><?php echo $per_C_plus; ?>%</td>
                                <td><?php echo $per_C; ?>%</td>
                                <td><?php echo $per_C_minus; ?>%</td>
                                <td><?php echo $per_D_plus; ?>%</td>
                                <td><?php echo $per_D; ?>%</td>
                                <td><?php echo $per_F; ?>%</td>
                                <td><?php echo $per_I; ?>%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

<?php
}
if($_GET['content'] == 'co_attainment'){
    echo "I will add it letter";
}

}
?>

            </div>
        </div>


    </div>
    
</body>
</html>

<?php }
mysqli_close($conn);

} } ?>























