<?php
session_start();
include 'db.php';

$message = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM admins WHERE username='$username'");

    if($result->num_rows > 0){

        $admin = $result->fetch_assoc();

        if(password_verify($password, $admin['password'])){
            $_SESSION['admin'] = $username;
            header("Location: index.php");
            exit();
        } else {
            $message = "Wrong password";
        }

    } else {
        $message = "User not found";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Login</title>
<style>
body { font-family: Arial; background:#f4f6f9; }
.container {
    width: 350px;
    margin: 100px auto;
    background:white;
    padding:30px;
    border-radius:10px;
}
input {
    width:100%;
    padding:10px;
    margin:10px 0;
}
button {
    width:100%;
    padding:10px;
    background:#007bff;
    color:white;
    border:none;
}
</style>
</head>
<body>

<div class="container">
<h2>Admin Login</h2>

<p style="color:red;"><?php echo $message; ?></p>

<form method="POST">
<input type="text" name="username" placeholder="Username" required>
<input type="password" name="password" placeholder="Password" required>
<button type="submit">Login</button>
</form>

</div>

</body>
</html>