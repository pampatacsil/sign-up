<?php require 'database/db-admin-signup.php' ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="Admin Sign Up" content="Mang Macs, User Registration">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="icon" type="image/jpeg" href="logo/mang-macs-logo.jpg" sizes="70x70">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="Design/admin-user-regx.css?<?php echo time(); ?>">
    <title>Sign Up</title>
</head>

<body>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
        <div class="container">
            <div class="content">
                <h1>Mang Macs Marinero Pizza House</h1>
                <p>Create Account</p>
                <strong>
                    <?php
                    if (isset($_GET['msg'])) {
                        $msg = "User Registration Successfully.";
                        echo "<strong class='msg-Success fa fa-check'>$msg</strong>";
                    }
                    ?>
                </strong>
                <input type="hidden" name="id">
                <input type="text" class="form-control form" name="fname" placeholder="First Name" required>
                <!--First Name!-->
                <strong><?php if ($fnameError) echo "<strong class='msg-Error text-danger fa fa-times'>$fnameError</strong>" ?></strong>
                <input type="text" class="form-control form" name="lname" placeholder="Last Name" required>
                <!--Last Name!-->
                <strong><?php if ($lnameError) echo "<strong class='msg-Error text-danger fa fa-times'>$lnameError</strong>" ?></strong>
                <input type="text" class="form-control form" name="uname" placeholder="Username">
                <!--Username!-->
                <strong><?php if ($unameError) echo "<strong class='msg-Error text-danger fa fa-times'>$unameError</strong>" ?></strong>
                <input type="email" class="form-control form" name="email" placeholder="Email Address" required>
                <!--Email!-->
                <strong><?php if ($emailError) echo "<strong class='msg-Error text-danger fa fa-times'>$emailError</strong>" ?></strong>
                <input type="password" class="form-control form togglePassword" name="password" placeholder="Password" required>
                <!--Password!-->
                <strong><?php if ($passwordError) echo "<strong class='msg-Error text-danger fa fa-times'>$passwordError</strong>" ?></strong>
                <input type="password" class="form-control form togglePassword" name="confirmPassword" placeholder="Confirm Password" required>
                <!--Confirm Password!-->
                <strong><?php if ($confirmPwordError) echo "<strong class='msg-Error text-danger fa fa-times'>$confirmPwordError</strong>" ?></strong>
                <strong><?php if ($pwordError) echo "<strong class='msg-Error text-danger fa fa-times'>$pwordError</strong>" ?></strong>
                <!--Checkbox!-->
                <div class="inline-checkbox"><input type="checkbox" class="checkbox" onclick="toggle(this)">Show Password</div>
                <button type="submit" name="btnSignup">Sign Up</button>
                <p class="text-account">Already have an Account? <a href="admin-login.php">Login</a> </p>
            </div>
        </div>
    </form>
    <script src="assets/multi-password-visibility.js"></script>
</body>

</html>