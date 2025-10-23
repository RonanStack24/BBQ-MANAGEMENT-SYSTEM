<?php
// oauth.php - Temporary under-construction handler for social login buttons
$provider = $_GET['provider'] ?? 'unknown';
$provider = strtolower($provider);

// basic allow-list so only google/facebook are considered
$allowed = ['google','facebook'];
if (!in_array($provider, $allowed)) {
    http_response_code(400);
    echo "Invalid provider.";
    exit;
}

// Display a simple maintenance/under-construction page
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars(ucfirst($provider)) ?> Sign-in — Under Construction</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-neutral-900 text-white flex items-center justify-center">
  <div class="max-w-md p-8 bg-neutral-800 rounded-lg shadow-lg text-center">
    <h1 class="text-2xl font-bold mb-4"><?= htmlspecialchars(ucfirst($provider)) ?> Sign-in</h1>
    <p class="mb-6 text-neutral-300">Sorry — social sign-in with <strong><?= htmlspecialchars(ucfirst($provider)) ?></strong> is currently under construction.</p>
    <p class="text-sm text-neutral-400 mb-6">You can register / login using the site form for now.</p>

    <div class="flex justify-center gap-3">
      <a href="register.php" class="px-4 py-2 bg-orange-600 rounded">Register</a>
      <a href="login.php" class="px-4 py-2 bg-neutral-700 rounded">Login</a>
    </div>

    
</body>
</html>
