/**
 * Custom JavaScript for Online Notes Sharing System
 */

$(document).ready(function() {
    
    // Scroll to Top Button
    var scrollToTopBtn = $('#scrollToTop');
    
    $(window).scroll(function() {
        if ($(window).scrollTop() > 300) {
            scrollToTopBtn.fadeIn();
        } else {
            scrollToTopBtn.fadeOut();
        }
    });
    
    scrollToTopBtn.click(function() {
        $('html, body').animate({
            scrollTop: 0
        }, 600);
    });
    
    // Form Validation
    $('#signupForm, #loginForm, #addNotesForm, #editNotesForm, #profileForm, #changePasswordForm').on('submit', function(e) {
        var form = $(this);
        var isValid = true;
        
        // Remove previous error classes
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();
        
        // Validate required fields
        form.find('input[required], textarea[required]').each(function() {
            if (!$(this).val().trim()) {
                $(this).addClass('is-invalid');
                isValid = false;
            }
        });
        
        // Validate email fields
        form.find('input[type="email"]').each(function() {
            var email = $(this).val();
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email && !emailRegex.test(email)) {
                $(this).addClass('is-invalid');
                isValid = false;
            }
        });
        
        // Validate password match in change password form
        if (form.attr('id') === 'changePasswordForm') {
            var newPassword = $('#new_password').val();
            var confirmPassword = $('#confirm_password').val();
            
            if (newPassword !== confirmPassword) {
                $('#confirm_password').addClass('is-invalid');
                isValid = false;
            }
        }
        
        // Validate file sizes
        form.find('input[type="file"]').each(function() {
            var file = this.files[0];
            if (file) {
                var maxSize = 5 * 1024 * 1024; // 5MB
                if (file.size > maxSize) {
                    $(this).addClass('is-invalid');
                    isValid = false;
                    alert('File size exceeds 5MB limit: ' + file.name);
                }
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            return false;
        }
    });
    
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 5000);
    
    // Confirm delete actions
    $('a[href*="delete"]').on('click', function(e) {
        if (!confirm('Are you sure you want to delete this item?')) {
            e.preventDefault();
            return false;
        }
    });
    
    // File input change handler
    $('input[type="file"]').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        if (fileName) {
            $(this).next('.file-name').remove();
            $(this).after('<small class="file-name text-success d-block mt-1"><i class="fas fa-check"></i> ' + fileName + '</small>');
        }
    });
    
    // Mobile menu toggle
    $('.navbar-toggler').on('click', function() {
        $(this).toggleClass('active');
    });
    
    // Smooth scroll for anchor links
    $('a[href^="#"]').on('click', function(e) {
        var target = $(this.getAttribute('href'));
        if (target.length) {
            e.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 70
            }, 1000);
        }
    });
    
    // Character counter for textareas
    $('textarea[maxlength]').on('input', function() {
        var maxLength = $(this).attr('maxlength');
        var currentLength = $(this).val().length;
        var counter = $(this).siblings('.char-counter');
        
        if (counter.length === 0) {
            $(this).after('<small class="char-counter text-muted"></small>');
            counter = $(this).siblings('.char-counter');
        }
        
        counter.text(currentLength + ' / ' + maxLength + ' characters');
        
        if (currentLength > maxLength * 0.9) {
            counter.addClass('text-warning');
        } else {
            counter.removeClass('text-warning');
        }
    });
    
    // Password strength indicator (for change password form)
    $('#new_password').on('input', function() {
        var password = $(this).val();
        var strength = 0;
        
        if (password.length >= 6) strength++;
        if (password.length >= 8) strength++;
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^a-zA-Z0-9]/.test(password)) strength++;
        
        var strengthText = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
        var strengthClass = ['danger', 'warning', 'info', 'primary', 'success'];
        
        var indicator = $(this).siblings('.password-strength');
        if (indicator.length === 0) {
            $(this).after('<small class="password-strength d-block mt-1"></small>');
            indicator = $(this).siblings('.password-strength');
        }
        
        if (password.length > 0) {
            var level = Math.min(strength - 1, 4);
            indicator.text('Password Strength: ' + strengthText[level]);
            indicator.removeClass('text-danger text-warning text-info text-primary text-success');
            indicator.addClass('text-' + strengthClass[level]);
        } else {
            indicator.text('');
        }
    });
    
    // Prevent form resubmission on refresh
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
    
});

