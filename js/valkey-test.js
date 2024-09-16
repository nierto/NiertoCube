document.addEventListener('DOMContentLoaded', function () {
    var testButton = document.querySelector('#customize-control-valkey_test_connection input');
    if (testButton) {
        testButton.addEventListener('click', function (e) {
            e.preventDefault();
            var xhr = new XMLHttpRequest();
            xhr.open('POST', valkeyTest.ajax_url, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function () {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        alert(response.data);
                    } else {
                        alert(response.data);
                    }
                } else {
                    alert('An error occurred during the connection test.');
                }
            };
            xhr.send('action=test_valkey_connection&nonce=' + valkeyTest.nonce);
        });
    }
});