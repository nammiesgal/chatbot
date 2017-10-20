const express = require('express');
var bodyParser = require('body-parser');
var request = require("request");
var path = require("path");

var googleFunction = require('./google');

const app = express();

app.use(bodyParser.urlencoded({ extended: false }))
app.use(bodyParser.json())

app.get('/', function(req,res){
	res.sendFile(path.join(__dirname, './index.html'));
});

app.post('/google', function (req, res) {
    request.post("https://apaia-chatbot-webhook.herokuapp.com/", function(err, request, response){
        req.body = response;
        //This googleFunction takes the JSON response coming from https://apaia-chatbot-webhook.herokuapp.com/ and send it to the google function for processing.
        googleFunction(response);
    });
    res.end(JSON.stringify(req.body, null, 2));
});

app.listen(3000, function () {
  console.log('app listening on port 3000!');
});