<?php
// Initialize or retrieve the columns
session_start();

if (!isset($_SESSION['columns'])) {
    $_SESSION['columns'] = ['Date 1']; // Start with one column
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_column'])) {
        // Add a new column
        $newColumnName = 'Date ' . (count($_SESSION['columns']) + 1);
        $_SESSION['columns'][] = $newColumnName;
    } elseif (isset($_POST['delete_column'])) {
        // Remove the last column if there are multiple columns
        if (count($_SESSION['columns']) > 1) {
            array_pop($_SESSION['columns']);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Table</title>
    <style>
        table {
            border-collapse: collapse;
            width: 50%;
            margin: 20px auto;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }
        form {
            text-align: center;
            margin: 20px;
        }
        button {
            padding: 10px 15px;
            margin: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <form method="POST">
        <button type="submit" name="add_column">Add Column</button>
        <button type="submit" name="delete_column">Delete Column</button>
    </form>

    <table>
        <tr>
            <?php foreach ($_SESSION['columns'] as $column): ?>
                <th><?php echo htmlspecialchars($column); ?></th>
            <?php endforeach; ?>
        </tr>
        <tr>
            <?php foreach ($_SESSION['columns'] as $column): ?>
                <td>-</td>
            <?php endforeach; ?>
        </tr>
    </table>
</body>
</html>
