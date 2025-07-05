document.addEventListener("DOMContentLoaded", function() {
    const tocContainers = document.querySelectorAll('.pd-toc-container');

    tocContainers.forEach(container => {
        // 1) Read container data attributes
        const headingLevels = container.dataset.levels ? container.dataset.levels.split(',') : [];

        const desktopState = container.dataset.desktopState || 'expanded';
        const tabletState  = container.dataset.tabletState  || 'collapsed';
        const mobileState  = container.dataset.mobileState  || 'collapsed';
        const marker       = container.dataset.marker || 'bullets'; // 'bullets'|'numbers'|'hyphen'|'none'

        // Fallback
        const fallbackType      = container.dataset.fallbackType || 'none'; // 'none'|'text'|'custom_url'
        const fallbackText      = container.dataset.fallbackText || 'No headings found.';
        const fallbackUrl       = container.dataset.fallbackUrl   || '';
        const fallbackUrlLabel  = container.dataset.fallbackUrlLabel || 'Check out this link';

        const tocList   = container.querySelector('.pd-toc-list');
        const toggleBtn = container.querySelector('.pd-toc-toggle');
        let iconElem    = null;
        if (toggleBtn) {
            iconElem = toggleBtn.querySelector('.pd-toc-icon i, .pd-toc-icon svg');
        }

        // 2) Determine screen size => default expand/collapse
        let isExpanded = false;
        const w = window.innerWidth;
        if (w < 768) {
            // Mobile
            isExpanded = (mobileState === 'expanded');
        } else if (w < 1025) {
            // Tablet
            isExpanded = (tabletState === 'expanded');
        } else {
            // Desktop
            isExpanded = (desktopState === 'expanded');
        }

        // 3) If no heading levels selected, do nothing
        if (!headingLevels.length) return;

        // 4) Query headings from the DOM
        const selector = headingLevels.join(', ');
        const foundHeadings = document.querySelectorAll(selector);

        let headingCount = 0;
        foundHeadings.forEach(heading => {
            const text = heading.innerText.trim();
            if (!text) return;

            // If no ID, generate one
            if (!heading.id) {
                headingCount++;
                heading.id = `pd-toc-heading-${headingCount}`;
            }

            // Create list item
            const li = document.createElement('li');
            const a  = document.createElement('a');

            // If marker === 'hyphen', prefix the text with "- "
            let displayText = text;
            if (marker === 'hyphen') {
                displayText = '- ' + text;
            }
            // If 'bullets' or 'numbers' => just normal text
            // If 'none' => no prefix

            a.href        = '#' + heading.id;
            a.textContent = displayText;

            // Smooth scroll or offset
            a.addEventListener('click', function(e) {
                e.preventDefault();
                smoothScrollToHeading(heading);
            });

            li.appendChild(a);
            tocList.appendChild(li);
        });

        // 5) If headingCount == 0 => fallback
        if (headingCount === 0 && tocList.children.length === 0) {
            if (fallbackType === 'none') {
                // Do nothing (empty list)
            } else if (fallbackType === 'text') {
                const li = document.createElement('li');
                li.textContent = fallbackText;
                tocList.appendChild(li);
            } else if (fallbackType === 'custom_url') {
                const li = document.createElement('li');
                const a  = document.createElement('a');
                a.href   = fallbackUrl;
                a.textContent = fallbackUrlLabel;
                li.appendChild(a);
                tocList.appendChild(li);
            }
        }

        // 6) Apply expand/collapse
        tocList.style.display = isExpanded ? 'block' : 'none';
        updateIconRotation(iconElem, isExpanded);

        // 7) Toggle on click
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                isExpanded = !isExpanded;
                tocList.style.display = isExpanded ? 'block' : 'none';
                updateIconRotation(iconElem, isExpanded);
            });
        }
    });

    function updateIconRotation(icon, expanded) {
        if (!icon) return;
        icon.style.transition = 'transform 0.2s ease';
        icon.style.transform  = expanded ? 'rotate(180deg)' : 'rotate(0deg)';
    }

    function smoothScrollToHeading(headingElement) {
        const offset = 100; // e.g., 100px for a sticky header
        const headingPosition = headingElement.getBoundingClientRect().top + window.pageYOffset - offset;

        window.scrollTo({
            top: headingPosition,
            behavior: 'smooth'
        });
    }
});

// TOC Widget Functionality
document.addEventListener("DOMContentLoaded", function() {
    const tocContainers = document.querySelectorAll('.pd-toc-container');

    tocContainers.forEach(container => {
        // 1) Read container data attributes
        const headingLevels = container.dataset.levels ? container.dataset.levels.split(',') : [];

        const desktopState = container.dataset.desktopState || 'expanded';
        const tabletState  = container.dataset.tabletState  || 'collapsed';
        const mobileState  = container.dataset.mobileState  || 'collapsed';
        const marker       = container.dataset.marker || 'bullets'; // 'bullets'|'numbers'|'hyphen'|'none'

        // Fallback
        const fallbackType      = container.dataset.fallbackType || 'none'; // 'none'|'text'|'custom_url'
        const fallbackText      = container.dataset.fallbackText || 'No headings found.';
        const fallbackUrl       = container.dataset.fallbackUrl   || '';
        const fallbackUrlLabel  = container.dataset.fallbackUrlLabel || 'Check out this link';

        const tocList   = container.querySelector('.pd-toc-list');
        const toggleBtn = container.querySelector('.pd-toc-toggle');
        let iconElem    = null;
        if (toggleBtn) {
            iconElem = toggleBtn.querySelector('.pd-toc-icon i, .pd-toc-icon svg');
        }

        // 2) Determine screen size => default expand/collapse
        let isExpanded = false;
        const w = window.innerWidth;
        if (w < 768) {
            // Mobile
            isExpanded = (mobileState === 'expanded');
        } else if (w < 1025) {
            // Tablet
            isExpanded = (tabletState === 'expanded');
        } else {
            // Desktop
            isExpanded = (desktopState === 'expanded');
        }

        // 3) If no heading levels selected, do nothing
        if (!headingLevels.length) return;

        // 4) Query headings from the DOM
        const selector = headingLevels.join(', ');
        const foundHeadings = document.querySelectorAll(selector);

        let headingCount = 0;
        foundHeadings.forEach(heading => {
            const text = heading.innerText.trim();
            if (!text) return;

            // If no ID, generate one
            if (!heading.id) {
                headingCount++;
                heading.id = `pd-toc-heading-${headingCount}`;
            }

            // Create list item
            const li = document.createElement('li');
            const a  = document.createElement('a');

            // If marker === 'hyphen', prefix the text with "- "
            let displayText = text;
            if (marker === 'hyphen') {
                displayText = '- ' + text;
            }
            // If 'bullets' or 'numbers' => just normal text
            // If 'none' => no prefix

            a.href        = '#' + heading.id;
            a.textContent = displayText;

            // Smooth scroll or offset
            a.addEventListener('click', function(e) {
                e.preventDefault();
                smoothScrollToHeading(heading);
            });

            li.appendChild(a);
            tocList.appendChild(li);
        });

        // 5) If headingCount == 0 => fallback
        if (headingCount === 0 && tocList.children.length === 0) {
            if (fallbackType === 'none') {
                // Do nothing (empty list)
            } else if (fallbackType === 'text') {
                const li = document.createElement('li');
                li.textContent = fallbackText;
                tocList.appendChild(li);
            } else if (fallbackType === 'custom_url') {
                const li = document.createElement('li');
                const a  = document.createElement('a');
                a.href   = fallbackUrl;
                a.textContent = fallbackUrlLabel;
                li.appendChild(a);
                tocList.appendChild(li);
            }
        }

        // 6) Apply expand/collapse
        tocList.style.display = isExpanded ? 'block' : 'none';
        updateIconRotation(iconElem, isExpanded);

        // 7) Toggle on click
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                isExpanded = !isExpanded;
                tocList.style.display = isExpanded ? 'block' : 'none';
                updateIconRotation(iconElem, isExpanded);
            });
        }
    });

    function updateIconRotation(icon, expanded) {
        if (!icon) return;
        icon.style.transition = 'transform 0.2s ease';
        icon.style.transform  = expanded ? 'rotate(180deg)' : 'rotate(0deg)';
    }

    function smoothScrollToHeading(headingElement) {
        const offset = 100; // e.g., 100px for a sticky header
        const headingPosition = headingElement.getBoundingClientRect().top + window.pageYOffset - offset;

        window.scrollTo({
            top: headingPosition,
            behavior: 'smooth'
        });
    }
});

// Product Information Widget Functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize after a short delay to ensure DOM is fully loaded
    setTimeout(function() {
        initializeProductInfoWidgets();
    }, 100);
});

function initializeProductInfoWidgets() {
    // Use more specific selector with data attribute
    const expandToggleButtons = document.querySelectorAll('button[data-product-widget="toggle"]');

    console.log('Product Info: Found toggle buttons:', expandToggleButtons.length);

    expandToggleButtons.forEach((button, index) => {
        // Add unique identifier
        button.setAttribute('data-toggle-id', 'pd-toggle-' + index);

        // Remove any existing listeners
        button.removeEventListener('click', handleProductToggleClick);

        // Add new listener
        button.addEventListener('click', handleProductToggleClick);

        console.log('Product Info: Event listener attached to button', index);
    });
}

function handleProductToggleClick(event) {
    event.preventDefault();
    event.stopPropagation();

    const button = event.currentTarget;
    const toggleId = button.getAttribute('data-toggle-id');

    console.log('Product Info: Toggle clicked', toggleId);

    try {
        const container = button.closest('.pd-product-info-container');
        if (!container) {
            console.error('Product Info: Container not found');
            return;
        }

        const expandableContent = container.querySelector('.pd-product-info-expandable');
        if (!expandableContent) {
            console.error('Product Info: Expandable content not found');
            return;
        }

        const toggleText = button.querySelector('.pd-toggle-text');
        const toggleIcon = button.querySelector('.pd-toggle-icon');

        // Check current state
        const isCurrentlyVisible = expandableContent.style.display === 'block';

        if (isCurrentlyVisible) {
            // Collapse
            expandableContent.style.display = 'none';
            if (toggleText) toggleText.textContent = 'View Details';
            if (toggleIcon) toggleIcon.style.transform = 'rotate(0deg)';
            button.classList.remove('expanded');
            console.log('Product Info: Content collapsed');
        } else {
            // Expand
            expandableContent.style.display = 'block';
            if (toggleText) toggleText.textContent = 'Hide Details';
            if (toggleIcon) toggleIcon.style.transform = 'rotate(180deg)';
            button.classList.add('expanded');
            console.log('Product Info: Content expanded');

            // Smooth scroll to ensure content is visible
            setTimeout(() => {
                expandableContent.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            }, 100);
        }
    } catch (error) {
        console.error('Product Info: Error in toggle handler', error);
    }
}
