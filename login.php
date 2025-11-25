<?php
session_start();

$valid_user = [
    'username' => 'fahri',
    'email'    => 'frayudafahri@gmail.com',
    'password' => '12345' 
];

// Cek apakah user sudah login
if (isset($_SESSION['username'])) {
    // Ganti 'dashboard.php' jika nama file berbeda
    header("Location: dashboard.php");
    exit;
}

$error = '';

// Proses login saat form dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username_input = $_POST['username'] ?? '';
    $email_input    = $_POST['email'] ?? '';
    $password_input = $_POST['password'] ?? '';

    // Cek kecocokan
    if (
        $username_input == $valid_user['username'] &&
        $email_input    == $valid_user['email'] &&
        $password_input === $valid_user['password']
    ) {
        $_SESSION['username'] = $username_input;
        $_SESSION['role'] = 'Dosen';
        // Ganti 'dashboard.php' jika nama file berbeda
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Username, email, atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        /* --- Style CSS untuk Mempercantik Tampilan --- */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6; /* Warna latar belakang lembut */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .login-container {
            background: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); /* Bayangan elegan */
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
        }

        .login-container h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            border-bottom: 2px solid #faf606ff;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            border-color: #faf606ff; /* Fokus berwarna biru */
            outline: none;
        }

        .btn-login {
            width: 100%;
            background-color: #faf606ff; /* Warna tombol utama */
            color: white;
            padding: 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .btn-login:hover {
            background-color: #faf606ff; /* Warna lebih gelap saat hover */
        }

        .error-message {
            color: #dc3545; /* Warna merah untuk pesan error */
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>FAZU BANANA MELT </h2>

        <?php if (!empty($error)): ?>
            <div class="error-message">
                <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn-login">MASUK</button>
        </form>
    </div>
</body>
</html>