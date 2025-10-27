<?php
// login.php
session_start();
require 'config.php'; // must create $pdo (PDO instance)

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and trim inputs
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Username and password are required.';
    } else {
        // fetch user by username
        // Use prepared statements to prevent SQL Injection
        $stmt = $pdo->prepare("SELECT id, username, password, name FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // Use password_verify for secure password checking
        if ($user && !empty($user['password']) && password_verify($password, $user['password'])) {
            // success - set session
            session_regenerate_id(true); // Prevents Session Fixation
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['name'] = $user['name'] ?? $user['username'];

            header('Location: dashboard.php');
            exit;
        } else {
            // Use a generic message for both username and password to prevent enumeration attacks
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
        /* Add style for JS error messages */
        .error-message {
            color: #ef4444; /* red-500 */
            font-size: 0.875rem; /* sm */
            margin-top: 0.25rem;
            display: none;
        }
        .error-message.visible {
            display: block;
        }
    </style>
</head>
<body class="flex justify-center items-center min-h-screen bg-gradient-to-b from-neutral-900 to-black text-white font-sans overflow-hidden relative">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(255,255,255,0.05)_0%,transparent_70%)] opacity-40 animate-[smoke_12s_ease-in-out_infinite_alternate]"></div>
    <div class="absolute bottom-0 left-1/2 w-[350px] h-[250px] bg-[radial-gradient(circle,rgba(255,87,34,0.4)_0%,transparent_70%)] blur-3xl transform -translate-x-1/2 animate-[glow_4s_ease-in-out_infinite_alternate]"></div>

    <div class="flex flex-col md:flex-row bg-neutral-800 rounded-lg shadow-2xl overflow-hidden w-[90%] max-w-4xl relative z-10 border border-neutral-700">
        <div class="flex-1 hidden md:flex justify-center items-center bg-gradient-to-br from-neutral-800 to-neutral-900 border-r border-neutral-700 animate-[flicker_2s_ease-in-out_infinite]">
            </div>

        <div class="flex-1 bg-neutral-900 p-8 flex flex-col justify-center">
            <?php if ($error): ?>
                <div id="php-error" class="mb-4 p-3 rounded bg-red-700 text-white"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <h2 class="text-3xl font-bold text-center mb-6 text-orange-500 drop-shadow-lg animate-[flicker_3s_ease-in-out_infinite]">Sign In</h2>

            <form method="post" action="" id="loginForm" class="flex flex-col space-y-4">
                <div>
                    <input name="username" type="text" placeholder="Username" required id="username"
                        class="p-3 w-full rounded bg-neutral-800 text-white placeholder-gray-400 border border-neutral-700 focus:outline-none focus:ring-2 focus:ring-orange-500" />
                    <div id="usernameError" class="error-message"></div>
                </div>

                <div>
                    <input name="password" type="password" placeholder="Password" required id="password"
                        class="p-3 w-full rounded bg-neutral-800 text-white placeholder-gray-400 border border-neutral-700 focus:outline-none focus:ring-2 focus:ring-orange-500" />
                    <div id="passwordError" class="error-message"></div>
                </div>

                <button type="submit" class="bg-gradient-to-r from-orange-700 to-orange-500 py-3 rounded text-white font-semibold hover:shadow-[0_0_20px_rgba(255,179,0,0.7)] transition-all animate-[flicker_1.5s_ease-in-out_infinite]">
                    Login
                </button>
            </form>

            <div class="flex justify-between space-x-4 mt-6">
                <!-- REGISTER: Corrected to relative path previously -->
                <a href="register.php" class="flex-1 p-2 bg-neutral-800 border border-neutral-700 rounded text-neutral-300 hover:bg-neutral-700 transition text-center">Register</a>
                
                <!-- GOOGLE: CORRECTED to relative path -->
                <a href="oauth.php?provider=google" class="flex-1 p-2 bg-neutral-800 border border-neutral-700 rounded text-amber-400 hover:bg-neutral-700 transition text-center">Google</a>
                
                <!-- FACEBOOK: CORRECTED to relative path -->
                <a href="oauth.php?provider=facebook" class="flex-1 p-2 bg-neutral-800 border border-neutral-700 rounded text-amber-400 hover:bg-neutral-700 transition text-center">Facebook</a>
            </div>

            <div class="mt-4 text-center text-sm text-neutral-400">
                <a href="forgot.php" class="text-amber-300 underline">Forgot password?</a>
            </div>
        </div>
    </div>

<script>
    /**
     * FormValidator Class (Simplified for Login)
     * Checks only for empty fields since the server handles credential verification.
     */
    class FormValidator {
      constructor(formId) {
        this.form = document.getElementById(formId);
        if (!this.form) {
          console.error(`Form with ID ${formId} not found.`);
          return;
        }
        // Get elements by ID, as they are now present in the HTML
        this.inputs = {
          username: document.getElementById('username'),
          password: document.getElementById('password'),
        };
        this.errors = {
          username: document.getElementById('usernameError'),
          password: document.getElementById('passwordError'),
        };
        // Added check for the element before setting the property
        // The ID 'php-error' must be added to the PHP error box
        this.phpErrorBox = document.getElementById('php-error');
        this.init();
      }

      displayError(element, message) {
        if (element) {
          element.textContent = message;
          // Use 'visible' class added in the CSS <style> block
          element.classList.add('visible'); 
        }
      }

      clearError(element) {
        if (element) {
          element.textContent = '';
          element.classList.remove('visible');
        }
      }

      validateField(fieldName) {
        const input = this.inputs[fieldName];
        const errorElement = this.errors[fieldName];

        // Check if input/error elements exist for the field
        if (!input || !errorElement) return true; 

        const value = input.value.trim(); // Fixed: Use input.value.trim() to get the content
        let message = '';
        let isValid = true;

        if (value === '') {
          message = fieldName.charAt(0).toUpperCase() + fieldName.slice(1) + ' is required.';
          isValid = false;
        }
        // Note: For a real app, you'd add more checks here (e.g., min length)

        if (isValid) {
          this.clearError(errorElement);
        } else {
          this.displayError(errorElement, message);
        }
        return isValid;
      }

      validateAll() {
        let allValid = true;
        // Validate fields in order, using non-short-circuiting check
        ['username', 'password'].forEach(fieldName => {
          if (!this.validateField(fieldName)) {
            allValid = false;
          }
        });
        return allValid;
      }

      init() {
        // Bind real-time validation to all input events
        Object.keys(this.inputs).forEach(fieldName => {
            // Check if the input element exists before adding listener
            if (this.inputs[fieldName]) {
              this.inputs[fieldName].addEventListener('input', () => {
                this.validateField(fieldName);
              });
            }
        });

        // Bind form submission event
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
      }

      handleSubmit(e) {
        // Clear previous PHP error message if it exists
        if (this.phpErrorBox) {
            // Use remove() or hide logic instead of setting display = 'none' directly
            // For simplicity with the existing HTML, we'll use remove() on the element if it exists
            this.phpErrorBox.remove(); 
        }

        if (!this.validateAll()) {
          e.preventDefault();

          // Find the first invalid field and focus on it
          const firstInvalidField = ['username', 'password'].find(fieldName => {
            return this.errors[fieldName] && this.errors[fieldName].classList.contains('visible');
          });

          if (firstInvalidField) {
            this.inputs[firstInvalidField].focus();
          }
        }
      }
    }

    // Initialize the validator when the DOM is ready
    document.addEventListener('DOMContentLoaded', () => {
      // Pass the form ID used in the HTML
      new FormValidator('loginForm');
    });
  </script>
</body>
</html>
