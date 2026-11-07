<?php
include "../database.php";
if (!isset($_SESSION["user_id"])) { header("Location: ../login.php"); exit; }
if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: notes.php"); exit; }
$user_id=(int)$_SESSION["user_id"]; $subject=trim($_POST["subject"]??""); $content=trim($_POST["content"]??"");
if ($subject===""||$content==="") { header("Location: notes.php?status=error"); exit; }
$stmt=$conn->prepare("INSERT INTO notes (user_id,subject,content) VALUES (?,?,?)"); $stmt->bind_param("iss",$user_id,$subject,$content); $ok=$stmt->execute(); $stmt->close();
header("Location: notes.php?status=".($ok?"saved":"error")); exit;
?>