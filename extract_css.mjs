import fs from 'fs';
import path from 'path';

const cssPath = path.join(process.cwd(), 'src/App.css');
const appCss = fs.readFileSync(cssPath, 'utf8');
const lines = appCss.split('\n');

const getLine = (str) => lines.findIndex(l => l.includes(str));

const hpStart = getLine('PUBLIC HOME PAGE STYLES (.ph-*)') - 1;
const subStart = getLine('SUBSCRIPTION PAGE STYLES (.ps-*)') - 1;
const catStart = getLine('CATEGORY PAGE STYLES (.pc-*)') - 1;
const searchStart = getLine('.search-page {');
const recentStart = getLine('/* Recent Page Styles */');

// Extract the chunks
const hpChunk = lines.slice(hpStart, subStart).join('\n');
const subChunk = lines.slice(subStart, catStart).join('\n');
const catChunk = lines.slice(catStart, searchStart).join('\n');
const searchChunk = lines.slice(searchStart, recentStart).join('\n');
const recentChunk = lines.slice(recentStart, getLine('/* --- Mobile Responsive Base Layout --- */') - 1).join('\n');

// Write to files
fs.writeFileSync(path.join(process.cwd(), 'src/pags/homepage.css'), hpChunk);
fs.writeFileSync(path.join(process.cwd(), 'src/pags/subscription.css'), subChunk);
fs.writeFileSync(path.join(process.cwd(), 'src/pags/categorypage.css'), catChunk);
fs.writeFileSync(path.join(process.cwd(), 'src/pags/searchpage.css'), searchChunk);
fs.writeFileSync(path.join(process.cwd(), 'src/pags/recentpage.css'), recentChunk);

// Now remove them from App.css
// Keep 0 to hpStart, and everything from getLine('/* --- Mobile Responsive Base Layout --- */') - 1 onwards
const newAppCss = [
  ...lines.slice(0, hpStart),
  ...lines.slice(getLine('/* --- Mobile Responsive Base Layout --- */') - 1)
].join('\n');

fs.writeFileSync(cssPath, newAppCss);

console.log('Extraction complete!');
