<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>mySimplePlanner</title>
</head>
<body>
    <h1>mySimplePlanner Rejestracja</h1>

    <?php if (isset($message) && $message): ?>
        <p style="color:red;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="post" action="/register">
        <div>
            <label for="name">Nazwa użytkownika:</label>
            <input type="text" name="name" id="name" required>
        </div>
        <div>
            <label for="password">Hasło:</label>
            <input type="password" name="password" id="password" required>
        </div>
        <button type="submit">Zarejestruj</button>
    </form>
    <p>Masz już konto? <a href="/">Zaloguj się</a>.</p>
</body>
</html>