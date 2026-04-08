<?php
error_reporting(0);
ini_set('display_errors', 0);
header("Content-Type: text/plain");

include("db.php");

$phone = $_POST['phoneNumber'];
$text  = isset($_POST['text']) ? $_POST['text'] : "";

$input = explode("*", $text);
$level = count($input);

/* ================= MAIN MENU ================= */

if ($text == "") {
    echo "CON Welcome to School Voting\n";
    echo "1. Register\n";
    echo "2. Login\n";
    echo "3. Forgot PIN";
    exit();
}

/* ================= REGISTER ================= */

if ($input[0] == "1") {

    if ($level == 1) {
        echo "CON Enter Registration Number:";
        exit();
    }

    if ($level == 2) {
        echo "CON Create 4-digit PIN:";
        exit();
    }

    if ($level == 3) {

        $reg = trim($input[1]);
        $pin = password_hash($input[2], PASSWORD_DEFAULT);

        $check = $conn->prepare("SELECT id FROM students WHERE phone=?");
        $check->bind_param("s", $phone);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            echo "END This phone number is already registered.";
            exit();
        }

        $stmt = $conn->prepare("INSERT INTO students (reg_number, phone, pin) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $reg, $phone, $pin);
        $stmt->execute();

        echo "END Registration successful!";
        exit();
    }
}

/* ================= FORGOT PIN ================= */

if ($input[0] == "3") {

    if ($level == 1) {
        echo "CON Enter Registration Number:";
        exit();
    }

    if ($level == 2) {
        echo "CON Enter New 4-digit PIN:";
        exit();
    }

    if ($level == 3) {

        $reg = trim($input[1]);
        $newpin = password_hash($input[2], PASSWORD_DEFAULT);

        $stmt = $conn->prepare("SELECT id FROM students WHERE reg_number=? AND phone=?");
        $stmt->bind_param("ss", $reg, $phone);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 0) {
            echo "END Registration number not found for this phone.";
            exit();
        }

        $update = $conn->prepare("UPDATE students SET pin=? WHERE reg_number=? AND phone=?");
        $update->bind_param("sss", $newpin, $reg, $phone);
        $update->execute();

        echo "END PIN updated successfully!";
        exit();
    }
}

/* ================= LOGIN ================= */

if ($input[0] == "2") {

    if ($level == 1) {
        echo "CON Enter PIN:";
        exit();
    }

    if ($level == 2) {

        $entered_pin = $input[1];

        $stmt = $conn->prepare("SELECT id, pin FROM students WHERE phone=?");
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $student = $stmt->get_result()->fetch_assoc();

        if (!$student || !password_verify($entered_pin, $student['pin'])) {
            echo "END Invalid PIN";
            exit();
        }

        $student_id = $student['id'];

        // Check voting status
        $checkVote = $conn->prepare("
            SELECT 
                SUM(position='president') as pres_count,
                SUM(position='secretary') as sec_count
            FROM votes 
            WHERE student_id=?
        ");
        $checkVote->bind_param("i", $student_id);
        $checkVote->execute();
        $status = $checkVote->get_result()->fetch_assoc();

        // Already completed voting
        if ($status['pres_count'] > 0 && $status['sec_count'] > 0) {
            echo "END You have already completed voting.";
            exit();
        }

        // If voted president only → go to secretary
        if ($status['pres_count'] > 0 && $status['sec_count'] == 0) {

            $result = $conn->query("SELECT id, name FROM candidates WHERE position='secretary' ORDER BY id ASC");

            echo "CON Vote for Secretary:\n";
            $i = 1;
            while ($row = $result->fetch_assoc()) {
                echo $i.". ".$row['name']."\n";
                $i++;
            }
            exit();
        }

        // Otherwise start with president
        echo "CON Vote for:\n1. President";
        exit();
    }

    /* ===== PRESIDENT LIST ===== */

    if ($level == 3 && $input[2] == "1") {

        $result = $conn->query("SELECT id, name FROM candidates WHERE position='president' ORDER BY id ASC");

        echo "CON Choose President:\n";
        $i = 1;
        while ($row = $result->fetch_assoc()) {
            echo $i.". ".$row['name']."\n";
            $i++;
        }
        exit();
    }

    /* ===== SAVE PRESIDENT ===== */

    if ($level == 4) {

        $entered_pin = $input[1];
        $choice = intval($input[3]);

        $stmt = $conn->prepare("SELECT id, pin FROM students WHERE phone=?");
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $student = $stmt->get_result()->fetch_assoc();

        if (!$student || !password_verify($entered_pin, $student['pin'])) {
            echo "END Error occurred.";
            exit();
        }

        $student_id = $student['id'];

        $result = $conn->query("SELECT id FROM candidates WHERE position='president' ORDER BY id ASC");
        $candidates = [];
        while ($row = $result->fetch_assoc()) {
            $candidates[] = $row['id'];
        }

        if (!isset($candidates[$choice-1])) {
            echo "END Invalid selection.";
            exit();
        }

        $candidate_id = $candidates[$choice-1];

        $stmt2 = $conn->prepare("INSERT INTO votes (student_id, candidate_id, position) VALUES (?, ?, 'president')");
        $stmt2->bind_param("ii", $student_id, $candidate_id);

        if (!$stmt2->execute()) {
            echo "END You already voted for President.";
            exit();
        }

        // Move to Secretary
        $result = $conn->query("SELECT id, name FROM candidates WHERE position='secretary' ORDER BY id ASC");

        echo "CON President vote submitted!\nVote for Secretary:\n";
        $i = 1;
        while ($row = $result->fetch_assoc()) {
            echo $i.". ".$row['name']."\n";
            $i++;
        }
        exit();
    }

    /* ===== SAVE SECRETARY ===== */

    if ($level == 5) {

        $choice = intval($input[4]);

        $stmt = $conn->prepare("SELECT id FROM students WHERE phone=?");
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $student = $stmt->get_result()->fetch_assoc();

        $student_id = $student['id'];

        $result = $conn->query("SELECT id FROM candidates WHERE position='secretary' ORDER BY id ASC");
        $candidates = [];
        while ($row = $result->fetch_assoc()) {
            $candidates[] = $row['id'];
        }

        if (!isset($candidates[$choice-1])) {
            echo "END Invalid selection.";
            exit();
        }

        $candidate_id = $candidates[$choice-1];

        $stmt2 = $conn->prepare("INSERT INTO votes (student_id, candidate_id, position) VALUES (?, ?, 'secretary')");
        $stmt2->bind_param("ii", $student_id, $candidate_id);

        if (!$stmt2->execute()) {
            echo "END You already voted for Secretary.";
            exit();
        }

        echo "END Secretary vote submitted! Thank you.";
        exit();
    }
}

echo "END Invalid input.";
exit();
?>
