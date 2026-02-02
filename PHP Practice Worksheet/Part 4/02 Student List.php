<?php
$students = [
    ["name" => "A", "grade" => 90],
    ["name" => "B", "grade" => 85],
    ["name" => "C", "grade" => 72],
];

echo "<table border='1' cellpadding='6' cellspacing='0'>";
echo "<tr><th>Name</th><th>Grade</th></tr>";

foreach ($students as $student) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($student["name"]) . "</td>";
    echo "<td>" . $student["grade"] . "</td>";
    echo "</tr>";
}

echo "</table>";
?>
