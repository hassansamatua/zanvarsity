<?php
// Script to help locate image files
$search_dirs = [
    'c:/xampp/htdocs/c/zanvarsity/html/uploads/events/',
    'c:/xampp/htdocs/c/zanvarsity/html/admin/uploads/events/',
    'c:/xampp/htdocs/c/zanvarsity/uploads/events/'
];

$found_images = [];

// Search for image files in each directory
foreach ($search_dirs as $dir) {
    if (is_dir($dir)) {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $found_images[] = [
                    'path' => $dir . $file,
                    'web_path' => str_replace('c:/xampp/htdocs', '', $dir . $file),
                    'exists' => file_exists($dir . $file)
                ];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Image Locator</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .exists { color: green; }
        .missing { color: red; }
    </style>
</head>
<body>
    <h1>Image Locator</h1>
    <h2>Search Directories:</h2>
    <ul>
        <?php foreach ($search_dirs as $dir): ?>
            <li><?php echo htmlspecialchars($dir); ?> - <?php echo is_dir($dir) ? '<span class="exists">Exists</span>' : '<span class="missing">Missing</span>'; ?></li>
        <?php endforeach; ?>
    </ul>

    <h2>Found Images (<?php echo count($found_images); ?>)</h2>
    <table>
        <tr>
            <th>Path</th>
            <th>Web Path</th>
            <th>Status</th>
            <th>Preview</th>
        </tr>
        <?php foreach ($found_images as $image): ?>
            <tr>
                <td><?php echo htmlspecialchars($image['path']); ?></td>
                <td><?php echo htmlspecialchars($image['web_path']); ?></td>
                <td class="<?php echo $image['exists'] ? 'exists' : 'missing'; ?>">
                    <?php echo $image['exists'] ? 'Found' : 'Missing'; ?>
                </td>
                <td>
                    <?php if ($image['exists'] && getimagesize($image['path'])): ?>
                        <img src="<?php echo htmlspecialchars($image['web_path']); ?>" style="max-width: 100px; max-height: 100px;">
                    <?php else: ?>
                        No preview available
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
