<!-- Profile Enhancement Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Performance metrics animation with improved hover effects
    const metrics = document.querySelectorAll('.performance-metric');
    metrics.forEach(metric => {
        metric.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
            this.style.transition = 'all 0.2s ease-in-out';
            this.style.boxShadow = '0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1)';
        });
        metric.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.boxShadow = '';
        });
    });

    // Enhanced card hover effects for all interactive elements
    const interactiveCards = document.querySelectorAll('[class*="hover:scale-"]');
    interactiveCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.02)';
            this.style.transition = 'all 0.2s ease-in-out';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });

    // Smooth scrolling for internal links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Auto-animate numbers on page load
    const numberElements = document.querySelectorAll('[class*="font-bold"]');
    numberElements.forEach(element => {
        const text = element.textContent;
        const number = parseInt(text.replace(/[^\d]/g, ''));
        if (number && number > 0 && number < 1000) {
            animateNumber(element, 0, number, 1500);
        }
    });
});

// Number animation function
function animateNumber(element, start, end, duration) {
    const startTime = performance.now();
    const originalText = element.textContent;
    
    function updateNumber(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        const current = Math.floor(start + (end - start) * progress);
        element.textContent = originalText.replace(/\d+/, current);
        
        if (progress < 1) {
            requestAnimationFrame(updateNumber);
        }
    }
    
    requestAnimationFrame(updateNumber);
}

// Generate Report Function with enhanced feedback
function generateReport() {
    // Show loading state
    const button = event.target;
    const originalText = button.textContent;
    button.textContent = 'Generating...';
    button.disabled = true;
    button.style.opacity = '0.7';
    
    // Simulate API call for now
    setTimeout(() => {
        const reportData = {
            success: true,
            data: {
                period: 'December 2024',
                projects_completed: Math.floor(Math.random() * 20) + 10,
                performance_score: (Math.random() * 2 + 3).toFixed(1),
                total_hours: Math.floor(Math.random() * 100) + 150,
                client_satisfaction: Math.floor(Math.random() * 10) + 90
            }
        };
        
        if (reportData.success) {
            // Create modern notification
            showNotification('success', 'Report Generated Successfully!', 
                `Period: ${reportData.data.period}\n` +
                `Projects: ${reportData.data.projects_completed}\n` +
                `Performance: ${reportData.data.performance_score}/5.0\n` +
                `Hours: ${reportData.data.total_hours}h\n` +
                `Satisfaction: ${reportData.data.client_satisfaction}%`
            );
        }
        
        // Reset button
        button.textContent = originalText;
        button.disabled = false;
        button.style.opacity = '1';
    }, 2000);
}

// Export Data Function
function exportData() {
    const button = event.target;
    const originalText = button.textContent;
    button.textContent = 'Exporting...';
    button.disabled = true;
    button.style.opacity = '0.7';
    
    // Simulate export process
    setTimeout(() => {
        showNotification('success', 'Data Exported!', 'Your profile data has been exported successfully.');
        
        // Reset button
        button.textContent = originalText;
        button.disabled = false;
        button.style.opacity = '1';
    }, 1500);
}

// Enhanced notification system
function showNotification(type, title, message) {
    // Remove existing notifications
    const existing = document.querySelector('.notification-popup');
    if (existing) existing.remove();
    
    const notification = document.createElement('div');
    notification.className = 'notification-popup fixed top-4 right-4 z-50 max-w-sm w-full';
    notification.style.fontFamily = "'Poppins', sans-serif";
    
    const bgColor = type === 'success' ? 'bg-green-50' : 'bg-red-50';
    const borderColor = type === 'success' ? 'border-green-200' : 'border-red-200';
    const iconColor = type === 'success' ? 'text-green-600' : 'text-red-600';
    const textColor = type === 'success' ? 'text-green-800' : 'text-red-800';
    
    notification.innerHTML = `
        <div class="${bgColor} ${borderColor} border rounded-lg p-4 shadow-lg transform transition-all duration-300 translate-x-full">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 ${iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${type === 'success' ? 
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>' :
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                        }
                    </svg>
                </div>
                <div class="ml-3 w-0 flex-1">
                    <p class="text-sm font-medium ${textColor}" style="font-weight: 600;">${title}</p>
                    <p class="mt-1 text-sm ${textColor.replace('800', '700')}" style="font-weight: 400; white-space: pre-line;">${message}</p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button onclick="this.closest('.notification-popup').remove()" class="rounded-md inline-flex ${textColor} hover:${textColor.replace('800', '900')} focus:outline-none">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.querySelector('div').classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.querySelector('div').classList.add('translate-x-full');
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

// Show Password Modal with enhanced styling
function showPasswordModal() {
    showNotification('info', 'Feature Coming Soon', 'Password change functionality will be available in the next update.');
}

// Enhanced status indicator animation
setInterval(function() {
    const statusIndicators = document.querySelectorAll('.bg-green-400, .bg-blue-400, .bg-yellow-400');
    statusIndicators.forEach(indicator => {
        indicator.style.transition = 'all 0.5s ease-in-out';
        indicator.classList.toggle('animate-pulse');
    });
}, 3000);

// Add loading states to buttons
document.querySelectorAll('button').forEach(button => {
    if (!button.onclick && !button.getAttribute('onclick')) {
        button.addEventListener('click', function() {
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
        });
    }
});

// Smooth page transitions
window.addEventListener('beforeunload', function() {
    document.body.style.opacity = '0.8';
    document.body.style.transition = 'opacity 0.3s ease-out';
});
</script>

<style>
/* Additional styles for enhanced interactions */
.notification-popup {
    animation: slideInRight 0.3s ease-out;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Enhance button press feedback */
button:active {
    transform: scale(0.95) !important;
    transition: transform 0.1s ease-in-out !important;
}

/* Smooth hover transitions for all interactive elements */
[class*="hover:"]:not(button) {
    transition: all 0.2s ease-in-out;
}

/* Loading animation for buttons */
.loading {
    position: relative;
    pointer-events: none;
}

.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 16px;
    height: 16px;
    margin: -8px 0 0 -8px;
    border: 2px solid #ffffff;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}
</style>
