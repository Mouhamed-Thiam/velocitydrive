/**
 * VelocityDrive – Formulaire de contact avec EmailJS
 * Envoie les données vers th1933238@gmail.com
 * Variables du template : title, name, time, message, from_name, reply_to
 */

(function() {
  'use strict';

  // ========== CONFIGURATION EMAILJS (À REMPLACER PAR VOS IDENTIFIANTS) ==========
  const EMAILJS_PUBLIC_KEY = 'VOTRE_PUBLIC_KEY';     // ex: 'user_abc123'
  const EMAILJS_SERVICE_ID  = 'VOTRE_SERVICE_ID';    // ex: 'service_gmail'
  const EMAILJS_TEMPLATE_ID = 'VOTRE_TEMPLATE_ID';   // ex: 'template_contact'

  // Destinataire fixe (votre email)
  const RECIPIENT_EMAIL = 'th1933238@gmail.com';

  document.addEventListener('DOMContentLoaded', function() {
    // Initialiser EmailJS
    emailjs.init(EMAILJS_PUBLIC_KEY);

    const form = document.getElementById('contactForm');
    const feedbackDiv = document.getElementById('formFeedbackMessage');
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;

    if (!form) return;

    // Afficher un message à l'utilisateur
    function showFeedback(message, isError = true) {
      feedbackDiv.innerHTML = `<div class="alert alert-${isError ? 'danger' : 'success'} alert-dismissible fade show" role="alert">
                                  ${message}
                                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>`;
      feedbackDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Gérer l'état du bouton (loading)
    function setLoading(isLoading) {
      if (!submitBtn) return;
      submitBtn.disabled = isLoading;
      submitBtn.innerHTML = isLoading
        ? '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Envoi en cours...'
        : originalBtnText;
    }

    // Validation d'un champ (Bootstrap)
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

    // Valider tout le formulaire
    function validateForm() {
      let valid = true;
      ['fullName', 'emailAddress', 'messageText'].forEach(id => {
        const field = document.getElementById(id);
        if (field && !validateField(field)) valid = false;
      });
      return valid;
    }

    // Réinitialiser les styles de validation
    function resetValidation() {
      document.querySelectorAll('.form-control, .form-select').forEach(f => {
        f.classList.remove('is-valid', 'is-invalid');
      });
    }

    // --- SOUMISSION DU FORMULAIRE ---
    form.addEventListener('submit', async function(event) {
      event.preventDefault();
      feedbackDiv.innerHTML = '';
      resetValidation();

      if (!validateForm()) {
        showFeedback('⚠️ Veuillez remplir correctement tous les champs obligatoires (nom, email valide, message d’au moins 5 caractères).');
        const firstInvalid = form.querySelector('.is-invalid');
        if (firstInvalid) firstInvalid.focus();
        return;
      }

      // Récupération des valeurs
      const fullName = document.getElementById('fullName').value.trim();
      const email = document.getElementById('emailAddress').value.trim();
      const carModel = document.getElementById('carModelSelect').value;
      const userMessage = document.getElementById('messageText').value.trim();

      // Date et heure actuelles (format lisible)
      const now = new Date();
      const formattedTime = now.toLocaleString('fr-FR', {
        dateStyle: 'full',
        timeStyle: 'short'
      });

      // Construction du message complet (modèle choisi + texte)
      let finalMessage = userMessage;
      if (carModel && carModel !== '') {
        finalMessage = `Modèle souhaité : ${carModel}\n\n${userMessage}`;
      }

      // Paramètres exacts pour votre template EmailJS
      const templateParams = {
        title: `Demande de contact - ${fullName}`,   // sera utilisé dans le sujet
        name: fullName,
        time: formattedTime,
        message: finalMessage,
        from_name: fullName,
        reply_to: email,
        to_email: RECIPIENT_EMAIL   // même si votre template utilise "To Email" fixe, on le précise
      };

      setLoading(true);

      try {
        const response = await emailjs.send(
          EMAILJS_SERVICE_ID,
          EMAILJS_TEMPLATE_ID,
          templateParams
        );
        console.log('Email envoyé avec succès :', response);
        showFeedback('✅ Votre demande a bien été envoyée ! Nous vous répondrons sous 24h.', false);
        form.reset();               // vide le formulaire
        resetValidation();          // enlève les coches vertes
      } catch (error) {
        console.error('Erreur EmailJS :', error);
        showFeedback('❌ Échec de l\'envoi. Vérifiez votre connexion ou réessayez plus tard.');
      } finally {
        setLoading(false);
      }
    });

    // Validation en temps réel pendant la saisie
    const allFields = form.querySelectorAll('input, textarea, select');
    allFields.forEach(field => {
      field.addEventListener('input', function() {
        validateField(this);
        // Efface l'alerte d'erreur dès que l'utilisateur commence à corriger
        if (feedbackDiv.querySelector('.alert-danger')) {
          feedbackDiv.innerHTML = '';
        }
      });
    });
  });
})();