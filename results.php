<?php 
include 'db.php'; 
?>

<!DOCTYPE html>
<html>
<head>
<title>Results Dashboard</title>

<style>
body {
    font-family: Arial;
    background: #f4f6f9;
}

.container {
    width: 80%;
    margin: auto;
}

.card {
    background: white;
    padding: 20px;
    margin: 20px 0;
    border-radius: 10px;
}

.bar {
    height: 20px;
    background: blue;
    margin: 5px 0;
}

h2 {
    text-align: center;
}
</style>
</head>

<body>

<div class="container">

<h2>📊 Live Voting Results</h2>

<!-- PRESIDENT -->
<div class="card">
<h3>President</h3>

<?php
$query = "
SELECT candidates.id, candidates.name, COUNT(votes.id) as total
FROM candidates
LEFT JOIN votes ON votes.candidate_id = candidates.id
WHERE candidates.position='president'
GROUP BY candidates.id
";

$result = $conn->query($query);

if(!$result){
    die("Query Error: " . $conn->error);
}

while($row = $result->fetch_assoc()){
    $votes = $row['total'];
    echo "<p>".$row['name']." - ".$votes." votes</p>";
    echo "<div class='bar' style='width:".($votes*20)."px'></div>";
}
?>

</div>

<!-- SECRETARY -->
<div class="card">
<h3>Secretary</h3>

<?php
$query = "
SELECT candidates.id, candidates.name, COUNT(votes.id) as total
FROM candidates
LEFT JOIN votes ON votes.candidate_id = candidates.id
WHERE candidates.position='secretary'
GROUP BY candidates.id
";

$result = $conn->query($query);

if(!$result){
    die("Query Error: " . $conn->error);
}

while($row = $result->fetch_assoc()){
    $votes = $row['total'];
    echo "<p>".$row['name']." - ".$votes." votes</p>";
    echo "<div class='bar' style='width:".($votes*20)."px'></div>";
}
?>

</div>

</div>

</body>
</html>