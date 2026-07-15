const fs = require('fs');

// Fix globalsettings.tsx
let settingsContent = fs.readFileSync('src/pags/globalsettings.tsx', 'utf-8');

// Add state
settingsContent = settingsContent.replace(
  /const \[themeBorder, setThemeBorder\] = useState\("#e2e3e6"\);/,
  'const [themeBorder, setThemeBorder] = useState("#e2e3e6");\n  const [themeCardBorder, setThemeCardBorder] = useState("#c32f27");' // #c32f27 is rgb(195, 47, 39)
);

// Add fetch
settingsContent = settingsContent.replace(
  /setThemeBorder\(data\.themeColors\.border \|\| "#e2e3e6"\);/,
  'setThemeBorder(data.themeColors.border || "#e2e3e6");\n          setThemeCardBorder(data.themeColors.cardBorder || "#c32f27");'
);

// Add save
settingsContent = settingsContent.replace(
  /border: themeBorder/,
  'border: themeBorder,\n            cardBorder: themeCardBorder'
);

// Add UI
const uiChunk = `
                <div style={{ display: "flex", flexDirection: "column", gap: "8px" }}>
                  <label style={{ fontWeight: "bold", color: "var(--text-main)" }}>Bordes de Tarjetas</label>
                  <div style={{ display: "flex", gap: "8px", alignItems: "center" }}>
                    <input type="color" value={themeCardBorder} onChange={e => setThemeCardBorder(e.target.value)} style={{ width: "40px", height: "40px", padding: "0", border: "none", borderRadius: "4px", cursor: "pointer" }} />
                    <input type="text" value={themeCardBorder} onChange={e => setThemeCardBorder(e.target.value)} style={{ padding: "8px", borderRadius: "4px", border: "1px solid var(--border)", width: "100px", background: "transparent", color: "var(--text-main)" }} />
                  </div>
                </div>
              </div>
            </div>`;
settingsContent = settingsContent.replace(
  /<\/div>\s*<\/div>\s*<div style={{ display: "flex", justifyContent: "flex-end"/,
  uiChunk + '\n            <div style={{ display: "flex", justifyContent: "flex-end"'
);

fs.writeFileSync('src/pags/globalsettings.tsx', settingsContent);

// Fix homepage.tsx
let homeTsx = fs.readFileSync('src/pags/homepage.tsx', 'utf-8');
homeTsx = homeTsx.replace(
  /settings\?\.cardBorderColor \? \{ '--card-border-color': settings\.cardBorderColor \}/g,
  'settings?.themeColors?.cardBorder ? { \'--card-border-color\': settings.themeColors.cardBorder }'
);
fs.writeFileSync('src/pags/homepage.tsx', homeTsx);

