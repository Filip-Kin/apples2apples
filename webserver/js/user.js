var ip = ""

function connect() {
  var id = document.getElementById("id").value
  var name = document.getElementById("name").value
  document.getElementById("connErr").innerHTML = ""
  if (name !== "") {
    $.get("http://a2a.filipkin.com/getIp.php?id=" + id, function(data) {
      ip = data;
      if (ip == "0") {
        document.getElementById("connErr").innerHTML += "Game ID does not exist"
      } else {
        var ws = new WebSocket("wss://" + ip + ":8080")
        ws.onopen = function(event) {
          ws.send('{"evt": "client join", "info": {"name": "' + name.replace('"', '\"') + '"}}')
        }
        ws.onerror = function(event) {
          document.getElementById("connErr").innerHTML += "Failed to connect (make sure your ip is correct) - "
          console.log("Connection error")
        }
        ws.onclose = function(event) {
          document.getElementById("connErr").innerHTML += "Disconnected"
          console.log("Connection closed")
        }
        ws.onmessage = function(event) {
          var msg = JSON.parse(event.data)
          var keys = Object.keys(msg)
          console.log(JSON.stringify(keys))
          console.debug(JSON.stringify(msg))
          if (keys.includes("resp")) {
            if (msg.resp == "username in use") {
              document.getElementById("connErr").innerHTML = "Username in use - "
              ws.close()
            } else if (typeof msg.resp == "object" && msg.resp.hasOwnProperty("name")) {
              document.getElementById("join").style.display = "none";
              document.getElementById("waiting").style.display = "block";
            }
          } else if (keys.includes("evt")) {
            if (msg.evt == "startGame") {
              document.getElementById("waiting").innerHTML = "<tr><td><h2>Lets begin</h2></td></tr>"
              for (i = 0; i < msg.cards.length; i++) {
                var obj = msg.cards[i]
                var table = document.getElementById("deck-"+i)
                var rows = table.rows
                console.log("processing " + i + ": " + JSON.stringify(obj))
                rows[0].cells[0].innerHTML = obj.noun
                rows[1].cells[0].innerHTML = obj.synonyms[0]
                rows[2].cells[0].innerHTML = obj.synonyms[1]
                rows[3].cells[0].innerHTML = obj.synonyms[2]
                rows[4].cells[0].innerHTML = obj.definition
                rows[5].cells[0].innerHTML = obj.example
                var btn = rows[6].cells[0].childNodes[0]
                btn.onClick = "playCard('"+obj.noun+"')"
                btn.classList.remove("disabled")
                btn.innerHTML = "Submit"
              }
              setTimeout(function() {
                document.getElementById("waiting").style.display = "none";
                document.getElementById("game").style.display = "block";
              }, 2000)
            }
          }
        }
      }
    });
  } else {
    var errorElm = document.getElementById("connErr").innerHTML = "Please enter a username"
  }

}
