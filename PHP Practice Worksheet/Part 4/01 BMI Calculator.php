<?php
function calculateBMI(float $kg, float $m): array {
    if ($m <= 0) {
        return ["bmi" => null, "category" => "Invalid"];
    }

    $bmi = $kg / ($m * $m);

    if ($bmi < 18.5) {
        $category = "Under";
    } elseif ($bmi < 25) {   // 18.5 - 24.9
        $category = "Normal";
    } else {                 // 25+
        $category = "Over";
    }

    return [
        "bmi" => round($bmi, 1),
        "category" => $category
    ];
}

$result = calculateBMI(60, 1.7);
echo "BMI: {$result['bmi']} - {$result['category']}";
?>
