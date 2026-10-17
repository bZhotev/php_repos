
<?php
require __DIR__ . '/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Въведи име и валиден имейл.';
    } else {
        $password = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare(
                'INSERT INTO users (name, email, password)
                 VALUES (:name, :email, :password)'
            );

            $stmt->execute([
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ]);

            $message = 'Потребителят е добавен успешно!';
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $message = 'Този имейл вече съществува.';
            } else {
                error_log($e->getMessage());
                $message = 'Възникна грешка при записването.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Добавяне на потребител</title>
</head>
<body>
    <h1>Добавяне на потребител</h1>

    <?php if ($message !== ''): ?>
        <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form method="post">
        <label for="name">Име:</label>
        <input type="text" id="name" name="name" required>

        <br><br>

        <label for="email">Имейл:</label>
        <input type="email" id="email" name="email" required>

        <br><br>

        <button type="submit">Запиши потребителя</button>
    </form>
</body>
</html>
