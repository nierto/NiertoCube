// utils.js
function getThemeUrl() {
    return '/wp-content/themes/' + window.themeData.themeName + '/';
}

// Export the function if using ES6 modules
export { getThemeUrl };