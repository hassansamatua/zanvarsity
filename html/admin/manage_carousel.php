<?php
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/includes/auth.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /c/zanvarsity/html/admin/login.php');
    exit();
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Get image path before deleting
    $stmt = $conn->prepare("SELECT image_path FROM carousel WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Delete from database
    $stmt = $conn->prepare("DELETE FROM carousel WHERE id = ?");
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        // Delete the image file
        if (!empty($row['image_path'])) {
            $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim($row['image_path'], '/');
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        $_SESSION['success'] = 'Carousel item deleted successfully';
    } else {
        $_SESSION['error'] = 'Error deleting carousel item';
    }

    header('Location: manage_carousel.php');
    exit();
}

// Get all carousel items
$carouselItems = [];
$result = $conn->query("SELECT * FROM carousel ORDER BY display_order ASC");
if ($result) {
    $carouselItems = $result->fetch_all(MYSQLI_ASSOC);
    
    // Add is_active for backward compatibility
    foreach ($carouselItems as &$item) {
        $item['is_active'] = ($item['status'] === 'active') ? 1 : 0;
    }
    unset($item); // Break the reference
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Carousel - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .carousel-img {
            max-width: 150px;
            max-height: 100px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Manage Carousel</h1>
                    <a href="edit_carousel.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Carousel Item
                    </a>
                </div>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Image</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Order</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($carouselItems)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center">No carousel items found. <a href="edit_carousel.php">Add a new one</a>.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($carouselItems as $index => $item): ?>
                                            <tr>
                                                <td><?= $index + 1; ?></td>
                                                <td>
                                                    <?php if (!empty($item['image_path'])): ?>
                                                        <img src="/<?= ltrim($item['image_path'], '/'); ?>" alt="<?= htmlspecialchars($item['title']); ?>" class="img-thumbnail carousel-img">
                                                    <?php else: ?>
                                                        <span class="text-muted">No image</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars($item['title']); ?></td>
                                                <td><?= !empty($item['description']) ? substr(htmlspecialchars($item['description']), 0, 50) . (strlen($item['description']) > 50 ? '...' : '') : 'N/A'; ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $item['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                                        <?= ucfirst($item['status']); ?>
                                                    </span>
                                                </td>
                                                <td><?= (int)$item['display_order']; ?></td>
                                                <td>
                                                    <a href="edit_carousel.php?id=<?= $item['id']; ?>" 
                                                       class="btn btn-sm btn-primary" 
                                                       title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" 
                                                            onclick="confirmDelete(<?= $item['id']; ?>)" 
                                                            class="btn btn-sm btn-danger" 
                                                            title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this carousel item!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'manage_carousel.php?action=delete&id=' + id;
                }
            });
            return false;
        }
    </script>
</body>
</html>
