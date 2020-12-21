const path = require('path');

module.exports = (Encore) => {
    Encore.addEntry('ezplatform-menu-css', [
        path.resolve(__dirname, '../public/css/menu.css')
    ]);
};