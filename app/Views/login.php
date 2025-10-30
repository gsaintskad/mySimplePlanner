<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>mySimplePlanner</title>
</head>
<body>
    <h1>mySimplePlanner Logowanie</h1>

    <?php if (isset($message) && $message): ?>
        <p style="color:red;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="post" action="/">
        <div>
            <label for="name">Nazwa użytkownika:</label>
            <input type="text" name="name" id="name" required>
        </div>
        <div>
            <label for="password">Hasło:</label>
            <input type="password" name="password" id="password" required>
        </div>
        <button type="submit">Zaloguj</button>
    </form>
    
    <p>Nie masz konta? <a href="/register">Zarejestruj się</a>.</p>
</body>
</html>