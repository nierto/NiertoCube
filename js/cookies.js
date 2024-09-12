function acceptCookieNotice() {
    document.cookie = "nierto_cube_cookie_notice_accepted=1; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/";
    document.getElementById('cookie-notice').style.display = 'none';
}

function clearLocalData() {
    if (confirm("Are you sure you want to clear all local data? This will log you out and may affect your offline experience.")) {
        caches.keys().then(function (names) {
            for (let name of names)
                caches.delete(name);
        });
        localStorage.clear();
        sessionStorage.clear();
        location.reload();
    }
}