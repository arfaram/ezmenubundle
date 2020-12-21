const path = require('path');

module.exports = (Encore) => {
    Encore.addEntry('ezplatform-menu-js', [
        path.resolve(__dirname, '../public/js/menu.js')
    ]);
};