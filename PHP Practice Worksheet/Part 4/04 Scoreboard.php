<?php
$scores = [60, 70, 80, 90]; // ví dụ input

// Calculate stats
$sum = 0;
$max = $scores[0];
$min = $scores[0];

foreach ($scores as $score) {
    $sum += $score;

    if ($score > $max) {
        $max = $score;
    }

    if ($score < $min) {
        $min = $score;
    }
}

$avg = $sum / count($scores);

// Filter scores greater than average
$top = [];
foreach ($scores as $score) {
    if ($score > $avg) {
        $top[] = $score;
    }
}

// Output
echo "Avg: " . (int)$avg . ", Top: [" . implode(", ", $top) . "]";
?>
