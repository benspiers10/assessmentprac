<?php
$monthly_payment = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $loan = (float)$_POST['loan'];
    $rate = (float)$_POST['rate'] / 12 / 100;
    $months = (int)$_POST['months'];

    if ($rate > 0) {
        $monthly_payment = $loan * $rate * pow(1 + $rate, $months) / (pow(1 + $rate, $months) - 1);
    } else {
        $monthly_payment = $loan / $months;
    }
    $monthly_payment = round($monthly_payment, 2);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Loan Calculator</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-md mx-auto bg-white shadow-lg p-6 rounded-lg">
    <h2 class="text-2xl font-bold mb-4">Loan / EMI Calculator</h2>

    <form method="post" class="space-y-4">
        <label class="block">
            <span class="text-gray-700">Loan Amount:</span>
            <input type="number" step="0.01" name="loan" required class="mt-1 block w-full border rounded p-2">
        </label>

        <label class="block">
            <span class="text-gray-700">Annual Interest Rate (%):</span>
            <input type="number" step="0.01" name="rate" required class="mt-1 block w-full border rounded p-2">
        </label>

        <label class="block">
            <span class="text-gray-700">Loan Term (months):</span>
            <input type="number" name="months" required class="mt-1 block w-full border rounded p-2">
        </label>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Calculate</button>
    </form>

    <?php if ($monthly_payment): ?>
        <p class="text-green-600 mt-4">Your monthly payment is: <strong>$<?= $monthly_payment ?></strong></p>
    <?php endif; ?>
</div>
</body>
</html>
