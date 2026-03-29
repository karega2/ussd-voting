<?php include 'auth.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>School Voting System</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f6f9;
            text-align: center;
        }
        .container {
            margin-top: 100px;
        }
        h1 {
            color: #333;
        }
        a {
            display: inline-block;
            margin: 15px;
            padding: 15px 25px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }
        a:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>📊 School Voting System</h1>

    <a href="results.php">View Results</a>
    <a href="add_candidate.php">Add Candidate</a>
    <a href="logout.php">Logout</a>
</div>

</body>
</html>