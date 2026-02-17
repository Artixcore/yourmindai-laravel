/**
 * Global AJAX infrastructure for loading-free form submissions.
 * Uses jQuery only. Progressive enhancement: forms work without JS.
 */
(function() {
    'use strict';

    if (typeof jQuery === 'undefined') return;

    var $ = jQuery;

    // CSRF setup for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || '',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    });

    /**
     * Show an alert in the AJAX alerts container.
     * @param {string} type - success, error, warning, info
     * @param {string} message
     */
    function showAjaxAlert(type, message) {
        var container = $('#ajax-alerts');
        if (!container.length) {
            container = $('.content-wrapper, .p-4, main .container-fluid, .container-fluid').first();
            if (container.length) {
                container.prepend('<div id="ajax-alerts"></div>');
                container = $('#ajax-alerts');
            } else {
                $('main, .content-wrapper, body').first().prepend('<div id="ajax-alerts" class="p-4"></div>');
                container = $('#ajax-alerts');
            }
        }

        var alertClass = 'alert-' + type;
        var iconMap = {
            success: 'bi-check-circle-fill',
            error: 'bi-exclamation-triangle-fill',
            warning: 'bi-exclamation-circle-fill',
            info: 'bi-info-circle-fill'
        };
        var icon = iconMap[type] || 'bi-info-circle-fill';

        var html = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
            '<i class="bi ' + icon + ' me-2"></i>' +
            message +
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';

        container.append(html);

        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            container.find('.alert').last().alert('close');
        }, 5000);
    }

    /**
     * Clear validation errors from form inputs.
     */
    function clearValidationErrors(form) {
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();
    }

    /**
     * Show validation errors under inputs and as summary.
     */
    function showValidationErrors(form, errors) {
        clearValidationErrors(form);

        if (typeof errors === 'object') {
            $.each(errors, function(field, messages) {
                var input = form.find('[name="' + field + '"]');
                if (!input.length) input = form.find('[name="' + field + '[]"]');
                if (input.length) {
                    input.addClass('is-invalid');
                    var msg = Array.isArray(messages) ? messages[0] : messages;
                    input.closest('.mb-4, .mb-3, .form-group, .col-md-6, .col-12, div').first()
                        .append('<div class="invalid-feedback d-block">' + msg + '</div>');
                }
            });
            var firstMsg = typeof errors === 'object' ? (Object.values(errors)[0] || '') : '';
            var summary = Array.isArray(firstMsg) ? firstMsg[0] : firstMsg;
            if (summary) showAjaxAlert('error', 'Please fix the errors: ' + summary);
        }
    }

    /**
     * Set button loading state.
     */
    function setButtonLoading(btn, loading) {
        if (!btn || !btn.length) return;
        if (loading) {
            btn.data('original-html', btn.html());
            btn.prop('disabled', true);
            btn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' + (btn.data('loading-text') || 'Saving...'));
        } else {
            btn.prop('disabled', false);
            btn.html(btn.data('original-html') || 'Submit');
        }
    }

    /**
     * Global AJAX error handler.
     */
    $(document).ajaxError(function(event, jqXHR, settings, thrownError) {
        if (settings.skipGlobalErrorHandler) return;

        var msg = 'Something went wrong. Please try again.';
        if (jqXHR.status === 0) msg = 'Network error. Please check your connection.';
        else if (jqXHR.status === 419) msg = 'Session expired. Please refresh the page.';
        else if (jqXHR.status === 500) msg = 'Server error. Please try again later.';

        showAjaxAlert('error', msg);
    });

    /**
     * Intercept .ajax-form submissions.
     */
    $(document).on('submit', '.ajax-form', function(e) {
        var form = $(this);
        if (form.data('ajax-submitting')) return;
        form.data('ajax-submitting', true);

        e.preventDefault();

        var btn = form.find('button[type="submit"], input[type="submit"]').first();
        var action = form.attr('action');
        var method = (form.attr('method') || 'GET').toUpperCase();
        var data = form.serialize();

        if (method === 'GET') {
            $.get(action + (action.indexOf('?') >= 0 ? '&' : '?') + data)
                .done(function(resp) { handleAjaxSuccess(form, resp, btn); })
                .fail(function(jqXHR) { handleAjaxError(form, jqXHR, btn); })
                .always(function() { form.data('ajax-submitting', false); setButtonLoading(btn, false); });
        } else {
            setButtonLoading(btn, true);
            clearValidationErrors(form);

            $.ajax({
                url: action,
                type: method,
                data: data,
                dataType: 'json'
            })
                .done(function(resp) { handleAjaxSuccess(form, resp, btn); })
                .fail(function(jqXHR) { handleAjaxError(form, jqXHR, btn); })
                .always(function() { form.data('ajax-submitting', false); setButtonLoading(btn, false); });
        }
    });

    function handleAjaxSuccess(form, resp, btn) {
        if (resp && resp.success !== false) {
            showAjaxAlert('success', resp.message || 'Saved successfully.');
            if (resp.html && resp.target) {
                var target = $(resp.target);
                if (target.length) {
                    if (resp.replace === true) {
                        target.replaceWith(resp.html);
                    } else if (resp.prepend === true) {
                        target.prepend(resp.html);
                        if (resp.hideOnAdd) $(resp.hideOnAdd).hide();
                        if (resp.showTarget) target.show();
                    } else {
                        target.html(resp.html);
                    }
                }
            }
            if (resp.redirect) {
                window.location.href = resp.redirect;
                return;
            }
            // Close modal if inside one
            var modal = form.closest('.modal');
            if (modal.length && typeof modal.modal === 'function') {
                modal.modal('hide');
            }
            // Optional callback
            if (typeof form.data('ajax-success') === 'function') {
                form.data('ajax-success')(resp);
            }
        } else {
            showAjaxAlert('error', (resp && resp.message) || 'An error occurred.');
        }
    }

    function handleAjaxError(form, jqXHR, btn) {
        if (jqXHR.status === 422 && jqXHR.responseJSON && jqXHR.responseJSON.errors) {
            showValidationErrors(form, jqXHR.responseJSON.errors);
            showAjaxAlert('error', 'Please fix the validation errors.');
        } else if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
            showAjaxAlert('error', jqXHR.responseJSON.message);
        }
        // Global handler also fires for generic errors
    }

    // Expose for custom use
    window.AppAjax = {
        showAlert: showAjaxAlert,
        clearValidationErrors: clearValidationErrors,
        showValidationErrors: showValidationErrors,
        setButtonLoading: setButtonLoading
    };
})();
