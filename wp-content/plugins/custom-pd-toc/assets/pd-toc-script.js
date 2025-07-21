/**
 * PD TOC + Product-Information Widget Script
 * ------------------------------------------
 *  • Builds the dynamic Table of Contents for `.pd-toc-container`.
 *  • Adds expand/collapse + smooth-scrolling.
 *  • Handles “View details / Hide details” toggle inside
 *    `.pd-product-info-container`.
 *
 * 2025-07-21 — duplicate block removed; syntax error fixed.
 */
(function () {
    'use strict';

    /* initialise once the DOM is ready */
    document.addEventListener('DOMContentLoaded', () => {
        initTOCWidgets();
        initProductInfoWidgets();
    });

    /* ---------------------------------------------------------------------
     *  TABLE OF CONTENTS  ( .pd-toc-container )
     * -------------------------------------------------------------------*/
    function initTOCWidgets() {
        const tocContainers = document.querySelectorAll('.pd-toc-container');

        tocContainers.forEach(container => {
            /* ▸ 1 — Data attributes --------------------------------------------------*/
            const headingLevels = container.dataset.levels
                ? container.dataset.levels.split(',')
                : [];

            const desktopState = container.dataset.desktopState || 'expanded';
            const tabletState  = container.dataset.tabletState  || 'collapsed';
            const mobileState  = container.dataset.mobileState  || 'collapsed';
            const marker       = container.dataset.marker       || 'bullets'; // bullets|numbers|hyphen|none

            /* Fallback */
            const fallbackType      = container.dataset.fallbackType     || 'none'; // none|text|custom_url
            const fallbackText      = container.dataset.fallbackText     || 'No headings found.';
            const fallbackUrl       = container.dataset.fallbackUrl      || '';
            const fallbackUrlLabel  = container.dataset.fallbackUrlLabel || 'Check out this link';

            const tocList   = container.querySelector('.pd-toc-list');
            const toggleBtn = container.querySelector('.pd-toc-toggle');
            const iconElem  = toggleBtn
                ? toggleBtn.querySelector('.pd-toc-icon i, .pd-toc-icon svg')
                : null;

            /* ▸ 2 — Determine default expanded/collapsed by viewport -----------------*/
            let isExpanded;
            const w = window.innerWidth;
            if (w < 768) {
                isExpanded = (mobileState === 'expanded');   // mobile
            } else if (w < 1025) {
                isExpanded = (tabletState === 'expanded');   // tablet
            } else {
                isExpanded = (desktopState === 'expanded');  // desktop
            }

            /* ▸ 3 — Bail if no heading levels selected -------------------------------*/
            if (!headingLevels.length) return;

            /* ▸ 4 — Query headings & build list --------------------------------------*/
            const selector      = headingLevels.join(', ');
            const foundHeadings = document.querySelectorAll(selector);

            let headingCount = 0;
            foundHeadings.forEach(heading => {
                const text = heading.innerText.trim();
                if (!text) return;

                // auto-ID if missing
                if (!heading.id) {
                    headingCount += 1;
                    heading.id = `pd-toc-heading-${headingCount}`;
                }

                // build list item
                const li = document.createElement('li');
                const a  = document.createElement('a');

                // marker style
                let displayText = text;
                if (marker === 'hyphen') displayText = '- ' + text;

                a.href        = `#${heading.id}`;
                a.textContent = displayText;

                a.addEventListener('click', e => {
                    e.preventDefault();
                    smoothScrollToHeading(heading);
                });

                li.appendChild(a);
                tocList.appendChild(li);
            });

            /* ▸ 5 — Fallback if no headings ------------------------------------------*/
            if (headingCount === 0 && tocList.children.length === 0) {
                const li = document.createElement('li');
                switch (fallbackType) {
                    case 'text':
                        li.textContent = fallbackText;
                        break;
                    case 'custom_url':
                        const a = document.createElement('a');
                        a.href        = fallbackUrl;
                        a.textContent = fallbackUrlLabel;
                        li.appendChild(a);
                        break;
                    case 'none':
                    default:
                        // leave empty
                        break;
                }
                tocList.appendChild(li);
            }

            /* ▸ 6 — Apply initial state ---------------------------------------------*/
            tocList.style.display = isExpanded ? 'block' : 'none';
            updateIconRotation(iconElem, isExpanded);

            /* ▸ 7 — Toggle handler ---------------------------------------------------*/
            if (toggleBtn) {
                toggleBtn.addEventListener('click', () => {
                    isExpanded = !isExpanded;
                    tocList.style.display = isExpanded ? 'block' : 'none';
                    updateIconRotation(iconElem, isExpanded);
                });
            }
        });

        /* helper: rotate chevron */
        function updateIconRotation(icon, expanded) {
            if (!icon) return;
            icon.style.transition = 'transform 0.2s ease';
            icon.style.transform  = expanded ? 'rotate(180deg)' : 'rotate(0deg)';
        }

        /* helper: smooth scroll with offset */
        function smoothScrollToHeading(headingElement) {
            const offset  = 100; // adjust for sticky header height
            const targetY = headingElement.getBoundingClientRect().top
                + window.pageYOffset - offset;

            window.scrollTo({ top: targetY, behavior: 'smooth' });
        }
    }

    /* ---------------------------------------------------------------------
     *  PRODUCT - INFO  ( .pd-product-info-container )
     * -------------------------------------------------------------------*/
    function initProductInfoWidgets() {
        // run a tick later so Elementor preview DOM is ready
        setTimeout(() => {
            document
                .querySelectorAll('button[data-product-widget="toggle"]')
                .forEach((btn, idx) => {
                    btn.setAttribute('data-toggle-id', `pd-toggle-${idx}`);
                    btn.removeEventListener('click', handleToggle); // clean slate
                    btn.addEventListener('click', handleToggle);
                });
        }, 100);
    }

    function handleToggle(e) {
        e.preventDefault();
        e.stopPropagation();

        const btn        = e.currentTarget;
        const container  = btn.closest('.pd-product-info-container');
        const expandable = container
            ? container.querySelector('.pd-product-info-expandable')
            : null;

        if (!expandable) return;

        const label = btn.querySelector('.pd-toggle-text');
        const icon  = btn.querySelector('.pd-toggle-icon');
        const open  = expandable.style.display === 'block';

        if (open) {
            // collapse
            expandable.style.display = 'none';
            if (label) label.textContent = 'View Details';
            if (icon)  icon.style.transform = 'rotate(0deg)';
            btn.classList.remove('expanded');
        } else {
            // expand
            expandable.style.display = 'block';
            if (label) label.textContent = 'Hide Details';
            if (icon)  icon.style.transform = 'rotate(180deg)';
            btn.classList.add('expanded');

            // keep it in view, nice UX
            setTimeout(() => {
                expandable.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }, 100);
        }
    }
})();
