if (getIp() == "") {} else { var ws = new WebSocket("wss://"+getIp()+":8080")}

var players = []

ws.onopen = function(event) {

  setTimeout(function() {
    ws.send('{"evt": "server join", "info": {"id": "' + getId() + '"}}')
  }, 200)

}

function closeServer() {

  ws.send('{"cmd": "shutdown"}')

  window.close()

}

ws.onmessage = function(event) {
  console.log(event.data)
  msg = JSON.parse(event.data)
  msg.keys = Object.keys(msg)
  if (msg.keys.includes("evt")) {
    if (msg.evt == "player join") {
      var playertable = document.getElementById("playertable")
      var row = playertable.insertRow(playertable.rows.length)
      row.insertCell(0).innerHTML = "<strong>" + msg.info.id + "</strong>"
      row.insertCell(1).innerHTML = msg.info.name
      players.push({ "name": msg.info.name, "id": msg.info.id, "wins": 0})
    } else if (msg.evt == "min limit reached") {
      document.getElementById("startGameBtn").style.display = "block";
    }
  }
}

function startGame() {
  ws.send('{"cmd": "startGame"}')
  document.getElementById("join").style.display = "none";
  document.getElementById("card").style.display = "block";
}

function indexOfMax(arr) {
  if(arr.length === 0) {
    return -1;
  }

  var max = arr[0];
  var maxIndex = 0;

  for(var i = 1; i < arr.length; i++) {
    if(arr[i] > max) {
      maxIndex = i;
      max = arr[i];
    }
  }

  return maxIndex;
}

function sortLeaderBoard() {
  var lb = document.getElementById("lb")
  lb.innerHTML = "<tr><th>Place</th><th>Name</th><th>Cards</th></tr>";
  var playerswins = [];
  players.forEach(function(player) {
    playerswins.push(player.wins);
  })
  var playeridorder = [];
  for(i=0; i < playerswins.length; i++) {
    var id = indexOfMax(playerswins);
    playeridorder.push(id);
    playerswins[id] = -1;
    var placing = playeridorder.indexOf(id)+1
    console.log(id+" "+placing+" "+JSON.stringify(playerswins))
    lb.innerHTML = lb.innerHTML + "<tr><td>"+placing+"</td><td>"+players[id].name+"</td><td>"+players[id].wins+"</td></tr>";
  }
}

