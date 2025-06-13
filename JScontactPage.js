// דף צור קשר - WiseCart (מעודכן לשליחה לשרת)
document.addEventListener('DOMContentLoaded', function() {
    // טופס יצירת קשר
    const contactForm = document.getElementById('contact-form');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const subjectSelect = document.getElementById('subject');
    const messageTextarea = document.getElementById('message');
    const formSubmitBtn = document.querySelector('.form-submit');
    
    // טיפול באירוע השליחה של הטופס
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // וולידציה של השדות
            if (!validateForm()) {
                return;
            }
            
            // שליחת נתונים לשרת
            submitFormToServer();
        });
    }
    
    // פונקציה לשליחת הטופס לשרת
    async function submitFormToServer() {
        // הצגת מצב טעינה
        formSubmitBtn.disabled = true;
        formSubmitBtn.textContent = 'שולח...';
        
        // הכנת נתונים לשליחה
        const formData = {
            name: nameInput.value.trim(),
            email: emailInput.value.trim(),
            phone: phoneInput.value.trim(),
            subject: subjectSelect.value,
            message: messageTextarea.value.trim()
        };
        
        try {
            const response = await fetch('contact_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                showSuccessMessage();
                resetForm();
                
                // שליחת אירוע Google Analytics (אופציונלי)
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'form_submit', {
                        'event_category': 'Contact',
                        'event_label': 'Contact Form Submission'
                    });
                }
            } else {
                showErrorMessage(result.error || result.errors);
            }
            
        } catch (error) {
            console.error('Error submitting form:', error);
            showErrorMessage('שגיאה בשליחת הטופס. אנא נסה שנית.');
        } finally {
            // החזרת כפתור למצב רגיל
            formSubmitBtn.disabled = false;
            formSubmitBtn.textContent = 'שליחה';
        }
    }
    
    // פונקציה לוולידציה של הטופס
    function validateForm() {
        let isValid = true;
        
        // וולידציה של שם
        if (!nameInput.value.trim()) {
            showError(nameInput, 'נא להזין שם מלא');
            isValid = false;
        } else if (nameInput.value.trim().length < 2) {
            showError(nameInput, 'השם קצר מדי');
            isValid = false;
        } else {
            removeError(nameInput);
        }
        
        // וולידציה של דוא"ל
        if (!validateEmail(emailInput.value)) {
            showError(emailInput, 'נא להזין כתובת דוא"ל תקינה');
            isValid = false;
        } else {
            removeError(emailInput);
        }
        
        // וולידציה של טלפון (אם הוזן)
        if (phoneInput.value.trim() !== '' && !validatePhone(phoneInput.value)) {
            showError(phoneInput, 'נא להזין מספר טלפון תקין (05X-XXXXXXX)');
            isValid = false;
        } else {
            removeError(phoneInput);
        }
        
        // וולידציה של נושא
        if (!subjectSelect.value) {
            showError(subjectSelect, 'נא לבחור נושא');
            isValid = false;
        } else {
            removeError(subjectSelect);
        }
        
        // וולידציה של תוכן ההודעה
        if (!messageTextarea.value.trim()) {
            showError(messageTextarea, 'נא להזין תוכן הודעה');
            isValid = false;
        } else if (messageTextarea.value.trim().length < 10) {
            showError(messageTextarea, 'ההודעה קצרה מדי (מינימום 10 תווים)');
            isValid = false;
        } else if (messageTextarea.value.trim().length > 1000) {
            showError(messageTextarea, 'ההודעה ארוכה מדי (מקסימום 1000 תווים)');
            isValid = false;
        } else {
            removeError(messageTextarea);
        }
        
        return isValid;
    }
    
    // פונקציה לבדיקת תקינות של דוא"ל
    function validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    // פונקציה לבדיקת תקינות של מספר טלפון (בפורמט ישראלי)
    function validatePhone(phone) {
        // מקבל מספרים בפורמט 05X-XXXXXXX או 05XXXXXXXX או 05X XXXXXXX
        const phoneRegex = /^05\d([- ]?)(\d{7})$/;
        return phoneRegex.test(phone);
    }
    
    // פונקציה להצגת הודעת שגיאה
    function showError(inputElement, message) {
        removeError(inputElement);
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        errorDiv.style.color = 'var(--accent-color)';
        errorDiv.style.fontSize = '0.85rem';
        errorDiv.style.marginTop = '5px';
        
        inputElement.style.borderColor = 'var(--accent-color)';
        inputElement.parentNode.appendChild(errorDiv);
    }
    
    // פונקציה להסרת הודעת שגיאה
    function removeError(inputElement) {
        inputElement.style.borderColor = '';
        
        const parent = inputElement.parentNode;
        const errorDiv = parent.querySelector('.error-message');
        if (errorDiv) {
            parent.removeChild(errorDiv);
        }
    }
    
    // פונקציה לאיפוס הטופס
    function resetForm() {
        contactForm.reset();
        // הסרת כל הודעות השגיאה
        document.querySelectorAll('.error-message').forEach(el => el.remove());
    }
    
    // פונקציה להצגת הודעת הצלחה
    function showSuccessMessage() {
        const successModal = document.createElement('div');
        successModal.className = 'success-modal';
        successModal.innerHTML = `
            <div class="success-modal-content">
                <div class="success-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--secondary-color)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                </div>
                <h3>ההודעה נשלחה בהצלחה!</h3>
                <p>תודה על פנייתך. קיבלנו את הודעתך ונחזור אליך בהקדם האפשרי.</p>
                <button class="close-modal-btn btn btn-primary">סגור</button>
            </div>
        `;
        
        // עיצוב המודאל
        Object.assign(successModal.style, {
            position: 'fixed',
            top: '0',
            left: '0',
            width: '100%',
            height: '100%',
            backgroundColor: 'rgba(0, 0, 0, 0.5)',
            display: 'flex',
            justifyContent: 'center',
            alignItems: 'center',
            zIndex: '1000'
        });
        
        const modalContent = successModal.querySelector('.success-modal-content');
        Object.assign(modalContent.style, {
            backgroundColor: 'white',
            padding: '30px',
            borderRadius: '10px',
            textAlign: 'center',
            boxShadow: '0 5px 15px rgba(0, 0, 0, 0.2)',
            maxWidth: '400px'
        });
        
        document.body.appendChild(successModal);
        
        // מטפל בסגירת המודאל
        const closeBtn = successModal.querySelector('.close-modal-btn');
        closeBtn.addEventListener('click', function() {
            if (document.body.contains(successModal)) {
                document.body.removeChild(successModal);
            }
        });
        
        // סגירה בלחיצה על הרקע
        successModal.addEventListener('click', function(e) {
            if (e.target === successModal) {
                if (document.body.contains(successModal)) {
                    document.body.removeChild(successModal);
                }
            }
        });
    }
    
    // פונקציה להצגת הודעת שגיאה כללית
    function showErrorMessage(error) {
        let errorText = '';
        
        if (Array.isArray(error)) {
            errorText = error.join('<br>');
        } else {
            errorText = error;
        }
        
        const errorModal = document.createElement('div');
        errorModal.className = 'error-modal';
        errorModal.innerHTML = `
            <div class="error-modal-content">
                <div class="error-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--accent-color)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>
                </div>
                <h3>שגיאה בשליחת הטופס</h3>
                <p>${errorText}</p>
                <button class="close-modal-btn btn btn-primary">סגור</button>
            </div>
        `;
        
        // עיצוב זהה למודאל הצלחה
        Object.assign(errorModal.style, {
            position: 'fixed',
            top: '0',
            left: '0',
            width: '100%',
            height: '100%',
            backgroundColor: 'rgba(0, 0, 0, 0.5)',
            display: 'flex',
            justifyContent: 'center',
            alignItems: 'center',
            zIndex: '1000'
        });
        
        const modalContent = errorModal.querySelector('.error-modal-content');
        Object.assign(modalContent.style, {
            backgroundColor: 'white',
            padding: '30px',
            borderRadius: '10px',
            textAlign: 'center',
            boxShadow: '0 5px 15px rgba(0, 0, 0, 0.2)',
            maxWidth: '400px'
        });
        
        document.body.appendChild(errorModal);
        
        // מטפל בסגירת המודאל
        const closeBtn = errorModal.querySelector('.close-modal-btn');
        closeBtn.addEventListener('click', function() {
            if (document.body.contains(errorModal)) {
                document.body.removeChild(errorModal);
            }
        });
    }
    
    // אירועי פוקוס לשיפור חווית המשתמש
    const formInputs = document.querySelectorAll('.form-control');
    formInputs.forEach(input => {
        // מסיר שגיאות בעת הקלדה
        input.addEventListener('input', function() {
            removeError(this);
        });
        
        // מוסיף אפקט פוקוס משופר
        input.addEventListener('focus', function() {
            this.style.boxShadow = '0 0 0 3px rgba(52, 152, 219, 0.2)';
        });
        
        input.addEventListener('blur', function() {
            this.style.boxShadow = '';
        });
    });
});