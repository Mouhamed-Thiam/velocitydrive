/**
 * VelocityDrive – Contact Form with EmailJS
 * Sends exactly the parameters expected by the user's templates:
 * - Admin template "Contact Us": title, from_name, car_model, time, message, to_email, reply_to
 * - Auto-reply template "Auto-Reply": from_name, car_model, reply_to, to_email
 */

(function() {
  'use strict';

  // ========== EMAILJS CONFIGURATION (REPLACE WITH YOUR OWN VALUES) ==========
  const EMAILJS_PUBLIC_KEY = '0Jq4Z-F7K0NZyurEu';          // e.g., 'user_abc123'
  const EMAILJS_SERVICE_ID  = 'service_ljq5big';         // e.g., 'service_gmail'

  // Template ID for admin email (named "Contact Us" in your screenshot)
  const ADMIN_TEMPLATE_ID = 'template_pxmzqi5';

  // Template ID for auto-reply email (named "Auto-Reply" in your screenshot)
  const AUTO_REPLY_TEMPLATE_ID = 'template_lgfxc6q';

  const ADMIN_EMAIL = 'th1933238@gmail.com';

  document.addEventListener('DOMContentLoaded', function() {
    emailjs.init(EMAILJS_PUBLIC_KEY);

    const form = document.getElementById('contactForm');
    const feedbackDiv = document.getElementById('formFeedbackMessage');
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;

    if (!form) return;

    function showFeedback(message, isError = true) {
      feedbackDiv.innerHTML = `<div class="alert alert-${isError ? 'danger' : 'success'} alert-dismissible fade show" role="alert">
                                  ${message}
                                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>`;
      feedbackDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function setLoading(isLoading) {
      if (!submitBtn) return;
      submitBtn.disabled = isLoading;
      submitBtn.innerHTML = isLoading
        ? '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Sending...'
        : originalBtnText;
    }

    // Validation helpers
    function validateField(field) {
      let isValid = true;
      const value = field.value.trim();

      if (field.hasAttribute('required') && !value) isValid = false;
      else if (field.type === 'email' && value !== '') {
        const emailPattern = /^[^\s@]+@([^\s@.,]+\.)+[^\s@.,]{2,}$/;
        if (!emailPattern.test(value)) isValid = false;
      }
      else if (field.id === 'fullName' && value.length < 2 && value !== '') isValid = false;
      else if (field.id === 'messageText' && value.length < 5 && value !== '') isValid = false;

      if (!isValid) {
        field.classList.add('is-invalid');
        field.classList.remove('is-valid');
      } else if (value !== '') {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
      } else {
        field.classList.remove('is-valid', 'is-invalid');
      }
      return isValid;
    }

    function validateForm() {
      let valid = true;
      ['fullName', 'emailAddress', 'messageText'].forEach(id => {
        const field = document.getElementById(id);
        if (field && !validateField(field)) valid = false;
      });
      return valid;
    }

    function resetValidation() {
      document.querySelectorAll('.form-control, .form-select').forEach(f => {
        f.classList.remove('is-valid', 'is-invalid');
      });
    }

    // ----- FORM SUBMIT -----
    form.addEventListener('submit', async function(event) {
      event.preventDefault();
      feedbackDiv.innerHTML = '';
      resetValidation();

      if (!validateForm()) {
        showFeedback('⚠️ Please fill all required fields correctly (name, valid email, message at least 5 characters).');
        const firstInvalid = form.querySelector('.is-invalid');
        if (firstInvalid) firstInvalid.focus();
        return;
      }

      const fullName = document.getElementById('fullName').value.trim();
      const email = document.getElementById('emailAddress').value.trim();
      const carModel = document.getElementById('carModelSelect').value || 'Not specified';
      const userMessage = document.getElementById('messageText').value.trim();

      // Current date and time for admin email
      const now = new Date();
      const formattedTime = now.toLocaleString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
      });

      // ----- Admin email parameters (template "Contact Us") -----
      // Expected: title, from_name, car_model, time, message, to_email, reply_to
      const adminParams = {
        title: `Inquiry from ${fullName}`,           // used in subject: "Contact Us: {{title}}"
        from_name: fullName,
        car_model: carModel,
        time: formattedTime,
        message: userMessage,
        to_email: ADMIN_EMAIL,
        reply_to: email
      };

      // ----- Auto-reply parameters (template "Auto-Reply") -----
      // Expected: from_name, car_model, reply_to, to_email
      const autoReplyParams = {
        from_name: fullName,
        car_model: carModel,
        reply_to: email,          // customer's email address
        to_email: ADMIN_EMAIL     // so they can reply to you
      };

      setLoading(true);

      try {
        // 1. Send email to admin (template "Contact Us")
        await emailjs.send(EMAILJS_SERVICE_ID, ADMIN_TEMPLATE_ID, adminParams);
        
        // 2. Send auto-reply to customer (template "Auto-Reply")
        await emailjs.send(EMAILJS_SERVICE_ID, AUTO_REPLY_TEMPLATE_ID, autoReplyParams);

        showFeedback('✅ Your request has been sent! A confirmation email has been sent to you.', false);
        form.reset();
        resetValidation();
      } catch (error) {
        console.error('EmailJS error:', error);
        let errorMsg = '❌ Failed to send your request. Please check your internet connection and try again.';
        if (error.text) console.error('EmailJS details:', error.text);
        showFeedback(errorMsg);
      } finally {
        setLoading(false);
      }
    });

    // Real-time validation
    const allFields = form.querySelectorAll('input, textarea, select');
    allFields.forEach(field => {
      field.addEventListener('input', function() {
        validateField(this);
        if (feedbackDiv.querySelector('.alert-danger')) {
          feedbackDiv.innerHTML = '';
        }
      });
    });
  });
})();