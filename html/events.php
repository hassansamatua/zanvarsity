<?php

$year = date('Y');
$month = date('m');

echo json_encode(array(

    array(
        'id' => 1,
        'title' => "Conservatory Exhibit: The garden of india a country and culture revealed", // Event Title
        'start' => "2014-04-04", // Event Start Date
        'url' => "event-detail.html" // Event URL
    ),

    array(
        'id' => 2,
        'title' => "February Half-Term Activities: Big Stars and Little Secrets",
        'start' => "$year-$month-10",
        'url' => "event-detail.html"
    ),

    array(
        'id' => 3,
        'title' => "The Orchestra of the Age of Enlightenment perform with Music",
        'start' => "$year-$month-11",
        'url' => "event-detail.html"
    ),

    array(
        'id' => 4,
        'title' => "Museums and the Construction of Identities",
        'start' => "$year-$month-14",
        'url' => "event-detail.html"
    ),

    array(
        'id' => 5,
        'title' => "Reporting: In Conversation with Miriam Elder and Julia Ioffe",
        'start' => "$year-$month-26",
        'url' => "event-detail.html"
    ),

    array(
        'id' => 5,
        'title' => "The Orchestra of the Age of Enlightenment perform with Music",
        'start' => "$year-$month-29",
        'url' => "event-detail.html"
    )

));

?>