const contactForm = document.getElementById("contact-form");

if (contactForm) {
  contactForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    clearErrors();

    const name        = document.getElementById("name").value.trim();
    const email       = document.getElementById("email").value.trim();
    const message     = document.getElementById("message").value.trim();
    const formMessage = document.getElementById("form-message");
    let valid         = true;

    if (!name) {
      showFieldError("name-error", "Name is required.");
      valid = false;
    }

    if (!email || !isValidEmail(email)) {
      showFieldError("email-error", "A valid email is required.");
      valid = false;
    }

    if (!message) {
      showFieldError("message-error", "Message cannot be empty.");
      valid = false;
    }

    if (!valid) return;

    const submitBtn       = contactForm.querySelector(".auth-submit-btn");
    submitBtn.disabled    = true;
    submitBtn.textContent = "Sending...";

    try {
      const response = await fetch("php/contact.php", {
        method:  "POST",
        headers: { "Content-Type": "application/json" },
        body:    JSON.stringify({ name, email, message })
      });

      const data = await response.json();

      if (data.success) {
        formMessage.textContent = "Message sent! We'll be in touch soon.";
        formMessage.className   = "form-message success";
        contactForm.reset();
      } else {
        formMessage.textContent = data.message || "Could not send message.";
        formMessage.className   = "form-message error";
      }

    } catch (err) {
      formMessage.textContent = "Could not connect to the server.";
      formMessage.className   = "form-message error";
    }

    submitBtn.disabled    = false;
    submitBtn.textContent = "Send Message";
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
