<?php
// login.php
session_start();
require 'config.php'; // must create $pdo (PDO instance)

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Username and password are required.';
    } else {
        // fetch user by username
        $stmt = $pdo->prepare("SELECT id, username, password, name FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && !empty($user['password']) && password_verify($password, $user['password'])) {
            // success - set session
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['name'] = $user['name'] ?? $user['username'];

            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login - BBQ MANAGEMENT SYSTEM</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Animations (kept small, used by Tailwind animate[...] utility) */
    @keyframes smoke { 0%,100%{transform:translateY(0) scale(1);opacity:0.4}50%{transform:translateY(-30px) scale(1.1);opacity:0.6} }
    @keyframes glow  { 0%{opacity:0.3;transform:translateX(-50%) scale(1)}100%{opacity:0.8;transform:translateX(-50%) scale(1.3)} }
    @keyframes flicker{ 0%,100%{opacity:0.9}50%{opacity:0.6} }
  </style>
</head>
<body class="flex justify-center items-center min-h-screen bg-gradient-to-b from-neutral-900 to-black text-white font-sans overflow-hidden relative">
  <!-- smoke background -->
  <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(255,255,255,0.05)_0%,transparent_70%)] opacity-40 animate-[smoke_12s_ease-in-out_infinite_alternate]"></div>
  <!-- flame glow -->
  <div class="absolute bottom-0 left-1/2 w-[350px] h-[250px] bg-[radial-gradient(circle,rgba(255,87,34,0.4)_0%,transparent_70%)] blur-3xl transform -translate-x-1/2 animate-[glow_4s_ease-in-out_infinite_alternate]"></div>

  <div class="flex flex-col md:flex-row bg-neutral-800 rounded-lg shadow-2xl overflow-hidden w-[90%] max-w-4xl relative z-10 border border-neutral-700">
    <!-- left decorative panel -->
    <div class="flex-1 hidden md:flex justify-center items-center bg-gradient-to-br from-neutral-800 to-neutral-900 border-r border-neutral-700 animate-[flicker_2s_ease-in-out_infinite]">
      <!-- optional: place an SVG/flame icon here later -->
    </div>

    <!-- right form panel -->
    <div class="flex-1 bg-neutral-900 p-8 flex flex-col justify-center">
      <?php if ($error): ?>
        <div class="mb-4 p-3 rounded bg-red-700 text-white"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <h2 class="text-3xl font-bold text-center mb-6 text-orange-500 drop-shadow-lg animate-[flicker_3s_ease-in-out_infinite]">Sign In</h2>

      <form method="post" action="" class="flex flex-col space-y-4">
        <input name="username" type="text" placeholder="Username" required
               class="p-3 rounded bg-neutral-800 text-white placeholder-gray-400 border border-neutral-700 focus:outline-none focus:ring-2 focus:ring-orange-500" />
        <input name="password" type="password" placeholder="Password" required
               class="p-3 rounded bg-neutral-800 text-white placeholder-gray-400 border border-neutral-700 focus:outline-none focus:ring-2 focus:ring-orange-500" />

        <button type="submit" class="bg-gradient-to-r from-orange-700 to-orange-500 py-3 rounded text-white font-semibold hover:shadow-[0_0_20px_rgba(255,179,0,0.7)] transition-all animate-[flicker_1.5s_ease-in-out_infinite]">
          Login
        </button>
      </form>

      <div class="flex justify-between space-x-4 mt-6">
        <a href="/register.php" class="flex-1 p-2 bg-neutral-800 border border-neutral-700 rounded text-neutral-300 hover:bg-neutral-700 transition text-center">Register</a>
        <a href="/oauth.php?provider=google" class="flex-1 p-2 bg-neutral-800 border border-neutral-700 rounded text-amber-400 hover:bg-neutral-700 transition text-center">Google</a>
        <a href="/oauth.php?provider=facebook" class="flex-1 p-2 bg-neutral-800 border border-neutral-700 rounded text-amber-400 hover:bg-neutral-700 transition text-center">Facebook</a>
      </div>

      <div class="mt-4 text-center text-sm text-neutral-400">
        <!-- optional: forgot password link -->
        <a href="forgot.php" class="text-amber-300 underline">Forgot password?</a>
      </div>
    </div>
  </div>
</body>
</html>
