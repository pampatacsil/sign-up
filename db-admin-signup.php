<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
require 'database/dbconnect.php';
date_default_timezone_set('Asia/Manila');
$fnameError = $lnameError = $passwordError = $confirmPwordError = $pwordError = $unameError = $emailError = $otpError = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //user registration
    if (isset($_POST['btnSignup'])) {
        $id = mysqli_real_escape_string($connect, $_POST['id']);
        $fname = mysqli_real_escape_string($connect, $_POST['fname']);
        $lname = mysqli_real_escape_string($connect, $_POST['lname']);
        $uname = mysqli_real_escape_string($connect, $_POST['uname']);
        $email = mysqli_real_escape_string($connect, $_POST['email']);
        $password = mysqli_real_escape_string($connect, $_POST['password']);
        $confirmPassword = mysqli_real_escape_string($connect, $_POST['confirmPassword']);
        $passwordHash = password_hash($confirmPassword, PASSWORD_DEFAULT);
        $user_image = "no image";
        $position = "no selected";
        $salary = 0;
        $created_at = date('y-m-d h:i:s');
        $email_uname_check = $connect->prepare("SELECT * FROM tblusers WHERE uname=? OR email=?");
        $email_uname_check->bind_param('ss', $uname, $email);
        $email_uname_check->execute();
        $fetch_email_uname = $email_uname_check->get_result(); //for num rows
        $row = $fetch_email_uname->fetch_assoc(); //for fetching data in database
        //check if uname already exist
        if ($fetch_email_uname->num_rows == 1) {
            if ($uname == $row['uname']) {
                $unameError = "Username already exist.";
            }
            if ($email == $row['email']) {
                $emailError = "Email already exist.";
            }
        }
        //check if field is empty
        if (empty($fname) || empty($lname) || empty($uname) || empty($email) || empty($password)) {
            $fnameError = "Missing.";
        }
        //check password length
        if (strlen($password) <= 8) {
            $passwordError = "Password must contain at least 8 characters.";
        }
        if (strlen($confirmPassword) <= 8) {
            $confirmPwordError = "Password must contain at least 8 characters";
        }
        if ($password != $confirmPassword) {
            $pwordError = "Password do not match";
        }
        //check if true
        else {
            if ($email_uname_check) {
                $ver_code = rand(999999, 111111);
                $status = "not verified";
                $insertUser = $connect->prepare("INSERT INTO tblusers (id,fname,lname,uname,email,user_password,profile,position,salary,created_at,verification_code,status)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
                $insertUser->bind_param('isssssssssis', $id, $fname, $lname, $uname, $email, $passwordHash, $user_image, $position, $salary, $created_at, $ver_code, $status);
                $insertUser->execute();
                if ($insertUser) {
                    require 'php-mailer/vendor/autoload.php';
                    $mail = new PHPMailer();
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'mangmacsmarinerospizzahouse@gmail.com';
                    $mail->Password = 'mangmacsmarineros';
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;
                    $mail->setFrom('mangmacsmarinerospizzahouse@gmail.com', "Mang Mac's Marinero");
                    $mail->addAddress($email);
                    $mail->isHTML(true);
                    $mail->Subject = "Your Mang Mac's activation code";
                    $mail->Body = "<p>Hello $fname,</p><p>Your Mang Mac's Verification code is <b>$ver_code</b></p>";
                    $mail->AltBody = 'FROM: mangmacsmarinerospizzahouse@gmail.com';
                    if ($mail->send()) {
                        $info = "We've sent a verification code to your email - $email to verify your account";
                        $_SESSION['info'] = $info;
                        $_SESSION['email'] = $email;
                        $_SESSION['password'] = $password;
                        header('location: admin-otp.php?msg-success');
                        exit();
                    } else {
                        header('Location:admin-signup.php?Failed');
                    }
                }
            }
        }
    }
    //user verification code
    if (isset($_POST['btn-submit-otp'])) {
        $_SESSION['info'] = "";
        $sessionEmail = $_SESSION['email'];
        $otp = mysqli_real_escape_string($connect, $_POST['otp']);
        //check email otp verification
        $check_email_otp = $connect->prepare("SELECT * FROM tblusers WHERE verification_code=?");
        $check_email_otp->bind_param('s', $otp);
        $check_email_otp->execute();
        $fetch_email_otp = $check_email_otp->get_result();
        $row = $fetch_email_otp->fetch_assoc();
        if ($fetch_email_otp->num_rows == 1) {
            if ($otp == $row['verification_code']) {
                $fetchCode = $row['verification_code'];
                $fetchEmail = $row['email'];
                $code = 0;
                $status = "verified";
                //Update verification code and status
                $update_code_status = $connect->prepare("UPDATE tblusers SET verification_code=?, status =? WHERE verification_code=?");
                $update_code_status->bind_param('sss', $code, $status, $fetchCode);
                $update_code_status->execute();
                if ($update_code_status) {
                    header('Location:admin-login.php');
                }
            }
        } else {
            $otpError = "Incorrect Code";
        }
    }
    //check email to recover password
    if (isset($_POST['btn-continue'])) {
        $email = mysqli_real_escape_string($connect, $_POST['email']);
        if (!empty($email)) {
            $getUserRecord = $connect->prepare("SELECT * FROM tblusers WHERE email=?");
            $getUserRecord->bind_param('s', $email);
            $getUserRecord->execute();
            $row = $getUserRecord->get_result();
            $fetchUserRecord = $row->fetch_assoc();
            if ($row->num_rows == 1) {
                if ($email == $fetchUserRecord['email']) {
                    $code = rand(999999, 111111);
                    $fetchCode = $fetchUserRecord['verification_code'];
                    $updateOtp = $connect->prepare("UPDATE tblusers SET verification_code=? WHERE email=?");
                    $updateOtp->bind_param('ss', $code, $email);
                    $updateOtp->execute();
                    if ($updateOtp) {
                        require 'php-mailer/vendor/autoload.php';
                        $mail = new PHPMailer;
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'mangmacsmarinerospizzahouse@gmail.com';
                        $mail->Password = 'mangmacsmarineros';
                        $mail->SMTPSecure = 'tls';
                        $mail->Port = 587;
                        $mail->setFrom('mangmacsmarinerospizzahouse@gmail.com', "Mang Mac's Marinero");
                        $mail->addAddress($email);
                        $mail->isHTML(true);
                        $mail->Subject = "Your Mang Mac's reset password code";
                        $mail->Body = "<p>Hello,</p><p>To reset your password, enter this code<b> $code</b></p>";
                        $mail->AltBody = 'FROM: mangmacsmarinerospizzahouse@gmail.com';
                        if ($mail->send()) {
                            $info = "We've sent a verification code to your email - $email to reset your password.";
                            $_SESSION['info'] = $info;
                            $_SESSION['email'] = $email;
                            $_SESSION['password'] = $password;
                            header('location: admin-otp-reset.php?msg-success');
                            exit();
                        } else {
                            header('Location:admin-signup.php?Failed');
                        }
                    }
                }

            } else {
                $emailError = "Email not found";
            }
        }
    }
    //check verification code
    if (isset($_POST['btn-otp-reset'])) {
        $otpReset = mysqli_real_escape_string($connect, $_POST['otp-reset']);
        $getUserOtp = $connect->prepare("SELECT * FROM tblusers WHERE verification_code=?");
        $getUserOtp->bind_param('s', $otpReset);
        $getUserOtp->execute();
        $row = $getUserOtp->get_result();
        $fetchOtp = $row->fetch_assoc();
        $email = $fetchOtp['email'];
        $_SESSION['emailAddress'] = $email;
        if ($row->num_rows == 1) {
            if ($otpReset == $fetchOtp['verification_code']) {
                header('Location:admin-reset-password.php');
            }
        } else {
            $otpError = "Incorrect Code";
        }

    }
    //check user code to reset password
    if (isset($_POST['btn-reset'])) {
        $newPassword = mysqli_real_escape_string($connect, $_POST['newPassword']);
        $confirmPassword = mysqli_real_escape_string($connect, $_POST['confirmPassword']);
        $resetPasswordHash = password_hash($confirmPassword, PASSWORD_DEFAULT);
        $code = 0;
        $email = $_SESSION['emailAddress'];
        //verify code
        if (strlen($confirmPassword <= 8)) {
            $pwordError = "Your new password must contain at least 8 characters.";
        }
        if ($newPassword === $confirmPassword) {
            $updateUserPassword = $connect->prepare("UPDATE tblusers SET user_password=?,verification_code=? WHERE email=?");
            echo $connect->error;
            $updateUserPassword->bind_param('sss', $resetPasswordHash, $code, $email);
            $updateUserPassword->execute();
            if ($updateUserPassword) {
                header('Location:admin-reset-password.php?password-changed');
            }
        } else {
            $confirmPwordError = "Password do not match.";
        }
    }
}
