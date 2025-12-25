<?php
/**
 * Edit Notes Page
 * Online Notes Sharing System
 */

require_once 'config/database.php';
require_once 'includes/auth.php';

// Require login
requireLogin();

$user_id = getCurrentUserId();
$user_name = getCurrentUserName();
$user_email = getCurrentUserEmail();

$error = '';
$success = '';
$note = null;

// Get note ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: notes.php');
    exit();
}

$note_id = intval($_GET['id']);

// Get note data
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT * FROM notes WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $note_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $stmt->close();
    closeDBConnection($conn);
    header('Location: notes.php');
    exit();
}

$note = $result->fetch_assoc();
$stmt->close();

// Get existing files
$stmt = $conn->prepare("SELECT * FROM note_files WHERE note_id = ?");
$stmt->bind_param("i", $note_id);
$stmt->execute();
$filesResult = $stmt->get_result();
$existingFiles = $filesResult->fetch_all(MYSQLI_ASSOC);
$stmt->close();
closeDBConnection($conn);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitizeInput($_POST['title'] ?? '');
    $subject = sanitizeInput($_POST['subject'] ?? '');
    $description = sanitizeInput($_POST['description'] ?? '');
    
    // Handle file deletion
    if (isset($_POST['delete_files'])) {
        $filesToDelete = $_POST['delete_files'];
        $conn = getDBConnection();
        
        foreach ($filesToDelete as $fileId) {
            $fileId = intval($fileId);
            $stmt = $conn->prepare("SELECT file_path FROM note_files WHERE id = ? AND note_id = ?");
            $stmt->bind_param("ii", $fileId, $note_id);
            $stmt->execute();
            $fileResult = $stmt->get_result();
            
            if ($fileResult->num_rows > 0) {
                $fileData = $fileResult->fetch_assoc();
                $filePath = $fileData['file_path'];
                
                // Delete physical file
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                
                // Delete from database
                $stmt2 = $conn->prepare("DELETE FROM note_files WHERE id = ? AND note_id = ?");
                $stmt2->bind_param("ii", $fileId, $note_id);
                $stmt2->execute();
                $stmt2->close();
            }
            $stmt->close();
        }
        closeDBConnection($conn);
        
        // Refresh files list
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT * FROM note_files WHERE note_id = ?");
        $stmt->bind_param("i", $note_id);
        $stmt->execute();
        $filesResult = $stmt->get_result();
        $existingFiles = $filesResult->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        closeDBConnection($conn);
    }
    
    // Validation
    if (empty($title) || empty($subject)) {
        $error = 'Title and Subject are required!';
    } else {
        $conn = getDBConnection();
        
        // Update note
        $stmt = $conn->prepare("UPDATE notes SET title = ?, subject = ?, description = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sssii", $title, $subject, $description, $note_id, $user_id);
        
        if ($stmt->execute()) {
            $stmt->close();
            
            // Handle new file uploads
            $uploadDir = 'assets/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $uploadedFiles = [];
            $allowedTypes = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'gif'];
            $maxFileSize = 5 * 1024 * 1024; // 5MB
            
            // Process file uploads (up to 4 files)
            for ($i = 1; $i <= 4; $i++) {
                $fileKey = 'file' . $i;
                
                if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] == UPLOAD_ERR_OK) {
                    $file = $_FILES[$fileKey];
                    $fileName = $file['name'];
                    $fileTmp = $file['tmp_name'];
                    $fileSize = $file['size'];
                    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    
                    // Validate file
                    if (!in_array($fileExt, $allowedTypes)) {
                        $error = "File type not allowed: $fileName. Allowed types: " . implode(', ', $allowedTypes);
                        break;
                    }
                    
                    if ($fileSize > $maxFileSize) {
                        $error = "File too large: $fileName. Maximum size: 5MB";
                        break;
                    }
                    
                    // Generate unique filename
                    $newFileName = uniqid() . '_' . time() . '.' . $fileExt;
                    $filePath = $uploadDir . $newFileName;
                    
                    if (move_uploaded_file($fileTmp, $filePath)) {
                        // Insert file record
                        $stmt = $conn->prepare("INSERT INTO note_files (note_id, file_name, file_path) VALUES (?, ?, ?)");
                        $stmt->bind_param("iss", $note_id, $fileName, $filePath);
                        $stmt->execute();
                        $stmt->close();
                        
                        $uploadedFiles[] = $fileName;
                    } else {
                        $error = "Error uploading file: $fileName";
                        break;
                    }
                }
            }
            
            if (empty($error)) {
                $success = 'Notes updated successfully!';
                if (!empty($uploadedFiles)) {
                    $success .= ' ' . count($uploadedFiles) . ' new file(s) uploaded.';
                }
                
                // Refresh note data
                $stmt = $conn->prepare("SELECT * FROM notes WHERE id = ? AND user_id = ?");
                $stmt->bind_param("ii", $note_id, $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $note = $result->fetch_assoc();
                $stmt->close();
                
                // Refresh files list
                $stmt = $conn->prepare("SELECT * FROM note_files WHERE note_id = ?");
                $stmt->bind_param("i", $note_id);
                $stmt->execute();
                $filesResult = $stmt->get_result();
                $existingFiles = $filesResult->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
            }
        } else {
            $error = 'Error updating notes! Please try again.';
        }
        
        closeDBConnection($conn);
    }
}

$pageTitle = 'Edit Notes';
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
                <h2 class="fw-bold">Edit Notes</h2>
                <a href="notes.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Notes
                </a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" enctype="multipart/form-data" id="editNotesForm">
                        <div class="mb-3">
                            <label for="title" class="form-label">Notes Title</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?php echo htmlspecialchars($note['title']); ?>" 
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" 
                                   value="<?php echo htmlspecialchars($note['subject']); ?>" 
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Notes Description</label>
                            <textarea class="form-control" id="description" name="description" rows="5" 
                                      required><?php echo htmlspecialchars($note['description']); ?></textarea>
                        </div>

                        <?php if (!empty($existingFiles)): ?>
                            <div class="mb-3">
                                <label class="form-label">Existing Files</label>
                                <div class="list-group">
                                    <?php foreach ($existingFiles as $file): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fas fa-file"></i> 
                                                <?php echo htmlspecialchars($file['file_name']); ?>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="delete_files[]" value="<?php echo $file['id']; ?>" 
                                                       id="delete_<?php echo $file['id']; ?>">
                                                <label class="form-check-label text-danger" for="delete_<?php echo $file['id']; ?>">
                                                    Delete
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="file1" class="form-label">Upload New File</label>
                            <input type="file" class="form-control" id="file1" name="file1" 
                                   accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif">
                            <small class="text-muted">Allowed: PDF, DOC, DOCX, TXT, JPG, PNG, GIF (Max: 5MB)</small>
                        </div>

                        <div class="mb-3">
                            <label for="file2" class="form-label">More File</label>
                            <input type="file" class="form-control" id="file2" name="file2" 
                                   accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif">
                        </div>

                        <div class="mb-3">
                            <label for="file3" class="form-label">More File</label>
                            <input type="file" class="form-control" id="file3" name="file3" 
                                   accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif">
                        </div>

                        <div class="mb-3">
                            <label for="file4" class="form-label">More File</label>
                            <input type="file" class="form-control" id="file4" name="file4" 
                                   accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif">
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="notes.php" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

