function acceptCookieNotice() {
    setCookiePreference('accepted');
}

function rejectCookieNotice() {
    setCookiePreference('rejected');
}

function setCookiePreference(preference) {
    document.cookie = "nierto_cube_cookie_notice_" + preference + "=1; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/; SameSite=Strict";
    document.getElementById('cookie-notice-overlay').style.display = 'none';
    logCookiePreference(preference);
}

function logCookiePreference(preference) {
    const data = new FormData();
    data.append('action', 'nierto_cube_log_cookie_preference');
    data.append('nonce', niertoCubeData.nonce);
    data.append('preference', preference);

    fetch(niertoCubeData.ajaxurl, {
        method: 'POST',
        credentials: 'same-origin',
        body: data
    });
}

function clearLocalData() {
    if (confirm("Are you sure you want to clear all local data? This will log you out and may affect your offline experience.")) {
        // Clear cookies
        document.cookie.split(";").forEach(function (c) {
            document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/");
        });

        // Clear local storage
        localStorage.clear();
        sessionStorage.clear();

        // Clear IndexedDB
        indexedDB.databases().then((dbs) => {
            dbs.forEach((db) => {
                indexedDB.deleteDatabase(db.name);
            });
        });

        // Reload the page
        location.reload();
    }
}