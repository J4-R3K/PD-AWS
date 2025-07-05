document.addEventListener('DOMContentLoaded', function() {
    const letters = document.querySelectorAll('.index-letter');
    const indexContainer = document.querySelector('.horizontal-scroll-index');
    const audio = new Audio('/wp-content/plugins/horizontal-scroll-index/click-sound.mp3');

    // Function to handle the click on each letter
    function handleLetterClick() {
        let letter = this.textContent;

        if (letter === 'ALL') {
            window.location.href = 'https://projectdesign.io/tag/all/';
        } else if (letter === 'Lessons Learned') {
            window.location.href = 'https://projectdesign.io/category/lessons-learned/';
        } else {
            window.location.href = `https://projectdesign.io/tag/${letter.toLowerCase()}/`;
        }

        // Play click sound
        audio.play();
    }

    // Attach the click event listener to each letter
    letters.forEach(letter => {
        letter.addEventListener('click', handleLetterClick);
    });

    // Test sound button event listener
    document.getElementById('test-sound').addEventListener('click', function() {
    if (audio.paused) {
        audio.play();
    } else {
        audio.pause();
    }
    });

    // Scroll event listener for haptic feedback
    let lastScrollLeft = 0;
    indexContainer.addEventListener('scroll', function() {
        let currentScrollLeft = indexContainer.scrollLeft;
        if (Math.abs(currentScrollLeft - lastScrollLeft) > 10) {
            if ('vibrate' in navigator) {
                navigator.vibrate(15); // Short vibration on horizontal scroll
            }
            lastScrollLeft = currentScrollLeft;
        }
    });
});
