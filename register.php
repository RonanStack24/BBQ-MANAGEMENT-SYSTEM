<?php
include 'config.php';

$error = '';
$success = '';

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm = trim($_POST['confirm']);

    if (empty($username) || empty($email) || empty($password) || empty($confirm)) {
        $error = "All fields are required!";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long!";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->rowCount() > 0) {
            $error = "Username already exists!";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $email, $hashed])) {
                $success = "Registration successful! <a href='login.php' class='underline text-amber-400'>Login here</a>.";
            } else {
                $error = "Registration failed, please try again.";
            }
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>BBQ MANAGEMENT SYSTEM</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @keyframes smoke { 0%,100%{transform:translateY(0) scale(1);opacity:0.4}50%{transform:translateY(-30px) scale(1.1);opacity:0.6} }
    @keyframes glow  { 0%{opacity:0.3;transform:translateX(-50%) scale(1)}100%{opacity:0.8;transform:translateX(-50%) scale(1.3)} }
    @keyframes flicker{ 0%,100%{opacity:0.9}50%{opacity:0.6} }
  </style>
</head>

<body class="flex justify-center items-center min-h-screen bg-gradient-to-b from-neutral-900 to-black text-white font-sans overflow-hidden relative">
  <!-- smoke -->
  <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(255,255,255,0.05)_0%,transparent_70%)] opacity-40 animate-[smoke_12s_ease-in-out_infinite_alternate]"></div>
  <!-- flame glow -->
  <div class="absolute bottom-0 left-1/2 w-[350px] h-[250px] bg-[radial-gradient(circle,rgba(255,87,34,0.4)_0%,transparent_70%)] blur-3xl transform -translate-x-1/2 animate-[glow_4s_ease-in-out_infinite_alternate]"></div>
  
  <div class="flex flex-col md:flex-row bg-neutral-800 rounded-lg shadow-2xl overflow-hidden w-[90%] max-w-4xl relative z-10 border border-neutral-700">
    <div class="flex-1 hidden md:flex justify-center items-center bg-gradient-to-br from-neutral-800 to-neutral-900 border-r border-neutral-700 animate-[flicker_2s_ease-in-out_infinite]"></div>
     
    <div class="flex-1 bg-neutral-900 p-8 flex flex-col justify-center">
      <?php if ($error): ?>
        <div class="mb-4 p-3 rounded bg-red-700 text-white"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="mb-4 p-3 rounded bg-green-700 text-white"><?= $success ?></div>
      <?php endif; ?>

      <h2 class="text-3xl font-bold text-center mb-6 text-orange-500 drop-shadow-lg animate-[flicker_3s_ease-in-out_infinite]">Create Account</h2>

      <form method="post" action="" class="flex flex-col space-y-4">
        <input name="username" type="text" placeholder="Username" required
               class="p-3 rounded bg-neutral-800 text-white placeholder-gray-400 border border-neutral-700 focus:outline-none focus:ring-2 focus:ring-orange-500" />
        <input name="email" type="email" placeholder="Email" required
               class="p-3 rounded bg-neutral-800 text-white placeholder-gray-400 border border-neutral-700 focus:outline-none focus:ring-2 focus:ring-orange-500" />
        <input name="password" type="password" placeholder="Password" required
               class="p-3 rounded bg-neutral-800 text-white placeholder-gray-400 border border-neutral-700 focus:outline-none focus:ring-2 focus:ring-orange-500" />
        <input name="confirm" type="password" placeholder="Confirm Password" required
               class="p-3 rounded bg-neutral-800 text-white placeholder-gray-400 border border-neutral-700 focus:outline-none focus:ring-2 focus:ring-orange-500" />

        <button name="register" type="submit"
                class="bg-gradient-to-r from-orange-700 to-orange-500 py-3 rounded text-white font-semibold hover:shadow-[0_0_20px_rgba(255,179,0,0.7)] transition-all animate-[flicker_1.5s_ease-in-out_infinite]">
          Register
        </button>
      </form>

      <!-- Login Button -->
      <div class="mt-6 text-center">
        <a href="login.php"
           class="inline-block bg-gradient-to-r from-orange-700 to-orange-500 px-6 py-2 rounded text-white font-semibold hover:shadow-[0_0_15px_rgba(255,179,0,0.6)] transition-all">
          Already have an account? Login
        </a>
      </div>

      <!-- Social Buttons -->
      <div class="flex justify-between space-x-4 mt-6">
        <a href="oauth.php?provider=google" class="flex-1 p-2 bg-neutral-800 border border-neutral-700 rounded text-amber-400 hover:bg-neutral-700 transition text-center">Google</a>
        <a href="oauth.php?provider=facebook" class="flex-1 p-2 bg-neutral-800 border border-neutral-700 rounded text-amber-400 hover:bg-neutral-700 transition text-center">Facebook</a>
      </div>
    </div>
  </div>


<script>
 /**
  * FormValidator Class
  * Encapsulate all client-side validation
  */

  class FormValidator {
    constructor(formId) {
      this.form = document.getElementById(formId);
      if (!this.form) {
        console.error('Form with ID ${formId} not found.');
        return;
      }
      this.inputs = {
        username: document.getElementById('username'),
        email: document.getElementById('email'),
        password: document.getElementById('password'),
        confirm: document.getElementById('confirm'),
      };
      this.errors = {
        username: document.getElementById('usernameError'),
        email: document.getElementById('emailError'),
        password: document.getElementById('passwordError'),
        confirm: document.getElementById('confirmError'),
      };
      this.phpErrorBox = document.getElementById('php-error');
      this.init();
    }

    // Error handling 

    displayError(element, message) {
      element.textContent = message;
      element.classList.add('visible');
    }

    clearError(element) {
      element.textContent = '';
      element.classList.remove('visible');
    }

    // validation logic methods

    validateField(fieldName) {
      const input = this.inputs[fieldName];
      const errorElement = this.errors[fieldName];
      const value = input.value.trim();
      let message = '';
      let isValid = true;

      switch (fieldName) {
        case 'username':
          if (value === '') {
            message = 'Username is required.';
            isValid = false;
          }
          break;

      case 'email':
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (value === '') {
          message = 'Email is required.';
          isValid = false;
        } else if (!emailRegex.test(value)) {
          message = 'Please enter a valid email address.';
          isValid = false;
        }
        break;

      case 'password':
        if (value === '') {
          messsage = 'Password is required.';
          isValid = false;
        } else if (value.length < 6) {
          message = 'Password must be at least 6 characters.';
          isValid = false;
        }
        // Re validate confirm field if password changes
        if (isValid && this.inputs.confirm.value !== '') {
            this.validateField('confirm');
        }
        break;

      case 'confirm':
        const passwordValue = this.inputs.password.value;
        if (value === '') {
          message = 'Confirmation is required.';
          isValid = false;
        } else if (passwordValue !== value) {
          message = 'Passwords do not match!';
          isValid = false;
        }
        break;
      }

      if (isValid) {
        this.clearErrors(errorElement);
      } else {
        this.displayError(errorElement, message);
      }
      return isValid;
    }

    validateAll() {
      let allValid = true;
      // Validate fields in order
      ['username', 'email', 'password', 'confirm'].forEach(fieldName => {
        // Use a non-short-circuiting check 
        if (!this.validateField(fieldName)) {
         allValid = false;
        }
      });
      return allValid;
    }

    // Intialization and Event Binding

    init() {
      // Bind real-time validation to all input events
      Object.keys(this.inputs).forEach(fieldName => {
        this.inputs[fieldName].addEventListener('input', () => {
          this.validateField(fieldName);
        });
      });

      // Bind form submission event
      this.form.addEventListener('submit', (e) => this.handleSubmit(e));
    }

    handleSubmit(e) {
      // Clear previous PHP error message if it exists
      if (this.phpErrorBox) {
          this.phpErrorBox.style.display = 'none'
      }

      if (!this.validateAll()) {
        e.preventDefault();

        // Find the first invalid field and focus on it
        const firstInvalidField = ['username', 'email', 'password', 'confirm'].find(fieldName => {
          // Check if the corresponding error message is visible
          return this.errors[fieldName].classList.contains('visible');
        });

        if (firstInvalidField) {
          this.inputs[firstInvalidField].focus();
        }
      }
      // IF VALIDATEALL RETURNS TRUE, THE FORM SUBMITS NORMALLY TO PHP
    }
  }

  // INITIALIZE THE VALIDATOR WHEN THE DOM IS READY
  document.addEventListener('DOMContentLoaded', () => {
    // Create a single isntance of the validator for the registration form
    new FormValidator('registrationForm');
  });
  </script>
</body>
</html>
