var WebSocket = require('ws')
var getHostName = require('os').hostname
var dns = require('dns').lookup
var opn = require('opn')
var colors = require('colors')
var rw = require('./randomWords')

var debugOpen = true;

console.log("Welcome! Starting server".bold.green)

console.log("Attempting to open websocket".bold)
const wss = new WebSocket.Server({ port: 8080 })

game = {"players": []}
conns = []

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
            rw.randAdj().then(function(adj) {
              conn.send('{"evt": "startGame", "card": '+JSON.stringify(adj)+'}')
            })
            first = false
          } else {
            Promise.all([rw.randNoun(), rw.randNoun(), rw.randNoun(), rw.randNoun(), rw.randNoun()]).then(function(nouns) {
              conn.send('{"evt":"startGame","cards":' + JSON.stringify(nouns) + '}')
            })
          }
        })
      }
    }
  })
})

var myIp = "Failed to detect local ip."

dns(getHostName(), function (err, add, fam) {
  console.log("Detecting ip".bold)
  myIp = add
  console.log("Ip is ".bold+myIp.bold.green)
  opn("http://a2a.filipkin.com/server.php?ip="+myIp)
})
