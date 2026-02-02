<?php
function safeDiv(float $a, float $b): ?float {
    if ($b == 0) {
        return null;
    }
    return $a / $b;
}

// Test input
$result = safeDiv(10, 2);

// Print output
var_export($result);
?>
