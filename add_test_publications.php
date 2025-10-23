<?php
// Include database connection
require_once __DIR__ . '/includes/db_connect.php';

// Sample publication data
$publications = [
    [
        'title' => 'Advancements in Agricultural Technology',
        'author' => 'Dr. John Mwambene',
        'publication_date' => '2023-10-15',
        'description' => 'A comprehensive study on the latest innovations in agricultural technology and their impact on crop yields in East Africa.',
        'image_url' => 'assets/img/publications/agri-tech.jpg',
        'document_url' => 'assets/documents/advancements-agri-tech.pdf',
        'is_featured' => 1
    ],
    [
        'title' => 'Sustainable Water Management in Arid Regions',
        'author' => 'Prof. Sarah Johnson',
        'publication_date' => '2023-09-22',
        'description' => 'Research on innovative water conservation techniques suitable for arid and semi-arid regions of Tanzania.',
        'image_url' => 'assets/img/publications/water-mgmt.jpg',
        'document_url' => 'assets/documents/water-management-research.pdf',
        'is_featured' => 1
    ],
    [
        'title' => 'Renewable Energy Solutions for Rural Communities',
        'author' => 'Dr. Michael Petro',
        'publication_date' => '2023-08-05',
        'description' => 'Analysis of cost-effective renewable energy solutions to power rural communities in developing nations.',
        'image_url' => 'assets/img/publications/renewable-energy.jpg',
        'document_url' => 'assets/documents/renewable-energy-solutions.pdf',
        'is_featured' => 1
    ]
];

// Prepare SQL statement
$sql = "INSERT INTO publications (title, author, publication_date, description, image_url, document_url, is_featured, created_at, updated_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

$stmt = $conn->prepare($sql);
$added = 0;

foreach ($publications as $pub) {
    $stmt->bind_param("ssssssi", 
        $pub['title'],
        $pub['author'],
        $pub['publication_date'],
        $pub['description'],
        $pub['image_url'],
        $pub['document_url'],
        $pub['is_featured']
    );
    
    if ($stmt->execute()) {
        $added++;
    } else {
        echo "Error adding publication: " . $stmt->error . "<br>";
    }
}

echo "Successfully added $added test publications to the database.<br>";
echo "<a href='/c/zanvarsity/html/index.php'>Go back to homepage</a> to see the changes.";

$stmt->close();
$conn->close();
?>
