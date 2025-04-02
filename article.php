<?php
session_start();
$mysqli = new mysqli("localhost", "root", "", "blog");

$article_id = $_GET['id'] ?? 1;

// Fake logged-in user (you can replace this with session-based login)
$user = [
    'id' => 1,
    'username' => 'john',
    'is_admin' => 0 // Change to 1 for admin
];

// Fetch article
$stmt = $mysqli->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->bind_param("i", $article_id);
$stmt->execute();
$article = $stmt->get_result()->fetch_assoc();

// Add comment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_comment'])) {
    $comment = trim($_POST['comment']);
    if (!empty($comment)) {
        $stmt = $mysqli->prepare("INSERT INTO comments (user_id, article_id, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $user['id'], $article_id, $comment);
        $stmt->execute();
    }
    header("Location: article.php?id=$article_id");
    exit;
}

// Delete comment
if (isset($_GET['delete'])) {
    $comment_id = (int)$_GET['delete'];
    $stmt = $mysqli->prepare("SELECT user_id FROM comments WHERE id = ?");
    $stmt->bind_param("i", $comment_id);
    $stmt->execute();
    $owner = $stmt->get_result()->fetch_assoc();

    if ($owner && ($owner['user_id'] == $user['id'] || $user['is_admin'])) {
        $stmt = $mysqli->prepare("DELETE FROM comments WHERE id = ?");
        $stmt->bind_param("i", $comment_id);
        $stmt->execute();
    }
    header("Location: article.php?id=$article_id");
    exit;
}

// Edit comment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_comment'])) {
    $comment_id = (int)$_POST['comment_id'];
    $new_text = trim($_POST['comment']);
    $stmt = $mysqli->prepare("SELECT user_id FROM comments WHERE id = ?");
    $stmt->bind_param("i", $comment_id);
    $stmt->execute();
    $owner = $stmt->get_result()->fetch_assoc();

    if ($owner && $owner['user_id'] == $user['id']) {
        $stmt = $mysqli->prepare("UPDATE comments SET comment = ? WHERE id = ?");
        $stmt->bind_param("si", $new_text, $comment_id);
        $stmt->execute();
    }
    header("Location: article.php?id=$article_id");
    exit;
}

// Get all comments
$stmt = $mysqli->prepare("
    SELECT c.id, c.comment, c.user_id, u.username, c.created_at
    FROM comments c
    JOIN users u ON c.user_id = u.id
    WHERE article_id = ?
    ORDER BY c.created_at DESC
");
$stmt->bind_param("i", $article_id);
$stmt->execute();
$comments = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($article['title']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
    <div class="max-w-3xl mx-auto p-6 bg-white shadow-xl mt-10 rounded-lg">

        <h1 class="text-3xl font-bold mb-4"><?= htmlspecialchars($article['title']) ?></h1>
        <p class="text-gray-700 mb-8"><?= nl2br(htmlspecialchars($article['content'])) ?></p>

        <hr class="mb-6">

        <h2 class="text-2xl font-semibold mb-4">Comments</h2>

        <!-- Comment Form -->
        <form method="post" class="mb-6">
            <textarea name="comment" required placeholder="Write your comment..."
                      class="w-full p-3 border rounded mb-2 resize-none focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
            <button type="submit" name="new_comment"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Post Comment</button>
        </form>

        <!-- Comments List -->
        <ul class="space-y-6">
            <?php while ($row = $comments->fetch_assoc()): ?>
                <li class="p-4 border rounded bg-gray-50 shadow-sm">
                    <div class="flex justify-between items-center mb-2">
                        <strong class="text-blue-600"><?= htmlspecialchars($row['username']) ?></strong>
                        <span class="text-sm text-gray-500"><?= $row['created_at'] ?></span>
                    </div>

                    <?php if (isset($_GET['edit']) && $_GET['edit'] == $row['id'] && $row['user_id'] == $user['id']): ?>
                        <form method="post">
                            <textarea name="comment" class="w-full p-2 border rounded mb-2"><?= htmlspecialchars($row['comment']) ?></textarea>
                            <input type="hidden" name="comment_id" value="<?= $row['id'] ?>">
                            <button type="submit" name="edit_comment"
                                    class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">Save</button>
                        </form>
                    <?php else: ?>
                        <p class="mb-2"><?= nl2br(htmlspecialchars($row['comment'])) ?></p>
                        <?php if ($row['user_id'] == $user['id'] || $user['is_admin']): ?>
                            <div class="text-sm space-x-4">
                                <a href="article.php?id=<?= $article_id ?>&edit=<?= $row['id'] ?>" class="text-yellow-600 hover:underline">Edit</a>
                                <a href="article.php?id=<?= $article_id ?>&delete=<?= $row['id'] ?>" 
                                   onclick="return confirm('Delete this comment?')" 
                                   class="text-red-600 hover:underline">Delete</a>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
</body>
</html>
