<?php
// Debug API Response
$apiUrl = "https://www.zumis.ac.tz/academic/data/api/getAllPublications";

// Initialize cURL
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_HTTPHEADER => ['Accept: application/json'],
    CURLOPT_HEADER => true
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);
curl_close($ch);

// Output the response
echo "<h2>API Debug Information</h2>";
echo "<h3>HTTP Status: $httpCode</h3>";

echo "<h3>Response Headers:</h3>";
echo "<pre>" . htmlspecialchars($headers) . "</pre>";

echo "<h3>Response Body (first 1000 chars):</h3>";
$sample = substr($body, 0, 1000);
echo "<pre>" . htmlspecialchars($sample) . "</pre>";

echo "<h3>JSON Decode Test:</h3>";
$data = json_decode($body, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "<div style='color:red;'>JSON Error: " . json_last_error_msg() . "</div>";
} else {
    echo "<div style='color:green;'>JSON is valid!</div>";
    echo "<p>Number of items: " . (is_array($data) ? count($data) : 'N/A') . "</p>";
    
    if (is_array($data) && !empty($data)) {
        echo "<h4>First item structure:</h4>";
        echo "<pre>" . print_r(reset($data), true) . "</pre>";
    }
}
?>
