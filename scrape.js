const scrape = require('website-scraper');

const options = {
  urls: ['http://localhost/uvee/'],
  directory: './public/wp-static',
  recursive: false,
  maxDepth: 1,
  prettifyUrls: true,
  filenameGenerator: 'bySiteStructure',
};

scrape(options).then((result) => {
  console.log("Entire website successfully downloaded");
}).catch((err) => {
  console.error("An error occurred", err);
});
