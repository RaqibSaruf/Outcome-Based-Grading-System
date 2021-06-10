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
    <title>OBE1</title>

    <link rel="stylesheet" href="../bootstrap/bootstrap.css">
    <link rel="stylesheet" href="../bootstrap/bootstrap-grid.css">
    
    <script src="../bootstrap/bootstrap.js"></script>
</head>
<body class="bg-dark">
    <div class="container-fluid bg-primary">
        <div class="d-flex justify-content-end">
            <a href="../home.php" class="btn btn-warning mt-2 mt-md-3">Back to Home</a>
        </div>

        <div class="table-responsive text-center">
            <table class="table table-bordered mt-3">
                <h4>Course Outcome measurement Sheet</h4>
                <thead class="bg-light">
                    <tr class="bg-success">
                        <th colspan="2"></th>
<?php
$sql="SELECT a_id,name FROM ".$assessment_table_name;
$result = mysqli_query($conn,$sql);
if(mysqli_num_rows($result) > 0){
    while($row =mysqli_fetch_assoc($result)){
        $a_id = $row['a_id'];		
?>
                        <th colspan="5"><?php echo $row['name']; ?></th>
<?php
    }
}
?>
                        <th></th>
                    </tr>
                    <tr class="bg-warning">
                        <th>Student ID</th>
			            <th>Student Name</th>
<?php
$total_marks=0;

$sql="SELECT a_id,name,exam_in_taken,(co1+co2+co3+co4)/exam_in_taken as ratio,co1,co2,co3,co4 FROM ".$assessment_table_name;
$result = mysqli_query($conn,$sql);
if(mysqli_num_rows($result) > 0){
    while($row =mysqli_fetch_assoc($result)){
		$a_id=$row['a_id'];
		$exam_in=$row['exam_in_taken'];
		$ratio=$row['ratio'];

		$obtained=$exam_in * $ratio ;

	$total_marks=$total_marks+$obtained;
		
?>
		                <th>co1 (<?php echo $row['co1']; ?>)</th>
		                <th>co2 (<?php echo $row['co2']; ?>)</th>
		                <th>co3 (<?php echo $row['co3']; ?>)</th>
		                <th>co4 (<?php echo $row['co4']; ?>)</th>
		                <th>Total (<?php echo $obtained; ?>)</th>

<?php
    }
}
?>
		                <th>Grand Total (<?php echo $total_marks; ?>)</th>
                    </tr>
                </thead>
                <tbody class="text-light">
<?php 
$sql="SELECT * from ".$student_table_name."	ORDER BY student_id";
$result = mysqli_query($conn,$sql);
if(mysqli_num_rows($result) > 0){
    while($row =mysqli_fetch_assoc($result)) {	
        $s_id = $row['s_id'];	
		$student_name=$row['student_name'];
		$student_id=$row['student_id'];
$grand_total=0;
?>
		            <tr class="bg-light text-dark">
			            <td><?php echo $student_id; ?></td>
			            <td><?php echo $student_name; ?></td>

<?php
$sql="SELECT a_id,(co1+co2+co3+co4)/exam_in_taken as ratio FROM ".$assessment_table_name;
$result1 = mysqli_query($conn,$sql);
if(mysqli_num_rows($result1) > 0){
    while($row1 =mysqli_fetch_assoc($result1)){
		$a_id=$row1['a_id'];
		$ratio=$row1['ratio'];

	$sql1="SELECT * FROM ".$marks_table_name." WHERE s_id='$s_id' AND a_id='$a_id'";
    $result2 = mysqli_query($conn,$sql1);
    $co1=$co2=$co3=$co4=0;
    if(mysqli_num_rows($result2) > 0){
        while($row2 =mysqli_fetch_assoc($result2)){
			$co1=$row2['co1'];
			$co2=$row2['co2'];
			$co3=$row2['co3'];
			$co4=$row2['co4'];
            }
        }	
			$total=(float)$co1+$co2+$co3+$co4;
			$obtained=$total*$ratio;

			$grand_total=$grand_total+$obtained;
?>
		                <td><?php echo $co1*$ratio; ?></td>
		                <td><?php echo $co2*$ratio; ?></td>
		                <td><?php echo $co3*$ratio; ?></td>
		                <td><?php echo $co4*$ratio; ?></td>
		                <td><?php echo $obtained; ?></td>
<?php
}
}
?>	
			            <td><?php echo $grand_total; ?></td>			
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























