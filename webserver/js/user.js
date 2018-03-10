var ip = ""
function connect() {
  var id = document.getElementById("id").value
  var name = document.getElementById("name").value
  document.getElementById("connErr").innerHTML = ""
  if (name !== "") {
    $.get("http://a2a.filipkin.com/getIp.php?id="+id, function(data) {
      ip = data;
      if (ip == "0") {
        document.getElementById("connErr").innerHTML += "Game ID does not exist"
      } else {
        var ws = new WebSocket("ws://"+ip+":8080")
        ws.onopen = function (event) {
          ws.send('{"evt": "client join", "info": {"name": "'+name.replace('"', '\"')+'"}}')
        }
        ws.onerror = function (event) {
          document.getElementById("connErr").innerHTML += "Failed to connect (make sure your ip is correct) - "
          console.log("Connection error")
        }
        ws.onclose = function (event) {
          document.getElementById("connErr").innerHTML += "Disconnected"
          console.log("Connection closed")
        }
        ws.onmessage = function (event) {
          console.log(event.data)
          var msg = JSON.parse(event.data)
          var keys = Object.keys(msg)
          if (keys.includes("resp")) {
            if (msg.resp == "username in use") {
              document.getElementById("connErr").innerHTML = "Username in use - "
              ws.close()
            } else if (typeof msg.resp == "object" && msg.resp.hasOwnProperty("name")) {
              document.getElementById("join").style.display = "none";
              document.getElementById("waiting").style.display = "block";
            }
          } else if (keys.includes("evt")) {
            if (msg.evt == "start") {
              document.getElementById("waiting").innerHTML = "<tr><td><h2>Lets begin</h2></td></tr>"
              /*setTimeout(function() = {
                document.getElementById("waiting").style.display = "none";
                document.getElementById("game").style.display = "block";
              }, 2000)*/
            }
          }
        }
      }
    });
  } else {
    var errorElm = document.getElementById("connErr").innerHTML = "Please enter a username"
  }
}
