<!-- Loader component that automatically hides when page loads -->
<div id="page-loader" class="fixed inset-0 z-50 flex items-center justify-center bg-white bg-opacity-90 dark:bg-gray-900 dark:bg-opacity-90">
    <div class="flex flex-col items-center">
        <img src="{{ asset('assets/MTFC_LOGO.png') }}" alt="ActiveGym Logo" class="h-16 mb-4">
        <svg class="animate-spin h-8 w-8 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>
</div>

<script>
    // Adjust loader background based on current theme
    document.addEventListener('DOMContentLoaded', function() {
        const htmlElement = document.documentElement;
        const loader = document.getElementById('page-loader');
        
        // Check if page is in dark mode
        if (htmlElement.classList.contains('dark') || 
            document.body.classList.contains('dark') || 
            document.body.classList.contains('bg-gray-900') ||
            document.body.classList.contains('dark:bg-gray-900')) {
            loader.classList.add('dark-mode');
        }
    });

    // Hide loader when page is fully loaded
    window.addEventListener('load', function() {
        const loader = document.getElementById('page-loader');
        if (loader) {
            // Add fade out animation
            loader.style.transition = 'opacity 0.5s ease';
            loader.style.opacity = '0';
            
            // Remove loader from DOM after animation completes
            setTimeout(function() {
                loader.style.display = 'none';
            }, 500);
        }
    });
</script> 