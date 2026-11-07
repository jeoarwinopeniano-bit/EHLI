<?php
include "../database.php";
if (!isset($_SESSION["user_id"])) { header("Location: ../login.php"); exit; }
if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: tasks.php"); exit; }
$user_id=(int)$_SESSION["user_id"]; $title=trim($_POST["title"]??""); $description=trim($_POST["description"]??""); $deadline=trim($_POST["deadline"]??""); $date=DateTime::createFromFormat("Y-m-d",$deadline);
if ($title===""||$description===""||!$date||$date->format("Y-m-d")!==$deadline) { header("Location: tasks.php?status=error"); exit; }
$stmt=$conn->prepare("INSERT INTO tasks (user_id,title,description,deadline) VALUES (?,?,?,?)"); $stmt->bind_param("isss",$user_id,$title,$description,$deadline); $ok=$stmt->execute(); $stmt->close();
header("Location: tasks.php?status=".($ok?"saved":"error")); exit;
?>