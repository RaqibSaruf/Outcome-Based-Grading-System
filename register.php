<?php


$fullnameErr = $emailErr = $mobileErr = $usernameErr = $passwordErr = $confirm_passwordErr = "";

$nameRE = "/^[a-zA-Z(\.)?(\s)?]{4,30}$/";
$passRE = "/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,20}$/";
$specialcharRE = "/[^a-zA-Z\d]/";
$mobileRE= "/^[0-9]{11}$/";
$userRE="/^[a-zA-Z0-9#$%_]+$/";

if(isset($_POST['register'])){

    $fullname = $email = $mobile = $username = $password = $confirm_password = "";

    if($_SERVER["REQUEST_METHOD"]=="POST"){


            if(empty($_POST["fullname"])){
                $fullnameErr = "Name is required";
            }else{
                if(!preg_match($nameRE,$_POST["fullname"])){
                $nameErr = "Name should be 4 to 30 letters and no special character is allowed";
                }else{
                    $fullname=$_POST["fullname"];
                }
            }

            if(empty($_POST["email"])){
                $emailErr = "Email is required";
            }else{
                if(!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){
                    $emailErr = "Invalid Email format";
                }else{
                    $email=$_POST["email"];
                }
            }

            if(empty($_POST["mobile"])){
                $mobileErr = "Mobile Number is required";
            }else{
                if(!preg_match($mobileRE,$_POST["mobile"])){
                $mobileErr = "Must be valid mobile number with in 11 digits";
                }else{
                    $mobile=$_POST["mobile"];
                }
            }


            if(empty($_POST["username"])){
                $usernameErr = "Name is required";
            }else{
                if(!preg_match($userRE,$_POST["username"])){
                $usernameErr = "any letter and digit and ( #,$,%,_ ) is allowed";
                }else{
                    $username=$_POST["username"];
                }
            }

            if(empty($_POST["password"])){
                $passwordErr = "Password is required";
            }else{
                if(!preg_match($passRE, $_POST["password"])){
                $passwordErr = "min 8 to max 20 letter, number and must be one capital, one small, one digit allowed";
                }else{
                    $password=$_POST["password"];
                }
            }
        
            if(empty($_POST["confirm_password"])){
                $confirm_passwordErr = "Confirm Password is required";
            }else{
                if($_POST["confirm_password"] != $_POST["password"]){
                    $confirm_passwordErr = "Password not match";
                }else{
                    $confirm_password=md5($password);
                }
            }




            if(empty($fullnameErr) && empty($emailErr) && empty($mobileErr) && empty($usernameErr) && empty($passwordErr) && empty($confirm_passwordErr)){
                
                $conn = mysqli_connect('localhost', 'root', '','obgs_cse480');

                if(!$conn){
                    die("connection failed");
                }else{
                    $sql = "SELECT * FROM instructor WHERE username='$username'";

                    $result = mysqli_query($conn, $sql);

                    if(mysqli_num_rows($result) > 0){
                        $usernameErr = "Username already exists";
                    }else{
                        $sql1 = "INSERT INTO instructor (fullname,email,mobile,username,pass) VALUES ('$fullname', '$email', '$mobile', '$username', '$confirm_password')";

                        if(mysqli_query($conn,$sql1)){
                            echo "<script> alert('Register successfully');window.location.href='index.php';</script>";
                        }else{
                            echo "error".$sql1." ". mysqli_error($conn);
                        }
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
    <title>LOGIN/REGISTER</title>

    <?php    include("css-link.php");  ?>
    
</head>
<body class="bg-dark">

    <div class="container">
        <div class="d-flex justify-content-center">
            <div class="card mt-3">
                <div class="card-header text-center"><h4 class="text-info">Register</h4></div>
                <div class="card-body">
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                        <div class="form-group">
                            <label for="fullname">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="fullname" name="fullname">
                            <span class="text-danger"><?php echo $fullnameErr; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email">
                            <span class="text-danger"> <?php echo $emailErr; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="mobile">Phone <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="mobile" name="mobile">
                            <span class="text-danger"> <?php echo $mobileErr; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="username">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username">
                            <span class="text-danger"> <?php echo $usernameErr; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="password">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password">
                            <span class="text-danger"> <?php echo $passwordErr; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="confirmpassword">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="confirmpassword" name="confirm_password">
                            <span class="text-danger"> <?php echo $confirm_passwordErr; ?></span>
                        </div>
                        
                        <button type="submit" class="form-control btn btn-success" name="register">Submit</button>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <a class="card-link text-success" href="index.php">Sign IN Now??</a>
                </div>
            </div>
        </div>
    </div>
    
</body>
</html>