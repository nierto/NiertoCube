// config.js
document.addEventListener('DOMContentLoaded', function () {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', niertoCubeData.ajaxurl, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function () {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success && response.data) {
                // Execute the received JavaScript
                eval(response.data);

                // Make setupCubeButtons globally available
                window.setupCubeButtons = setupCubeButtons;

                // Dispatch a custom event to signal that setupCubeButtons is ready
                document.dispatchEvent(new Event('setupCubeButtonsReady'));

                // Call setupCubeButtons
                setupCubeButtons();
            }
        } else {
            console.error('Error fetching cube configuration:', xhr.statusText);
        }
    };

    xhr.onerror = function () {
        console.error('Network error while fetching cube configuration');
    };

    const data = 'action=nierto_cube_get_config&nonce=' + niertoCubeData.nonce;
    xhr.send(data);
});