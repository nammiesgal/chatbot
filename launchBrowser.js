/*
const chromeLauncher = require('chrome-launcher');

var url = process.argv[2];

chromeLauncher.launch({
  startingUrl: url
}).then(chrome => {
  console.log(`Chrome debugging port running on ${chrome.port}`);
});

*/

(function(){
    var exec = require('child_process').exec;
    const { URL, URLSearchParams } = require('url');
    
    const url = new URL('https://codex.dialoggroup.biz/voice?search=JAVA');

  //  url.searchParams.set('q', 'stuff_to_search_for');

    var params = new URLSearchParams(url.search);
    
    exec(`start chrome ${url}`, function(err){
        if(err){
            console.log(err);
        }else{
            console.log('success');
            console.log(params);
        }
    });
})();

