const WebSocket = require('ws')
var Sentencer = require('sentencer')
var http = require('http')
var os = require('os')
var dns = require('dns')
var opn = require('opn')
var colors = require('colors')

console.log("Welcome! Starting server".bold.green)

console.log("Attempting to open websocket".bold)
const wss = new WebSocket.Server({ port: 8080 })

game = {"players": []}
conns = {}

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
      }
    }
  })
})

function synonyms(word) {
  console.log("Getting synonyms for ".bold+word.bold.green)
  return new Promise(function(resolve,reject) {
    var options = {
      host: 'api.datamuse.com',
      port: 80,
      path: '/words?ml='+word
    }
    var data = ""
    http.get(options, function(resp){
    resp.on('data', function(chunk){
      data += chunk
    })
    resp.on('end', () => {
      json = JSON.parse(data)
      resolve([json[0].word, json[1].word, json[2].word])
    })
    }).on("error", function(e){
      console.log("Got error: " + e.message)
    })
  })
}

// usage: synonyms(word).then(function(out){})

function randNoun() {
  console.log("Randomly generating noun".bold)
  return Sentencer.make("{{ noun }}")
}

function randAdj() {
  console.log("Randomly generating adjective".bold)
  return Sentencer.make("{{ adjective }}")
}

function test() {
  var noun = randNoun()
  var adj = randAdj()
  synonyms(noun).then(function(out){console.log(noun + ": " + out.join(", "))})
  synonyms(adj).then(function(out){console.log(adj + ": " + out.join(", "))})
  setTimeout(test, 3000)
}
//test()

var myIp = "Failed to detect local ip."

dns.lookup(os.hostname(), function (err, add, fam) {
  console.log("Detecting ip".bold)
  myIp = add
  console.log("Ip is ".bold+myIp.bold.green)
  opn("http://a2a.filipkin.com/server.php?ip="+myIp)
})
