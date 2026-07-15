const fs = require('fs');

// Fix App.css
let appCss = fs.readFileSync('src/App.css', 'utf-8');
appCss = appCss.replace(/\.ph-las-5-section\s*\{[\s\S]*?\}/, `.ph-las-5-section {\n    background: #111827;\n    color: #fff;\n    padding: 24px 16px;\n    margin: 24px 24px;\n    border-radius: 8px;\n  }\n  @media (min-width: 1024px) {\n    .ph-las-5-section {\n      max-width: calc(1400px - 48px);\n      margin: 48px auto;\n      padding: 32px 48px;\n    }\n  }`);
appCss = appCss.replace(/\.ph-newsletter-section\s*\{[\s\S]*?\}/, `.ph-newsletter-section {\n    background: #5a7a94;\n    color: #fff;\n    padding: 32px 24px;\n    text-align: center;\n    border-radius: 8px;\n    margin: 32px 24px;\n  }\n  @media (min-width: 1024px) {\n    .ph-newsletter-section {\n      max-width: calc(1400px - 48px);\n      margin: 48px auto;\n      padding: 48px;\n    }\n  }`);
fs.writeFileSync('src/App.css', appCss);

// Fix homepage.css
let homeCss = fs.readFileSync('src/pags/homepage.css', 'utf-8');
homeCss = homeCss.replace(/rgb\(195, 47, 39\)/g, 'var(--card-border-color, rgb(195, 47, 39))');
homeCss = homeCss.replace(/rgba\(195, 47, 39, 0\.7\)/g, 'var(--card-border-color, rgb(195, 47, 39))');
homeCss = homeCss.replace(/rgba\(195, 47, 39, 0\.1\)/g, 'rgba(0, 0, 0, 0.08)');
homeCss = homeCss.replace(/rgba\(195, 47, 39, 0\.2\)/g, 'rgba(0, 0, 0, 0.1)');
fs.writeFileSync('src/pags/homepage.css', homeCss);
