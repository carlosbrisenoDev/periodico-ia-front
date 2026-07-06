const fs = require('fs');
const content = fs.readFileSync('src/App.css', 'utf-8');
const lines = content.split('\n');

const blocks = [];
for (let i = 0; i < lines.length; i++) {
  if (lines[i].startsWith('/* ') || lines[i].startsWith('/* =') || lines[i].startsWith('/* -')) {
    blocks.push(`${i + 1}: ${lines[i]}`);
  }
}

fs.writeFileSync('comments.txt', blocks.join('\n'));
