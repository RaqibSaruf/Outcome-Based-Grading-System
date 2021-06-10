<?php
session_start();
if(isset($_SESSION['login'])){
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <?php    include("css-link.php");  ?>

</head>
<body class="bg-dark">
    <div class="container">
        <div class="row">


        <!-- Content -->
            <div class="col-12 col-md-9 order-md-last">
                <div class="card bg-info mt-3 p-3">
                    
                    <div class="row">

                        <div class="col-12 col-md-4 mt-2">
                            <div class="card bg-light p-2">
                                <a class="card-link text-primary" href="function/add_course.php">
                                    <div class="card-body">
                                        <h4 class="text-center">Add Course</h4>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <div class="col-12 col-md-4 mt-2">
                            <div class="card bg-light p-2">
                                <a class="card-link text-primary" href="function/assessment.php">
                                    <div class="card-body">
                                        <h4 class="text-center">Assessments</h4>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mt-2">
                            <div class="card bg-light p-2">
                                <a class="card-link text-primary" href="function/student_list.php">
                                    <div class="card-body">
                                        <h4 class="text-center">Students List</h4>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mt-2">
                            <div class="card bg-light p-2">
                                <a class="card-link text-primary" href="function/grade_sheet.php">
                                    <div class="card-body">
                                        <h4 class="text-center">Grade Sheet</h4>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mt-2">
                            <div class="card bg-light p-2">
                                <a class="card-link text-primary" href="function/final_grade_sheet.php">
                                    <div class="card-body">
                                        <h4 class="text-center">Final Grade Sheet</h4>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mt-2">
                            <div class="card bg-light p-2">
                                <a class="card-link text-primary" href="function/tabulation.php">
                                    <div class="card-body">
                                        <h4 class="text-center">Tabulation</h4>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mt-2">
                            <div class="card bg-light p-2">
                                <a class="card-link text-primary" href="function/co_distribution.php">
                                    <div class="card-body">
                                        <h4 class="text-center">OBE1</h4>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mt-2">
                            <div class="card bg-light p-2">
                                <a class="card-link text-primary" href="function/co_achive.php">
                                    <div class="card-body">
                                        <h4 class="text-center">OBE2</h4>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mt-2">
                            <div class="card bg-light p-2">
                                <a class="card-link text-primary" href="function/summary.php">
                                    <div class="card-body">
                                        <h4 class="text-center">Summury</h4>
                                    </div>
                                </a>
                            </div>
                        </div>

                    </div>

                </div>
            </div>



            <!-- Profile -->
            <div class="col-12 col-md-3 order-md-first">
                <div class="card bg-primary mt-3 p-3">
                    <div class="mt-5 d-flex justify-content-center">
                        <img src="avatar.jpg" class="img-fluid" style="height: 120px; border-radius:50%;">
                    </div>
                    <h6 class="text-center mt-2"><?php echo $_SESSION['username']; ?></h6>
                    <h3 class="text-center mt-2"><?php echo $_SESSION['name']; ?></h3>
                    <h5 class="text-center mt-1"><?php echo $_SESSION['email']; ?></h5>
                    <h5 class="text-center mt-1"><?php echo $_SESSION['mobile']; ?></h5>
                    <a class="card-link text-light text-center" href="change_password.php">Change Password</a>
                    <a href="logout.php?logout=1" class="btn btn-light mt-2">Log Out</a>
                </div>
                
            </div>            
        </div>
    </div>
    
</body>
</html>




















<?php
}else{
    header("Location: index.php");
}
?>