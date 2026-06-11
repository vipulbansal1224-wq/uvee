const scrape = require('website-scraper');

const options = {
  urls: ['http://localhost/uvee/'],
  directory: './scraped_site',
  recursive: true,
  maxDepth: 3,
  filenameGenerator: 'bySiteStructure',
  urlFilter: function(url) {
    // Only scrape URLs that belong to http://localhost/uvee/
    return url.indexOf('http://localhost/uvee') === 0;
  }
};

scrape(options).then((result) => {
  console.log("Scraping completed successfully.");
}).catch((err) => {
  console.error("An error occurred:", err);
});
