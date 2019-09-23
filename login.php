<?php

require_once 'util.php';
session_start();

// Check to see if we have some POST data, if we do process it
if ( isset($_POST['username']) && isset($_POST['pass']) ) {
        $salt = 'XyZzy12*_';
        $myUsername = 'okasha';
        $myPass = 'aaaass';
        $check = hash('md5', $salt.$_POST['pass']);
        $has_pass = hash('md5', $salt.$myPass);        
        if ($check == $has_pass && $_POST['username'] == $myUsername) {
            $_SESSION['username'] = $_POST['username'];
            header("Location: index.php");
            return;
        }
        else {
            $_SESSION['error'] = "Incorrect username or password";
            header("Location: login.php");
            return;        
        }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login</title>
<?php require_once 'head.php'; ?>
</head>
<body>
<div class="container" style="margin-top: 1em;">
<form method="POST" action="login.php">
<div class="form-group">
    <label for="username">Username</label>
    <input type="text" class="form-control" id="username" name="username" aria-describedby="usernameHelp" placeholder="Enter Username">
  <div class="form-group" style="margin-top: 1em;">
    <label for="password">Password</label>
    <input type="password" class="form-control" id="pass" placeholder="Password" name="pass">
  </div>
<?php
    flashMessage();
?>
</div>
  <button type="submit" class="btn btn-primary" onclick="return doValidate();">Submit</button>
</form>
</div>
<script type="text/javascript">
    function doValidate(){
        console.log('Validating...');
        try {
            addr = document.getElementById('username').value;
            pw = document.getElementById('pass').value;
            console.log("Validating addr="+addr+" pw="+pw);
            if (addr == null || addr == "" || pw == null || pw == "") {
                alert("Both fields must be filled out");
                return false;
            }
            return true;
        } catch(e) {
            return false;
        }
        return false;
    }
</script>
</body>
