<?php
/**
 * Notes Listing Page
 * Online Notes Sharing System
 */

require_once 'config/database.php';
require_once 'includes/auth.php';

// Require login
requireLogin();

$user_id = getCurrentUserId();
$user_name = getCurrentUserName();
$user_email = getCurrentUserEmail();

$message = '';
$messageType = '';

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $note_id = intval($_GET['delete']);
    
    $conn = getDBConnection();
    
    // Verify note belongs to user
    $stmt = $conn->prepare("SELECT id FROM notes WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $note_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Get file paths before deleting
        $stmt2 = $conn->prepare("SELECT file_path FROM note_files WHERE note_id = ?");
        $stmt2->bind_param("i", $note_id);
        $stmt2->execute();
        $filesResult = $stmt2->get_result();
        
        // Delete physical files
        while ($file = $filesResult->fetch_assoc()) {
            $filePath = $file['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        $stmt2->close();
        
        // Delete note (cascade will delete files from DB)
        $stmt3 = $conn->prepare("DELETE FROM notes WHERE id = ? AND user_id = ?");
        $stmt3->bind_param("ii", $note_id, $user_id);
        
        if ($stmt3->execute()) {
            $message = 'Note deleted successfully!';
            $messageType = 'success';
        } else {
            $message = 'Error deleting note!';
            $messageType = 'danger';
        }
        $stmt3->close();
    } else {
        $message = 'Note not found or access denied!';
        $messageType = 'danger';
    }
    
    $stmt->close();
    closeDBConnection($conn);
}

// Get all notes for user
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT n.*, 
                        (SELECT COUNT(*) FROM note_files WHERE note_id = n.id) as file_count
                        FROM notes n 
                        WHERE n.user_id = ? 
                        ORDER BY n.created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$notes = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
closeDBConnection($conn);

$pageTitle = 'My Notes';
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <aside class="col-md-3 col-lg-2 bg-light sidebar p-0">
            <div class="sidebar-sticky">
                <div class="p-3 bg-primary text-white">
                    <div class="text-center mb-3">
                        <i class="fas fa-user-circle fa-3x"></i>
                    </div>
                    <h6 class="mb-1"><?php echo htmlspecialchars($user_name); ?></h6>
                    <small><?php echo htmlspecialchars($user_email); ?></small>
                </div>
                
                <nav class="nav flex-column p-3">
                    <a class="nav-link" href="dashboard.php">
                        <i class="fas fa-th-large"></i> Dashboard
                    </a>
                    <a class="nav-link active" href="notes.php">
                        <i class="fas fa-sticky-note"></i> Notes
                    </a>
                    <a class="nav-link" href="profile.php">
                        <i class="fas fa-user"></i> Profile
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">My Notes</h2>
                <a href="add_notes.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Notes
                </a>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <i class="fas fa-<?php echo $messageType == 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i> 
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (empty($notes)): ?>
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-sticky-note fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">No notes found</h5>
                        <p class="text-muted">Start by adding your first note!</p>
                        <a href="add_notes.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Your First Note
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($notes as $note): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge bg-primary"><?php echo htmlspecialchars($note['subject']); ?></span>
                                        <span class="text-muted small">
                                            <i class="fas fa-file"></i> <?php echo $note['file_count']; ?> files
                                        </span>
                                    </div>
                                    <h5 class="card-title"><?php echo htmlspecialchars($note['title']); ?></h5>
                                    <p class="card-text text-muted small">
                                        <?php echo htmlspecialchars(substr($note['description'], 0, 100)); ?>
                                        <?php echo strlen($note['description']) > 100 ? '...' : ''; ?>
                                    </p>
                                    <div class="text-muted small mb-3">
                                        <i class="fas fa-calendar"></i> 
                                        <?php echo date('M d, Y', strtotime($note['created_at'])); ?>
                                    </div>
                                    <div class="btn-group w-100" role="group">
                                        <a href="edit_notes.php?id=<?php echo $note['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="notes.php?delete=<?php echo $note['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Are you sure you want to delete this note?');">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

