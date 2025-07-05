document.addEventListener('DOMContentLoaded', function() {
    // Listen for clicks on .pd-copy-button
    document.querySelectorAll('.pd-copy-button').forEach(function(button) {
        button.addEventListener('click', function() {
            // Find the highlight container
            const container = button.closest('.pd-highlight-container');
            if (!container) return;

            // Query only the content div, ignoring the button text
            const contentDiv = container.querySelector('.pd-highlight-content');
            if (!contentDiv) return;

            // Extract plain text from that content
            let plainText = contentDiv.innerText.trim();

            // Append the source URL at the end as a new line
            // Example: 
            // "User's text here...
            //
            // Source: https://example.com/current-page"
            plainText += '\n\nSource: ' + window.location.href;

            // Use modern Clipboard API if available
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(plainText).then(() => {
                    showCopiedMessage(button);
                });
            } else {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = plainText;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                showCopiedMessage(button);
            }
        });
    });

    function showCopiedMessage(button) {
        const copiedMsg = button.getAttribute('data-copied-message') || 'Copied!';
        // Temporarily change the button text
        const originalText = button.textContent;
        button.textContent = copiedMsg;

        // Restore after 2 seconds
        setTimeout(() => {
            button.textContent = originalText;
        }, 2000);
    }
});
