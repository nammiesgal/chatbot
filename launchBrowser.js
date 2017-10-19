
const chromeLauncher = require('chrome-launcher');

var url = process.argv[2];

chromeLauncher.launch({
  startingUrl: url
}).then(chrome => {
  console.log(`Chrome debugging port running on ${chrome.port}`);
});


