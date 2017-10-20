module.exports = function(data){
    var exec = require('child_process').exec;
    const { URL, URLSearchParams } = require('url');

    //Parse the json data coming back from the https://apaia-chatbot-webhook.herokuapp.com/ endpoint.
    var jsonContent = JSON.parse(data);

    //Get the postback address from the returned JSON data
    var codexUrl = jsonContent.messages[0].buttons[0].postback;
    
    const url = new URL(`${codexUrl}`);

    //Set the search params. These are the params from your querystring.
    url.searchParams.set('search', jsonContent.speech);

    var params = new URLSearchParams(url.search);
    
    exec(`start chrome ${url}`, function(err){
        if(err){
            console.log(err);
        }else{
           console.log('success');
           console.log(params);
       }
    });

};