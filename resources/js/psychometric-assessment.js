/**
 * Psychometric Assessment Form Handler
 * Handles dynamic form rendering and validation
 */

/**
 * Initialize assessment form
 */
function initAssessmentForm() {
    // Auto-save draft responses
    const form = document.getElementById('assessmentForm');
    if (form) {
        const assessmentId = form.getAttribute('action').match(/\/(\d+)\/complete/)?.[1];
        
        if (assessmentId) {
            // Load saved draft
            loadDraft(assessmentId);
            
            // Save draft on input
            form.addEventListener('input', debounce(() => {
                saveDraft(assessmentId);
            }, 1000));
        }
    }
}

/**
 * Save draft responses to localStorage
 */
function saveDraft(assessmentId) {
    const form = document.getElementById('assessmentForm');
    if (!form) return;
    
    const formData = new FormData(form);
    const responses = {};
    
    for (let [key, value] of formData.entries()) {
        if (key.startsWith('responses[')) {
            responses[key] = value;
        }
    }
    
    localStorage.setItem(`assessment_draft_${assessmentId}`, JSON.stringify(responses));
}

/**
 * Load draft responses from localStorage
 */
function loadDraft(assessmentId) {
    const draftData = localStorage.getItem(`assessment_draft_${assessmentId}`);
    if (!draftData) return;
    
    try {
        const responses = JSON.parse(draftData);
        const form = document.getElementById('assessmentForm');
        if (!form) return;
        
        for (let [key, value] of Object.entries(responses)) {
            const input = form.querySelector(`[name="${key}"]`);
            if (input) {
                if (input.type === 'radio') {
                    const radio = form.querySelector(`[name="${key}"][value="${value}"]`);
                    if (radio) radio.checked = true;
                } else {
                    input.value = value;
                }
            }
        }
        
        // Update progress
        if (typeof updateProgress === 'function') {
            updateProgress();
        }
    } catch (e) {
        console.error('Error loading draft:', e);
    }
}

/**
 * Clear draft responses
 */
function clearDraft(assessmentId) {
    localStorage.removeItem(`assessment_draft_${assessmentId}`);
}

/**
 * Validate assessment form before submission
 */
function validateAssessmentForm() {
    const form = document.getElementById('assessmentForm');
    if (!form) return false;
    
    const questions = form.querySelectorAll('.question-item');
    let allAnswered = true;
    
    questions.forEach((question, index) => {
        const inputs = question.querySelectorAll('input[required], textarea[required]');
        let questionAnswered = false;
        
        inputs.forEach(input => {
            if (input.type === 'radio') {
                if (input.checked) questionAnswered = true;
            } else if (input.type === 'checkbox') {
                if (input.checked) questionAnswered = true;
            } else {
                if (input.value.trim() !== '') questionAnswered = true;
            }
        });
        
        if (!questionAnswered) {
            allAnswered = false;
            question.classList.add('border-danger');
        } else {
            question.classList.remove('border-danger');
        }
    });
    
    return allAnswered;
}

/**
 * Debounce function
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Initialize on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAssessmentForm);
} else {
    initAssessmentForm();
}

// Clear draft on successful submission
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('assessmentForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const assessmentId = form.getAttribute('action').match(/\/(\d+)\/complete/)?.[1];
            if (assessmentId && validateAssessmentForm()) {
                clearDraft(assessmentId);
            } else if (!validateAssessmentForm()) {
                e.preventDefault();
                alert('Please answer all questions before submitting.');
            }
        });
    }
});
