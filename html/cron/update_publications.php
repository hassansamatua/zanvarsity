<?php
/**
 * Background script to update publications from the API
 * This script is called asynchronously to avoid slowing down page loads
 */

// Set unlimited execution time and ignore user abort
set_time_limit(0);
ignore_user_abort(true);

// Include required files
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../includes/publication_manager.php';

// Create cache directory if it doesn't exist
$cacheDir = __DIR__ . '/../cache';
if (!file_exists($cacheDir)) {
    mkdir($cacheDir, 0755, true);
}

// Create a lock file to prevent multiple instances
$lockFile = $cacheDir . '/update_publications.lock';
$lockHandle = @fopen($lockFile, 'w');

// If we can't get a lock, another instance is probably running
if (!flock($lockHandle, LOCK_EX | LOCK_NB)) {
    error_log('Publication update already in progress');
    exit(0);
}

try {
    // Update publications
    $publicationManager = new PublicationManager();
    $result = $publicationManager->updatePublicationsFromApi();
    
    if ($result === false) {
        error_log('Failed to update publications from API');
    } else {
        error_log("Successfully updated $result publications from API");
    }
} catch (Exception $e) {
    error_log('Error in update_publications.php: ' . $e->getMessage());
}

// Release the lock
flock($lockHandle, LOCK_UN);
fclose($lockHandle);
@unlink($lockFile);

// Exit with success status
exit(0);
