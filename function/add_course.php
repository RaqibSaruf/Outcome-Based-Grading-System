<?php
session_start();
if(isset($_SESSION['login'])){

    $instructor_id= $_SESSION['user_id'];

    $course_titleErr=$course_codeErr=$sectionErr=$semesterErr=$yearErr="";

    if(isset($_GET['remove'])){
        $conn = mysqli_connect('localhost', 'root', '','obgs_cse480');
            if(!$conn){
                die("connection failed");
            }else{
                $remove_id = $_GET['remove'];

                $r_course_code = $r_section = $r_semester = "";

                $sql1 = "SELECT * FROM courses WHERE id='$remove_id'";
                $result = mysqli_query($conn, $sql1);
                if (mysqli_num_rows($result) ==1){
                    while($row = mysqli_fetch_assoc($result)) {
                        $r_course_code = $row['course_code'];
                        $r_section = $row['section'];
                        $r_semester = $row['semester'];
                    }
                }else {echo "Error";}
                $temp=explode("'",$r_semester);
                $student_table_name ="student_list_". $_SESSION['user_id']."_". $_SESSION['username']. "_" . $r_course_code ."_".$r_section. "_" . $temp[0]. "_". $temp[1];
                $assessment_table_name ="assessments_". $_SESSION['user_id']."_". $_SESSION['username']. "_" . $r_course_code ."_".$r_section. "_" . $temp[0]. "_". $temp[1];
                $marks_table_name ="marks_". $_SESSION['user_id']."_". $_SESSION['username']. "_" . $r_course_code ."_".$r_section. "_" . $temp[0]. "_". $temp[1];

                $sql2 = "DROP TABLE ".$marks_table_name.", ".$student_table_name;
                if(mysqli_query($conn,$sql2)){
                    $sql3 = "DROP TABLE ".$assessment_table_name;
                    if(mysqli_query($conn,$sql3)){
                        echo "<script> alert('Tables deleted successfully');</script>";
                    }                    
                }else{
                    echo "<script> alert('Error deleting Tables');</script>";
                }

                $sql= "DELETE FROM courses WHERE id='$remove_id'";
                if(mysqli_query($conn,$sql)){
                    echo "<script> alert('Course deleted successfully');</script>";
                }else{
                    echo "<script> alert('Error deleting course');</script>";
                }
            }
        mysqli_close($conn);
    }

    if($_SERVER["REQUEST_METHOD"]=="POST"){
        if( isset($_POST['add'])){
            $course_title=$course_code=$section=$semester=$sem=$year="";            

            if( empty($_POST['course_title'])){
                $course_titleErr="Course title is required";
            }else{
                if(!preg_match("/^[a-zA-Z(\.)?(\s)?]{4,40}$/",$_POST["course_title"])){
                $course_titleErr = "Min 4 and Max 40 letters can be used";
                }else{
                    $course_title=$_POST["course_title"];
                }
            }

            if( empty($_POST['course_code'])){
                $course_codeErr="Course code is required";
            }else{
                $temp = strtoupper($_POST["course_code"]);
                if(!preg_match("/^[A-Z]{2,3}[0-9]{3}$/",$temp)){
                $course_codeErr = "3 letters then 3 numbers are allowed";
                }else{
                    $course_code=$temp;
                }
            }

            if( empty($_POST['section'])){
                $sectionErr="Section is required";
            }else{
                if(!preg_match("/^[0-9]{1,2}$/",$_POST["section"])){
                $sectionErr = "max 2 number is allowed";
                }else{
                    $section=$_POST["section"];
                }
            }

            if( empty($_POST['semester'])){
                $semesterErr="Semester is required";
            }else{
                    $sem=$_POST["semester"];
            }

            if( empty($_POST['year'])){
                $yearErr="Year is required";
            }else{
                if(!preg_match("/^[0-9]{4}$/",$_POST["year"])){
                $yearErr = "pattern (YYYY) is allowed";
                }else{
                    $year=$_POST["year"];
                }
            }

            if(!empty($sem) && !empty($year)){
                $semester = $sem."''".$year;
            }


            if( empty($course_titleErr) && empty($course_codeErr) && empty($sectionErr) && empty($semesterErr) ){
                $conn = mysqli_connect('localhost', 'root', '','obgs_cse480');
                if(!$conn){
                    die("connection failed");
                }else{
                    $sql1 = "INSERT INTO courses (course_title,course_code,section,semester,instructor_id) VALUES ('$course_title', '$course_code', '$section', '$semester', '$instructor_id')";
                    if(mysqli_query($conn,$sql1)){
                        echo "<script> alert('Course added successfully');</script>";
                        $temp=explode("''",$semester);
                        $student_table_name ="student_list_". $_SESSION['user_id']."_". $_SESSION['username']. "_" . $course_code ."_".$section. "_" . $temp[0]. "_". $temp[1];
                        $assessment_table_name ="assessments_". $_SESSION['user_id']."_". $_SESSION['username']. "_" . $course_code ."_".$section. "_" . $temp[0]. "_". $temp[1];
                        $marks_table_name ="marks_". $_SESSION['user_id']."_". $_SESSION['username']. "_" . $course_code ."_".$section. "_" . $temp[0]. "_". $temp[1];

                        $student_list = "CREATE TABLE ".$student_table_name." (
                            s_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                            student_name VARCHAR(30) NOT NULL,
                            student_id VARCHAR(13) NOT NULL
                            )";

                        $assessment = "CREATE TABLE ".$assessment_table_name." (
                            a_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                            name varchar(30),
                            co1 FLOAT DEFAULT 0,
                            co2 FLOAT DEFAULT 0,
                            co3 FLOAT DEFAULT 0,
                            co4 FLOAT DEFAULT 0,
                            exam_in_taken FLOAT DEFAULT 0
                            )";                         

                        $marks = "CREATE TABLE ".$marks_table_name." (
                            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                            s_id INT(6) UNSIGNED,
                            a_id INT(6) UNSIGNED,
                            co1 FLOAT DEFAULT 0,
                            co2 FLOAT DEFAULT 0,
                            co3 FLOAT DEFAULT 0,
                            co4 FLOAT DEFAULT 0,
                            present VARCHAR(2) DEFAULT 'P',
                            check(present= 'P' or present = 'A'),

                            FOREIGN KEY (s_id) REFERENCES ".$student_table_name."(s_id),
                            FOREIGN KEY (a_id) REFERENCES ".$assessment_table_name."(a_id)
                            )";


                        if (mysqli_query($conn, $student_list)) {
                            
                        } else {
                            echo "Error creating table: " . mysqli_error($conn);
                        }
                        
                        if ( mysqli_query($conn, $assessment) ) {
                            
                        } else {
                            echo "Error creating table: " . mysqli_error($conn);
                        }
                        
                        if (mysqli_query($conn, $marks) ) {
                            
                        } else {
                            echo "Error creating table: " . mysqli_error($conn);
                        }

                    }else{
                        echo "error".$sql1." ". mysqli_error($conn);
                    }
                }
                mysqli_close($conn);
            }

        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add course</title>
    <link rel="stylesheet" href="../bootstrap/bootstrap.css">
    <link rel="stylesheet" href="../bootstrap/bootstrap-grid.css">
    
    <script src="../bootstrap/bootstrap.js"></script>
</head>
<body class="bg-dark">
    <div class="container">
        <div class="d-flex justify-content-end">
            <a href="../home.php" class="btn btn-warning mt-2 mt-md-3">Back to Home</a>
        </div>
        
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="p-3 text-light">
            <h5 class="mt-3">Add new course</h5>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="title">Course Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="course_title" placeholder="title">
                        <span class="text-danger"><?php echo $course_titleErr; ?></span>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="code">Course Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="code" name="course_code" placeholder="code">
                        <span class="text-danger"><?php echo $course_codeErr; ?></span>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="section">Section <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="section" name="section" placeholder="section">
                        <span class="text-danger"><?php echo $sectionErr; ?></span>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="semester">Semester <span class="text-danger">*</span></label>
                        <select name="semester" id="semester" class="form-control">
                            <option selected>Select One</option>
                            <option value="spring">Spring</option>
                            <option value="summer">Summer</option>
                            <option value="fall">Fall</option>
                        </select>
                        <span class="text-danger"><?php echo $yearErr; ?></span>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="year">Year <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="year" name="year" placeholder="YYYY">
                        <span class="text-danger"><?php echo $yearErr; ?></span>
                    </div>
                </div>
                <div class="col-md-1">
                <br>
                    <button type="submit" class="form-control btn btn-success mt-md-2" name="add">Add</button>
                </div>
            </div>
        </form>

        <div class="bg-light mt-2 mt-md-5">
            <table class="table table-bordered">
                <tr class="text-center">
                    <td><strong>Course Title</strong></td>
                    <td><strong>Course Code</strong></td>
                    <td><strong>Section</strong></td>
                    <td><strong>Semester</strong></td>
                    <td><strong>Remove</strong></td>
                </tr>

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

                <tr class="text-center">
                    <td><?php echo $row['course_title']; ?></td>
                    <td><?php echo $row['course_code']; ?></td>
                    <td><?php echo $row['section']; ?></td>
                    <td><?php echo $row['semester']; ?></td>
                    <td><a class="btn btn-danger" href="add_course.php?remove=<?php echo $row['id']; ?>">Remove</a></td>
                </tr>
<?php
            }
        }else{
            echo "<tr> <td> No Course Found </td> </tr>";
        }
    }
    mysqli_close($conn);

?>
            </table>
        </div>

    </div>
</body>
</html>




<?php } ?>