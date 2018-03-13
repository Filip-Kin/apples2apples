var WebSocket = require('ws')
var Sentencer = require('sentencer')
var http = require('http')
var os = require('os')
var dns = require('dns')
var opn = require('opn')
var colors = require('colors')
var rp = require('request-promise')

var debugOpen = true;

function randNoun() {
  var noun = Sentencer.make("{{ noun }}")
  console.log("Randomly generated noun ".bold+noun.bold.green)
  return noun
}

function randAdj() {
  var adjective = Sentencer.make("{{ adjective }}")
  console.log("Randomly generated adjective ".bold+adjective.bold.red)
  return adjective
}

console.log("Welcome! Starting server".bold.green)

console.log("Attempting to open websocket".bold)
const wss = new WebSocket.Server({ port: 8080 })

game = {"players": []}
conns = []

function synonyms(word) {
  return new Promise(function(resolve, reject) {
    var options = {
      host: 'api.datamuse.com',
      port: 80,
      path: '/words?ml=' + encodeURIComponent(word)
    }
    var data = ""
    http.get(options, function(resp) {
      resp.on('data', function(chunk) {
        data += chunk
      })
      resp.on('end', () => {
        json = JSON.parse(data)
        resolve([json[0].word, json[1].word, json[2].word])
      })
    }).on("error", function(e) {
      console.log("Got error: " + e.message)
    })
  })
}

// usage: synonyms(word).then(function(out){})

function definition(word) {
  return new Promise(function(resolve, reject) {
    var options = {
      host: 'owlbot.info',
      port: 80,
      path: '/api/v2/dictionary/' + encodeURIComponent(word) + '?format=json'
    }
    var data = ""
    http.get(options, function(resp) {
      resp.on('data', function(chunk) {
        data += chunk
      })
      resp.on('end', () => {
        //json = JSON.parse(data)
        //resolve([json[0].definition, json[0].example])
        resolve(data)
      })
    }).on("error", function(e) {
      console.log("Got error: " + e.message)
    })
  })
}

wss.on('connection', function connection(ws, req) {
  const ip = req.connection.remoteAddress
  ws.on('error', (err) => {
    console.log(err.toString().bold.red + " from " + ip)
    if(ws == conns[0]) {
      console.log("Server tab closed, reopening".bold.red + " To close server press Close server on server tab".bold.green)
      opn("http://a2a.filipkin.com/server.php?ip="+myIp+"&id="+game.id)
    } else {
      console.log("Client left".bold.red)
    }
  })
  ws.on('message', function incoming(message) {
    console.log('received: %s from '+ip, message)
    msg = JSON.parse(message)
    msg.keys = Object.keys(msg)
    if (msg.keys.includes("evt")) {
      if (msg.evt == "server join") {
        conns[0] = ws
        game.id = msg.info.id
        ws.send('{"resp": "approved"}')
        if (debugOpen) {
          opn("http://a2a.filipkin.com/?id="+game.id)
          opn("http://a2a.filipkin.com/?id="+game.id)
          opn("http://a2a.filipkin.com/?id="+game.id)
        }
      } else if (msg.evt == "client join") {
        var playerid = game.players.length + 1
        var playernames = game.players.map(a => a.name)
        console.log(playernames.toString())
        var playerobj = {"id": playerid, "name": msg.info.name}
        if (!playernames.includes(playerobj.name)) {
          ws.send('{"resp": '+JSON.stringify(playerobj)+'}')
          game.players.push(playerobj)
          conns[playerid] = ws
          conns[0].send('{"evt": "player join", "info": '+JSON.stringify(playerobj)+'}')
          if (playerid == 3) {
            conns[0].send('{"evt": "min limit reached"}')
          }
        } else {
          ws.send('{"resp": "username in use"}')
        }
      }
    } else if (msg.keys.includes("cmd")) {
      if (msg.cmd == "shutdown") {
        process.exit()
      } else if (msg.cmd == "startGame") {
        var first = true
        conns.forEach( function(conn) {
          if (first) {
            conn.send('{"evt": "startGame", "card": "'+randAdj()+'"}')
            first = false
          } else {
            var nouns = [{ "noun": randNoun() }, { "noun": randNoun()}, { "noun": randNoun()}, { "noun": randNoun()}, { "noun": randNoun()}]
            nouns.forEach( function(obj){
              console.log("Getting info for "+obj.noun)
              synonyms(obj.noun).then(function(out) { obj.synonyms = out })
              definition(obj.noun, "noun").then(function(out) { 
                obj.definition = out[0]
                obj.example = out[1]
              })
            })
            conn.send('{"evt": "startGame", "cards": '+JSON.stringify(nouns)+'}')
          }
        })
      }
    }
  })
})

var myIp = "Failed to detect local ip."

dns.lookup(os.hostname(), function (err, add, fam) {
  console.log("Detecting ip".bold)
  myIp = add
  console.log("Ip is ".bold+myIp.bold.green)
  opn("http://a2a.filipkin.com/server.php?ip="+myIp)
})
