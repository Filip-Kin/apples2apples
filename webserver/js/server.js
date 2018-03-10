var ws = new WebSocket("ws://<?php echo $ip; ?>:8080")
ws.onopen = function(event) {
  ws.send('{"evt": "server join", "info": {"id": "<?php echo $id; ?>"}}')
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
    } else if (msg.evt == "min limit reached") {
      document.getElementById("startGameBtn").style.display = "block";
    }
  }
}
function startGame() {
  ws.send('{"cmd": "startGame"}')
  document.body.innerHTML = "<h1>Todo: Make the game</h1>"
}
