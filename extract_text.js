const fs = require('fs');
const cheerio = require('cheerio');
const html = fs.readFileSync('scraped_site/localhost/uvee/index.html', 'utf-8');
const $ = cheerio.load(html);
$('script, style, noscript').remove();
const text = $('body').text().replace(/\s+/g, ' ').trim();
console.log(text.substring(0, 3000));
