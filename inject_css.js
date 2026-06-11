const fs = require('fs');
const path = require('path');

function injectCSS(dir) {
    const files = fs.readdirSync(dir);
    for (const file of files) {
        const fullPath = path.join(dir, file);
        if (fs.statSync(fullPath).isDirectory()) {
            injectCSS(fullPath);
        } else if (fullPath.endsWith('.html')) {
            let content = fs.readFileSync(fullPath, 'utf8');
            if (!content.includes('premium-polish.css')) {
                // Determine the correct relative path or use absolute path
                // Since this will be served by Next.js from public/, /premium-polish.css works perfectly
                content = content.replace('</head>', '<link rel="stylesheet" href="/premium-polish.css"></head>');
                fs.writeFileSync(fullPath, content, 'utf8');
                console.log(`Injected into ${fullPath}`);
            }
        }
    }
}

injectCSS('./public');
console.log('Done injecting premium CSS.');
