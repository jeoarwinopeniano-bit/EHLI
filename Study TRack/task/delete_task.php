<?php
include "../database.php";
if (!isset($_SESSION["user_id"])) { header("Location: ../login.php"); exit; }
if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: tasks.php"); exit; }
$id=filter_input(INPUT_POST,"id",FILTER_VALIDATE_INT); $user_id=(int)$_SESSION["user_id"];
if (!$id) { header("Location: tasks.php?status=error"); exit; }
$stmt=$conn->prepare("DELETE FROM tasks WHERE id=? AND user_id=?"); $stmt->bind_param("ii",$id,$user_id); $stmt->execute(); $ok=$stmt->affected_rows===1; $stmt->close();
header("Location: tasks.php?status=".($ok?"deleted":"error")); exit;
?>