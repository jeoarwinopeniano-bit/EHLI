<?php
include "../database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

$user_id = (int) $_SESSION["user_id"];

$stmt = $conn->prepare("SELECT * FROM notes WHERE user_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$notes = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyTrack: Student Task and Study Planner | Notes</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="app-layout">
<aside class="sidebar">
    <div class="logo">
        <div class="logo-icon">S</div>
        <div><h2>StudyTrack</h2><p>Student Planner</p></div>
    </div>

    <nav class="navigation">
        <a href="../index.php" class="nav-link"><span class="nav-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/></svg></span>Dashboard</a>
        <a href="../task/tasks.php" class="nav-link"><span class="nav-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/></svg></span>Tasks</a>
        <a href="../notes/notes.php" class="nav-link active"><span class="nav-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h9l4 4v14H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2zM14 3v5h5M8 12h8M8 16h8"/></svg></span>Notes</a>
    </nav>

    <div class="sidebar-bottom">
        <p>Signed in as</p>
        <strong><?php echo htmlspecialchars($_SESSION["full_name"]); ?></strong>
        <a href="../logout.php" class="logout-link" data-logout-confirm>Logout</a>
    </div>
</aside>

<main class="main-content">
    <div class="top-section">
        <div>
            <p class="small-title">STUDY NOTES</p>
            <h1>My Notes</h1>
            <p class="subtitle">Keep important study information in one place.</p>
        </div>
    </div>

    <?php
$statusMessages = ["saved" => "Study note saved successfully!", "deleted" => "Study note deleted successfully!", "error" => "We could not complete that note action. Please try again."];
$status = $_GET["status"] ?? "";
if (isset($statusMessages[$status])): ?>
    <div class="<?php echo $status === "error" ? "error-message" : "success-message"; ?> transient-message" role="<?php echo $status === "error" ? "alert" : "status"; ?>" aria-live="<?php echo $status === "error" ? "assertive" : "polite"; ?>"><?php echo htmlspecialchars($statusMessages[$status]); ?></div>
<?php endif; ?>
<section class="form-card">
        <div class="section-heading">
            <div><p class="card-label">NEW NOTE</p><h2>Create a Study Note</h2></div>
        </div>

        <form action="add_note.php" method="POST" data-loading="Saving note…">
            <div class="form-group">
                <label for="subject">Subject</label>
                <input type="text" name="subject" placeholder="Example: Web Development" required>
            </div>

            <div class="form-group">
                <label for="content">Note</label>
                <textarea name="content" placeholder="Write your study note..." required></textarea>
            </div>

            <button type="submit" class="primary-button">Save Note</button>
        </form>
    </section>

    <section class="content-section">
        <div class="section-heading">
            <div><p class="card-label">SAVED NOTES</p><h2>My Study Notes</h2></div>
        </div>

        <div class="notes-grid">
            <?php if ($notes->num_rows > 0): ?>
                <?php while ($note = $notes->fetch_assoc()): ?>
                    <div class="note-card">
                        <div class="note-header">
                            <h3><?php echo htmlspecialchars($note["subject"]); ?></h3>

                            <form action="delete_note.php" method="POST" data-confirm="Are you sure you want to delete this note?" data-loading="Deleting…">
                                <input type="hidden" name="id" value="<?php echo $note["id"]; ?>">
                                <button type="submit" class="delete-button">Delete</button>
                            </form>
                        </div>

                        <p><?php echo nl2br(htmlspecialchars($note["content"])); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <h3>No notes yet</h3>
                    <p>No notes yet. Create one to get started!</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <footer>We confirm this is our own work and we are present today.</footer>
</main>
<script src="../script.js"></script>
</body>
</html>