<?php
session_start();
include 'koneksi.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['user_id']    = $user['users_id'];
       $_SESSION['user_nama']  = $user['username'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role']  = $user['role'] ?? 'user';
        header("Location: index.php");
        exit;
    } else {
        $error = "Email atau password salah.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Masuk – Beautify</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Fraunces:ital,wght@0,300;0,600;1,300&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    :root {
        --pink: #F297A0;
        --pink-light: #F9D0CE;
        --bg: #F3EBD8;
        --text: #3B2A2B;
        --text-muted: #8A7070;
        --border: #EDD9CC;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); min-height: 100vh; display: flex; flex-direction: column; }
    header { background: var(--pink); padding: 14px 24px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
    .logo { font-family: 'Fraunces', serif; font-size: 26px; font-weight: 600; color: white; text-decoration: none; }
    .logo span { font-style: italic; font-weight: 300; }
    header a.back { color: rgba(255,255,255,0.9); text-decoration: none; font-size: 13px; font-weight: 600; }
    header a.back:hover { color: white; text-decoration: underline; }

    .auth-page { flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px 16px; }
    .auth-wrap { background: white; border-radius: 16px; box-shadow: 0 8px 32px rgba(0,0,0,0.1); overflow: hidden; display: flex; width: 100%; max-width: 860px; }
    .auth-left { background: linear-gradient(135deg, #F297A0 0%, #F9D0CE 60%, #F3EBD8 100%); padding: 48px 36px; display: flex; flex-direction: column; justify-content: center; width: 340px; flex-shrink: 0; }
    .auth-brand { font-family: 'Fraunces', serif; font-size: 38px; font-weight: 600; color: white; margin-bottom: 8px; }
    .auth-brand em { font-style: italic; font-weight: 300; }
    .auth-brand-tag { font-size: 13px; font-weight: 600; color: rgba(255,255,255,0.85); letter-spacing: 1px; margin-bottom: 20px; }
    .auth-left-desc { font-size: 13px; color: rgba(59,42,43,0.75); line-height: 1.7; }

    .auth-right { flex: 1; padding: 48px 40px; display: flex; flex-direction: column; justify-content: center; }
    .auth-greeting { font-size: 14px; color: var(--pink); font-weight: 600; margin-bottom: 6px; }
    .auth-title { font-family: 'Fraunces', serif; font-size: 28px; font-weight: 600; color: var(--text); margin-bottom: 6px; }
    .auth-sub { font-size: 13px; color: var(--text-muted); margin-bottom: 24px; }
    .auth-sub a { color: var(--pink); font-weight: 700; text-decoration: none; }
    .auth-sub a:hover { text-decoration: underline; }

    .alert-error { background: #FFF0F1; border: 1px solid #F9D0CE; border-radius: 8px; padding: 10px 14px; font-size: 13px; color: #b5606b; font-weight: 600; margin-bottom: 18px; display: flex; align-items: center; gap: 8px; }

    .form-group { margin-bottom: 14px; }
    .form-label { display: block; font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
    .input-wrap { position: relative; }
    .input-wrap .fi { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 14px; }
    .form-control { width: 100%; padding: 10px 14px 10px 36px; border: 1.5px solid var(--border); border-radius: 8px; font-size: 14px; font-family: inherit; color: var(--text); outline: none; transition: border-color 0.2s; }
    .form-control:focus { border-color: var(--pink); box-shadow: 0 0 0 3px rgba(242,151,160,0.15); }
    .form-control.has-right { padding-right: 42px; }
    .toggle-pw { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--text-muted); font-size: 14px; padding: 0; }
    .toggle-pw:hover { color: var(--pink); }

    .btn-primary { background: var(--pink); color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 700; font-family: inherit; cursor: pointer; transition: background 0.2s; display: inline-flex; align-items: center; gap: 8px; width: 100%; justify-content: center; margin-top: 8px; }
    .btn-primary:hover { background: #e07880; }

    @media (max-width: 640px) { .auth-left { display: none; } .auth-right { padding: 32px 24px; } }
</style>
</head>
<body>

<header>
    <a href="index.php" class="logo">Beauti<span>fy</span></a>
    <a href="register.php" class="back">Belum punya akun? Daftar →</a>
</header>

<div class="auth-page">
<div class="auth-wrap">
  <div class="auth-left">
    <div class="auth-brand">Beauti<em>fy</em></div>
    <div class="auth-brand-tag">Your Beauty, Our Priority</div>
    <p class="auth-left-desc">Selamat datang kembali! Login untuk menikmati promo eksklusif dan lacak pesananmu.</p>
  </div>
  <div class="auth-right">
    <div class="auth-greeting">👋 Halo lagi!</div>
    <h1 class="auth-title">Masuk ke Akun</h1>
    <p class="auth-sub">Belum punya akun? <a href="register.php">Daftar di sini</a></p>

    <?php if ($error): ?>
    <div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label class="form-label">Email</label>
        <div class="input-wrap">
          <i class="fas fa-envelope fi"></i>
          <input type="email" name="email" class="form-control" placeholder="email@contoh.com"
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autofocus>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Password</label>
        <div class="input-wrap">
          <i class="fas fa-lock fi"></i>
          <input type="password" name="password" id="pw" class="form-control has-right" placeholder="Password kamu" required>
          <button type="button" class="toggle-pw" onclick="togglePw()"><i class="fas fa-eye" id="eyeIcon"></i></button>
        </div>
      </div>
      <button type="submit" class="btn-primary"><i class="fas fa-sign-in-alt"></i> Masuk Sekarang</button>
    </form>
  </div>
</div>
</div>

<script>
function togglePw() {
    const i = document.getElementById('pw');
    const e = document.getElementById('eyeIcon');
    i.type = i.type === 'password' ? 'text' : 'password';
    e.className = i.type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
}
</script>
</body>
</html>