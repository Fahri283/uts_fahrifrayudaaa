<?php
session_start();

// Hapus semua session
session_unset();
session_destroy();

// Ambil peran pengguna untuk pesan yang lebih personal sebelum dihapus
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Pengguna';

// Tentukan tujuan redirect
$redirect_target = "login.php";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout Berhasil</title>
    <meta http-equiv="refresh" content="3;url=<?= htmlspecialchars($redirect_target) ?>">
    <style>
        /* --- Style CSS Keren --- */
        :root {
            --primary-color: #007bff; /* Biru terang */
            --success-color: #28a745; /* Hijau */
            --background-color: #f8f9fa;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--background-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            color: #343a40;
        }

        .logout-box {
            background: #ffffff;
            padding: 40px 60px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            opacity: 0;
            animation: fadeIn 0.8s forwards;
            border-top: 5px solid var(--primary-color);
        }

        .icon {
            font-size: 4rem;
            color: var(--success-color);
            margin-bottom: 15px;
            animation: bounce 1s infinite alternate; /* Animasi bouncing */
        }

        .logout-box h2 {
            font-size: 2rem;
            margin-bottom: 10px;
            color: var(--primary-color);
        }

        .logout-box p {
            font-size: 1.1rem;
            color: #6c757d;
            margin-top: 0;
        }

        .redirect-info {
            font-size: 0.9rem;
            color: #adb5bd;
            margin-top: 20px;
        }
        
        /* --- Keyframes Animasi --- */
        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }
        
        @keyframes bounce {
            from {
                transform: translateY(0);
            }
            to {
                transform: translateY(-10px);
            }
        }
    </style>
</head>
<body>
    <div class="logout-box">
        <div class="icon">👋</div>
        <h2>Berhasil Logout!</h2>
        <p>Anda telah berhasil keluar dari sistem.</p>
        <p>Terima kasih atas sesi Anda, <?= htmlspecialchars($role) ?>!</p>
        
        <div class="redirect-info">
            Anda akan diarahkan kembali ke halaman login dalam 3 detik...
        </div>
    </div>
</body>
</html>