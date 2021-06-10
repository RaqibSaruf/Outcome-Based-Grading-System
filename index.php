<?php


$usernameErr = $passwordErr = "";



if($_SERVER["REQUEST_METHOD"]=="POST"){


    $username = $password = "";
    $user_id=$name=$user=$pass=$email=$mobile="";

    if(isset($_POST['login'])){

        $username = $_POST["username"];
        $password = md5($_POST["password"]);

        if(empty($username)){
            $usernameErr = "Username is required";
        }

        if(empty($password)){
            $passwordErr = "Password is required";
        }


        if(empty($usernameErr) && empty($passwordErr)){


            $conn = mysqli_connect('localhost', 'root', '','obgs_cse480');

                if(!$conn){
                    die("connection failed");
                }else{
                    $sql = "SELECT * FROM instructor WHERE username='$username'";
                    $result = mysqli_query($conn, $sql);

                    if(mysqli_num_rows($result) == 1){
                        while($row = mysqli_fetch_assoc($result)){
                            $user_id=$row['id'];
                            $name=$row["fullname"];
                            $user = $row["username"];
                            $pass = $row["pass"];
                            $email = $row["email"];
                            $mobile = $row["mobile"];
                        }
                    }else{
                        echo "User not found";
                    }
                }

                mysqli_close($conn);


                if($username != $user){
                    $usernameErr =  "Username invalid";
                }else{
                    if($password == $pass){
                        session_start();
                        $_SESSION["login"] = true;
                        $_SESSION["user_id"] = $user_id;
                        $_SESSION["name"] = $name;
                        $_SESSION["email"] = $email;
                        $_SESSION["mobile"] = $mobile;
                        $_SESSION["username"] = $user;
                        header("Location: home.php");
                    }else{
                        $passwordErr =  "Password invalid";             
                    }
                }

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
            <div class="card mt-5">
                <div class="card-header text-center"><h4 class="text-success">Sign In</h4></div>
                <div class="card-body">
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                        <div class="form-group">
                            <label for="username">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username">
                            <span class="text-danger"><?php echo $usernameErr; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="password">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password">
                            <span class="text-danger"> <?php echo $passwordErr; ?></span>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" onclick="show_passord()" class="form-check-input" id="showpassword">
                            <label class="form-check-label" for="showpassword">Show Password</label>
                        </div>
                        <button type="submit" class="form-control btn btn-primary" name="login">Submit</button>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <a class="card-link text-danger" href="">Forgot Password??</a>
                    <br>
                    <a class="card-link text-info" href="register.php">Register Now??</a>
                </div>
            </div>
        </div>
    </div>


<script type="text/javascript">
    
    function show_passord() {
      var x = document.getElementById("password");
      if (x.type === "password") {
        x.type = "text";
        } else {
        x.type = "password";
        }
    }

</script>
    
</body>
</html>