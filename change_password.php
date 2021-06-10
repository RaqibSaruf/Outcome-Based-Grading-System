<?php
session_start();

if(isset($_SESSION['login'])){


        $username = $_SESSION['username'];

        $old_passErr=$new_passwordErr= $confirm_passwordErr="";

        $passRE = "/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,20}$/";


        if($_SERVER["REQUEST_METHOD"]=="POST"){
            $old_pass=$new_password=$confirm_password=$id=$pass="";

            if(isset($_POST['con_pass'])){

                $conn = mysqli_connect('localhost', 'root', '','obgs_cse480');

                if(!$conn){
                    die("connection failed");
                }else{
                    $sql = "SELECT * FROM instructor WHERE username='$username'";
                    $result = mysqli_query($conn, $sql);

                    if(mysqli_num_rows($result) == 1){
                        while($row = mysqli_fetch_assoc($result)){
                            $id= $row["id"];
                            $pass=$row["pass"];
                        }
                    }else{
                        echo "User not found";
                    }
                }

                

                if($_POST['old_pass'] != $pass){
                    $old_passErr = "Wrong Password";
                }


                if(empty($_POST["new_password"])){
                    $new_passwordErr = "New Password is required";
                }elseif($pass == $_POST['new_password']){
                    $new_passwordErr = "Can't use old password";
                }else{
                    if(!preg_match($passRE, $_POST["new_password"])){
                    $new_passwordErr = "min 8 to max 20 letter, number and must be one capital, one small, one digit allowed";
                    }else{
                        $new_password=$_POST["new_password"];
                    }
                }
            
                if(empty($_POST["confirm_password"])){
                    $confirm_passwordErr = "Confirm Password is required";
                }else{
                    if($_POST["confirm_password"] != $_POST["new_password"]){
                        $confirm_passwordErr = "Password not match";
                    }else{
                        $confirm_password=$new_password;
                    }
                }

                if(empty($old_passErr) && empty($new_passwordErr) && empty($confirm_passwordErr)){
                    if(!$conn){
                        die("connection failed");
                    }else{
                        $sql = "UPDATE instructor SET pass='$confirm_password' WHERE id='$id'";
                        if(mysqli_query($conn,$sql)){
                            echo "<script> alert('Password updated successfully');window.location.href='home.php';</script>";
                        }else{
                            echo "<script> alert('Error to change password');window.location.href='change_password.php';</script>";
                        }
                    }
                }


                mysqli_close($conn);

            }
        }

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>

    <?php    include("css-link.php");  ?>
</head>
<body class="bg-dark">

    <div class="container">
        <div class="d-flex justify-content-center">
            <div class="card mt-5">
                <div class="card-header text-center"><h4 class="text-success">Change Password</h4></div>
                <div class="card-body">
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                        <div class="form-group">
                            <label for="old">Old Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="old" name="old_pass">
                            <span class="text-danger"><?php echo $old_passErr; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="new_password">New Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="new_password" name="new_password">
                            <span class="text-danger"> <?php echo $new_passwordErr; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                            <span class="text-danger"> <?php echo $confirm_passwordErr; ?></span>
                        </div>
                        <button type="submit" class="form-control btn btn-primary" name="con_pass">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>  
</body>
</html>




<?php
    
}



?>