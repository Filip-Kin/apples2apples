var Sentencer = require('sentencer')
var gets = require("https").get
var get = require("http").get

function genRandNoun() {
	var noun = Sentencer.make("{{ noun }}")
	return noun
}

function genRandAdj() {
	var adjective = Sentencer.make("{{ adjective }}")
	return adjective
}

function synonyms(word) {
	return new Promise(function(resolve, reject) {
		var options = {
			host: 'api.datamuse.com',
			port: 80,
			path: '/words?ml=' + encodeURIComponent(word)
		}
		var data = ""
		get(options, function(resp) {
			resp.on('data', function(chunk) {
				data += chunk
			})
			resp.on('end', () => {
				json = JSON.parse(data)
				if(json.length >= 3) {
					resolve([json[0].word, json[1].word, json[2].word])
				} else if(json.length == 2) {
					resolve([json[0].word, json[1].word, "Unable to find 3 synonyms"])
				} else if(json.length == 1) {
					resolve([json[0].word, "Unable to find", "2 synonyms"])
				} else if(json.length <= 0) {
					resolve(["Unable to", "find any", "synonyms"])
				}
			})
		}).on("error", function(e) {
			console.log("Got error: " + e.message)
		})
	})
}

function definition(word, type) {
	return new Promise(function(resolve, reject) {
		var options = {
			host: 'owlbot.info',
			port: 443,
			path: '/api/v2/dictionary/' + encodeURIComponent(word) + '?format=json'
		}
		var data = ""
		gets(options, function(resp) {
			resp.on('data', function(chunk) {
				data += chunk
			})
			resp.on('end', () => {
				json = JSON.parse(data)
				var out = ["No definition found", "No example found"]
				json.forEach(function(obj) {
					if(obj.type == type) {
						out[0] = obj.definition
						if(obj.example != null) {
							out[1] = obj.example
						}
					}
				})
				resolve(out)
			})
		}).on("error", function(e) {
			console.log("Got error: " + e.message)
		})
	})
}

exports.randNoun = function() {
	return new Promise (function (resolve, reject) {
		var obj = { "noun": genRandNoun() }
		definition(obj.noun, "noun").then(function(out) {
			obj.definition = out[0]
			obj.example = out[1]
			synonyms(obj.noun).then(function(out) {
				obj.synonyms = out
				resolve(obj)
			})
		})
	})
}

exports.randAdj = function() {
	return new Promise(function(resolve, reject) {
		var obj = { "adjective": genRandAdj() }
		definition(obj.adjective, "adjective").then(function(out) {
			obj.definition = out[0]
			obj.example = out[1]
			synonyms(obj.adjective).then(function(out) {
				obj.synonyms = out
				resolve(obj)
			})
		})
	})
}

exports.test = function() {
	exports.randNoun().then(function(out) { console.log(JSON.stringify(out)) })
	exports.randAdj().then(function(out) { console.log(JSON.stringify(out)) })
}
