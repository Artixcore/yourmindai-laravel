/**
 * Notification polling and UI updates.
 * Polls unread count every 15 seconds; fetches full list when dropdown opens.
 * Uses jQuery + Bootstrap. No framework.
 */
(function() {
    'use strict';

    var POLL_INTERVAL = 15000; // 15 seconds
    var countUrl = '/notifications/unread-count';
    var listUrl = '/notifications/unread';
    var markReadUrl = '/notifications/';
    var pollTimer = null;

    function getCsrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function showErrorAlert(message) {
        var alert = $('<div class="alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 9999; max-width: 90%;" role="alert">' +
            (message || 'Something went wrong. Please try again.') +
            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
        $('body').append(alert);
        setTimeout(function() { alert.alert('close'); }, 5000);
    }

    function fetchUnreadCount(callback) {
        $.ajax({
            url: countUrl,
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            success: function(data) {
                if (data.success && typeof data.count === 'number') {
                    if (callback) callback(data.count);
                }
            },
            error: function(xhr) {
                if (xhr.status !== 401 && xhr.status !== 419) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Failed to fetch notifications';
                    showErrorAlert(msg);
                }
            }
        });
    }

    function updateBadge(count) {
        var badge = document.getElementById('notificationBadge');
        if (!badge) return;
        var current = parseInt(badge.getAttribute('data-count'), 10) || 0;
        if (count === current) return;
        badge.setAttribute('data-count', count);
        badge.textContent = count > 99 ? '99+' : count;
        if (count > 0) {
            badge.classList.remove('d-none');
        } else {
            badge.classList.add('d-none');
        }
    }

    function poll() {
        fetchUnreadCount(function(count) {
            updateBadge(count);
        });
    }

    function fetchAndRenderList() {
        var $list = $('.notification-list');
        if (!$list.length) return;
        $.ajax({
            url: listUrl,
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            success: function(data) {
                if (data.success && data.notifications) {
                    var html = '';
                    if (data.notifications.length === 0) {
                        html = '<li class="px-3 py-2 small text-muted">No new notifications</li>';
                    } else {
                        data.notifications.forEach(function(n) {
                            var msg = (n.message || '').substring(0, 60);
                            if (n.message && n.message.length > 60) msg += '...';
                            html += '<li><a href="' + (n.url || '#') + '" class="dropdown-item py-2 notification-item" data-id="' + (n.id || '') + '">' +
                                '<div class="fw-semibold small text-dark">' + (n.title || 'Notification') + '</div>' +
                                '<div class="small text-muted">' + msg + '</div>' +
                                '<div class="small text-muted mt-1">' + (n.created_at || '') + '</div></a></li>';
                        });
                    }
                    var $items = $list.find('li').slice(2);
                    $items.remove();
                    $list.find('hr.dropdown-divider').after(html);
                    updateBadge(data.notifications.length);
                }
            },
            error: function() {
                showErrorAlert('Failed to load notifications');
            }
        });
    }

    function markAsRead(id, callback) {
        $.ajax({
            url: markReadUrl + id + '/read',
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            data: { _token: getCsrfToken() },
            success: function() {
                if (callback) callback();
            },
            error: function() {
                showErrorAlert('Failed to mark as read');
            }
        });
    }

    function init() {
        if (typeof jQuery === 'undefined') return;
        var $ = jQuery;

        poll();
        pollTimer = setInterval(poll, POLL_INTERVAL);

        $(document).on('shown.bs.dropdown', '#notificationDropdown', function() {
            fetchAndRenderList();
        });

        $(document).on('click', '.notification-item', function(e) {
            var id = $(this).data('id');
            if (id) {
                markAsRead(id, function() {
                    poll();
                });
            }
        });

        $(document).on('submit', 'form[action*="notifications/read-all"]', function(e) {
            e.preventDefault();
            var $form = $(this);
            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                success: function() {
                    updateBadge(0);
                    var $list = $form.closest('.dropdown').find('.notification-list');
                    $list.find('li').slice(2).remove();
                    $list.append('<li class="px-3 py-2 small text-muted">No new notifications</li>');
                    $form.parent().hide();
                },
                error: function() {
                    showErrorAlert('Failed to mark all as read');
                }
            });
            return false;
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
