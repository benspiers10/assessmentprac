<?php
$tip = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bill = (float)$_POST['bill'];
    $percentage = (float)$_POST['tip_percent'];
    $tip = round($bill * ($percentage / 100), 2);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Tip Calculator</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-md mx-auto bg-white shadow-lg p-6 rounded-lg">
    <h2 class="text-2xl font-bold mb-4">Tip Calculator</h2>

    <form method="post" class="space-y-4">
        <label class="block">
            <span class="text-gray-700">Bill Total:</span>
            <input type="number" step="0.01" name="bill" required class="mt-1 block w-full border rounded p-2">
        </label>

        <label class="block">
            <span class="text-gray-700">Tip Percentage:</span>
            <input type="number" name="tip_percent" required class="mt-1 block w-full border rounded p-2">
        </label>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Calculate Tip</button>
    </form>

    <?php if ($tip !== ''): ?>
        <p class="text-green-600 mt-4">Your tip should be: <strong>$<?= $tip ?></strong></p>
    <?php endif; ?>
</div>
</body>
</html>
