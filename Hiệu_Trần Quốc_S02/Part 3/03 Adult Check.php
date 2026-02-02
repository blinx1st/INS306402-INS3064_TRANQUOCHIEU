<?php
function isAdult(?int $age): bool {
    if ($age === null) {
        return false;
    }
    return $age >= 18;
}

// Test input
$result = isAdult(null);

// Print output as required
echo $result ? "True" : "False";
?>
