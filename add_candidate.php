<?php include 'auth.php'; ?>
<?php
include 'db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name']);
    $position = $_POST['position'];

    // VALIDATION
    if (empty($name) || empty($position)) {
        $message = "<p style='color:red;'>All fields are required</p>";
    } else {

        // PREPARED STATEMENT (SECURE)
        $stmt = $conn->prepare("INSERT INTO candidates (name, position) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $position);

        if ($stmt->execute()) {
            $message = "<p style='color:green;'>Candidate added successfully!</p>";
        } else {
            $message = "<p style='color:red;'>Error adding candidate</p>";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Candidate</title>

<style>
body {
    font-family: Arial;
    background: #f4f6f9;
}

.container {
    width: 400px;
    margin: 80px auto;
    background: white;
    padding: 30px;
    border-radius: 10px;
}

input, select {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
}

button {
    width: 100%;
    padding: 12px;
    background: green;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

button:hover {
    background: darkgreen;
}

h2 {
    text-align: center;
}
</style>
</head>

<body>

<div class="container">

<h2>Add Candidate</h2>

<?php echo $message; ?>

<form method="POST">

    <input type="text" name="name" placeholder="Candidate Name" required>

    <select name="position" required>
        <option value="">-- Select Position --</option>
        <option value="president">President</option>
        <option value="secretary">Secretary</option>
    </select>

    <button type="submit">Add Candidate</button>

</form>

</div>

</body>
</html>