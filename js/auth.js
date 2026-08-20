const loginForm    = document.getElementById("login-form");
const registerForm = document.getElementById("register-form");

if (loginForm) {
  loginForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    clearErrors();

    const email       = document.getElementById("email").value.trim();
    const password    = document.getElementById("password").value;
    const formMessage = document.getElementById("form-message");
    let valid         = true;

    if (!email) {
      showFieldError("email-error", "Email is required.");
      valid = false;
    }

    if (!password) {
      showFieldError("password-error", "Password is required.");
      valid = false;
    }

    if (!valid) return;

    const submitBtn       = loginForm.querySelector(".auth-submit-btn");
    submitBtn.disabled    = true;
    submitBtn.textContent = "Signing in...";

    try {
      const response = await fetch("php/login.php", {
        method:  "POST",
        headers: { "Content-Type": "application/json" },
        body:    JSON.stringify({ email, password })
      });

      const data = await response.json();

      if (data.success) {
        formMessage.textContent = "Logged in! Redirecting...";
        formMessage.className   = "form-message success";
        const destination = data.user && data.user.role === "admin"
          ? "admin/index.php"
          : "menu.html";
        setTimeout(() => { window.location.replace(destination); }, 800);
      } else {
        formMessage.textContent = data.message || "Login failed.";
        formMessage.className   = "form-message error";
        submitBtn.disabled      = false;
        submitBtn.textContent   = "Sign In";
      }

    } catch (err) {
      formMessage.textContent = "Could not connect to the server.";
      formMessage.className   = "form-message error";
      submitBtn.disabled      = false;
      submitBtn.textContent   = "Sign In";
    }
  });
}

if (registerForm) {
  registerForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    clearErrors();

    const name            = document.getElementById("name").value.trim();
    const email           = document.getElementById("email").value.trim();
    const password        = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm-password").value;
    const formMessage     = document.getElementById("form-message");
    let valid             = true;

    if (!name) {
      showFieldError("name-error", "Name is required.");
      valid = false;
    }

    if (!email || !isValidEmail(email)) {
      showFieldError("email-error", "A valid email is required.");
      valid = false;
    }

    if (password.length < 8) {
      showFieldError("password-error", "Password must be at least 8 characters.");
      valid = false;
    }

    if (password !== confirmPassword) {
      showFieldError("confirm-password-error", "Passwords do not match.");
      valid = false;
    }

    if (!valid) return;

    const submitBtn       = registerForm.querySelector(".auth-submit-btn");
    submitBtn.disabled    = true;
    submitBtn.textContent = "Creating account...";

    try {
      const response = await fetch("php/register.php", {
        method:  "POST",
        headers: { "Content-Type": "application/json" },
        body:    JSON.stringify({ name, email, password })
      });

      const data = await response.json();

      if (data.success) {
        formMessage.textContent = "Account created! Redirecting to login...";
        formMessage.className   = "form-message success";
        setTimeout(() => { window.location.href = "login.html"; }, 1500);
      } else {
        formMessage.textContent = data.message || "Registration failed.";
        formMessage.className   = "form-message error";
        submitBtn.disabled      = false;
        submitBtn.textContent   = "Create Account";
      }

    } catch (err) {
      formMessage.textContent = "Could not connect to the server.";
      formMessage.className   = "form-message error";
      submitBtn.disabled      = false;
      submitBtn.textContent   = "Create Account";
    }
  });
}

function isValidEmail(email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function showFieldError(elementId, message) {
  const el = document.getElementById(elementId);
  if (el) el.textContent = message;
}

function clearErrors() {
  document.querySelectorAll(".field-error").forEach(el => { el.textContent = ""; });
  const msg = document.getElementById("form-message");
  if (msg) { msg.textContent = ""; msg.className = "form-message"; }
}
