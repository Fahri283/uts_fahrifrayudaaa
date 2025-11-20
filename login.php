<?php
session_start();

// Cek apakah user sudah login
if (isset($_SESSION['username'])) {
    // Ganti 'dashboard.php' dengan nama file yang benar jika berbeda
    header("Location: dashboard.php");
    exit;
}

// Proses login saat form dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Login sederhana (username: fahri, password: 12345)
    if ($username == 'fahri' && $password === '12345') {
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'Dosen';
        // Ganti 'dashboard.php' dengan nama file yang benar jika berbeda
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Polgan Mart - Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* CSS Mewah/Modern */
        
        /* 1. Reset dan Font Dasar */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6; /* Warna latar belakang lembut */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            color: #333;
        }

        /* 2. Container Login Card */
        .login-container {
            background: #ffffff;
            padding: 40px;
            border-radius: 15px; /* Sudut membulat */
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); /* Bayangan lembut untuk kesan 'floating' */
            width: 100%;
            max-width: 400px; /* Ukuran yang pas */
            text-align: center;
        }

        /* 3. Judul */
        h2 {
            font-size: 28px;
            color: #0d6efd; /* Warna utama */
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .subtitle {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 30px;
        }

        /* 4. Form Styling */
        form {
            text-align: left;
        }
        
        /* 5. Input Fields */
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 20px;
            border: 1px solid #ced4da; /* Garis tepi lembut */
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 16px;
            transition: border-color 0.3s, box-shadow 0.3s;
            outline: none; /* Hilangkan outline default */
        }
        
        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #0d6efd; /* Garis tepi biru saat fokus */
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25); /* Glow lembut saat fokus */
        }

        /* 6. Label */
        label {
            display: block;
            font-size: 14px;
            margin-bottom: 8px;
            color: #495057;
            font-weight: 500;
        }
        
        /* 7. Button Submit */
        button[type="submit"] {
            width: 100%;
            background-color: #0d6efd; /* Warna utama biru */
            color: white;
            padding: 14px 20px;
            margin-top: 10px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            font-weight: 600;
            transition: background-color 0.3s, transform 0.1s;
        }

        button[type="submit"]:hover {
            background-color: #0a58ca; /* Biru yang sedikit lebih gelap saat hover */
        }
        
        button[type="submit"]:active {
            transform: translateY(1px); /* Efek tekan */
        }

        /* 8. Error Message */
        .error-message {
            color: #dc3545; /* Merah untuk pesan error */
            background-color: #f8d7da; /* Latar belakang merah muda lembut */
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Polgan Mart</h2>
    <p class="subtitle">Sistem Penjualan</p>
    
    <?php if (!empty($error)): ?>
        <p class="error-message"><?= $error ?></p>
    <?php endif; ?>
    
    <form method="post">
        
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>
        
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
        
        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>