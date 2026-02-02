<?php
function isPrime(int $n): bool {
    if ($n < 2) {
        return false;
    }

    for ($i = 2; $i * $i <= $n; $i++) {
        if ($n % $i == 0) {
            return false;
        }
    }
    return true;
}

for ($i = 1; $i <= 100; $i++) {
    if (isPrime($i)) {
        echo $i;
        if ($i < 97) { // 97 là số nguyên tố cuối cùng < 100
            echo ", ";
        }
    }
}
?>
