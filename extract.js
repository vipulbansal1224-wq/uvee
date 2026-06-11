const fs = require('fs');
const path = require('path');
const cheerio = require('cheerio');

const productDir = path.join(__dirname, 'scraped_site', 'localhost', 'uvee', 'product');

if (!fs.existsSync(productDir)) {
  console.log("No scraped product directory found.");
  process.exit(1);
}

const productFolders = fs.readdirSync(productDir);
const products = [];
const categoriesSet = new Set(['All']);

for (const folder of productFolders) {
  const indexFile = path.join(productDir, folder, 'index.html');
  if (fs.existsSync(indexFile)) {
    const html = fs.readFileSync(indexFile, 'utf-8');
    const $ = cheerio.load(html);
    
    // Extract name
    const name = $('h1.product_title').text().trim();
    
    // Extract price (get the last price if there are multiple like del/ins)
    let priceText = $('.woocommerce-Price-amount bdi').last().text().trim();
    // remove symbol
    const priceMatch = priceText.replace(/,/g, '').match(/\d+(\.\d+)?/);
    const price = priceMatch ? parseFloat(priceMatch[0]) : 0;
    
    // Extract image
    // Sometimes it's in a meta tag, or the first gallery image
    let image = $('.woocommerce-product-gallery__image a').attr('href') || 
                $('.woocommerce-product-gallery__wrapper img').attr('src');
                
    // Convert scraped localhost URL to our public URL
    if (image && image.includes('wp-content')) {
        image = '/' + image.split('wp-content/')[1];
        image = '/wp-content/' + image;
    } else {
        image = '/wp-content/uploads/woocommerce-placeholder.png'; // fallback
    }

    // Extract category
    const category = $('.posted_in a').first().text().trim() || 'Uncategorized';
    categoriesSet.add(category);
    
    // Description (short)
    const description = $('.woocommerce-product-details__short-description p').first().text().trim();

    if (name) {
        products.push({
            id: folder,
            name,
            price,
            category,
            image,
            description
        });
    }
  }
}

const jsContent = `export const products = ${JSON.stringify(products, null, 2)};

export const categories = ${JSON.stringify(Array.from(categoriesSet), null, 2)};
`;

fs.writeFileSync(path.join(__dirname, 'data', 'products.js'), jsContent);
console.log(`Extracted ${products.length} products to data/products.js`);
