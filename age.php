<?php
$age = '';
$error = '';
$birth_year = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $birth_year = (int)$_POST['birth_year'];
    $current_year = date("Y");

    if ($birth_year > 1900 && $birth_year <= $current_year) {
        $age = $current_year - $birth_year;
    } else {
        $error = "Please enter a valid birth year.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Age Calculator</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-md mx-auto bg-white shadow-lg p-6 rounded-lg">
    <h2 class="text-2xl font-bold mb-4">Age Calculator</h2>

    <form method="post" class="space-y-4">
        <label class="block">
            <span class="text-gray-700">Enter your birth year:</span>
            <input type="number" name="birth_year" value="<?= $birth_year ?>" required
                   class="mt-1 block w-full border rounded p-2">
        </label>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Calculate Age</button>
    </form>

    <?php if ($error): ?>
        <p class="text-red-600 mt-4"><?= $error ?></p>
    <?php elseif ($age !== ''): ?>
        <p class="text-green-600 mt-4">You are <strong><?= $age ?></strong> years old.</p>
    <?php endif; ?>
</div>
</body>
</html>
