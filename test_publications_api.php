<?php
// Test API Connection for Publications
$apiUrl = "https://www.zumis.ac.tz/academic/data/api/getAllPublications";

echo "<h2>Testing API Connection</h2>";
echo "<p>API Endpoint: $apiUrl</p>";

// Initialize cURL
$ch = curl_init();

// Set cURL options
curl_setopt_array($ch, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false, // For testing only, use true in production with proper SSL certs
    CURLOPT_HTTPHEADER => ['Accept: application/json'],
    CURLOPT_TIMEOUT => 10,
    CURLOPT_FOLLOWLOCATION => true
]);

// Execute the request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

// Close cURL
curl_close($ch);

// Output results
echo "<h3>Response Status: $httpCode</h3>";

if ($error) {
    echo "<div style='color: red;'><strong>cURL Error:</strong> " . htmlspecialchars($error) . "</div>";
} else {
    // Try to decode JSON
    $data = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "<div style='color: red;'><strong>JSON Error:</strong> " . json_last_error_msg() . "</div>";
        echo "<h4>Raw Response:</h4>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    } else {
        echo "<div style='color: green;'><strong>Successfully received API response</strong></div>";
        
        // Display publication count
        $publicationCount = is_array($data) ? count($data) : 0;
        echo "<p>Number of publications received: " . $publicationCount . "</p>";
        
        // Display first few publications
        if ($publicationCount > 0) {
            echo "<h4>Sample Publications:</h4>";
            echo "<table border='1' cellpadding='8' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>Title</th><th>Author</th><th>Date</th><th>Has Image</th><th>Has Document</th></tr>";
            
            $sample = array_slice($data, 0, 5); // Show first 5 publications
            foreach ($sample as $pub) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($pub['title'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($pub['author'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($pub['publication_date'] ?? 'N/A') . "</td>";
                echo "<td>" . (!empty($pub['image_url']) ? '✅' : '❌') . "</td>";
                echo "<td>" . (!empty($pub['document_url']) ? '✅' : '❌') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
}
?>
