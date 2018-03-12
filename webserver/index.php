<?php include("/var/www/analytics.php"); //Just some analytics stuff ?>
<?php
$user = "";
$gameid = "";
if (isset($_GET["id"]) && $_GET["id"] != "") {
  $user = dechex(rand(0, 1048575));
  $gameid = $_GET["id"];
}
?>
<html>
  <head>
    <link href="https://fonts.googleapis.com/css?family=Saira+Extra+Condensed:100,200,300,400,500,600,700,800,900" rel="stylesheet">
    <link href="css/global.css" rel="stylesheet">
    <link href="css/user.css" rel="stylesheet">
    <title>Apples2Apples</title>
  </head>
  <body align="center">
    <table align="center" id="join" class="center">
      <tr><td colspan=2><h1>Apples 2 Apples</h1></td></tr>
      <tr><td><input type="text" id="id" placeholder="Game ID" value="<?php echo $gameid; ?>"></td><td><input type="text" id="name" placeholder="Username" value="<?php echo $user; ?>"></td></tr>
      <tr><td colspan=2><a class="btn" href="#" onClick="connect()">Join Server</a></td></tr>
      <tr><td colspan=2><a class="btn" href="https://github.com/Filip9696/apples2apples/releases">Download Server</a></td></tr>
      <tr><td colspan=2><h3 id="connErr"></h3></td></tr>
    </table>
    <table align="center" id="waiting" class="center" style="display: none;">
      <tr><td><h1>You're in</h1></td></tr>
      <tr><td><h2>Now lets wait for the slow pokes</h2></td></tr>
      <tr><td><div id="loader"><div id="box"></div><div id="hill"></div></div><br><br><br></td></tr>
      <tr><td><a class="btn" href="#" onClick="leave()">Leave Server</a></td></tr>
    </table>
    <table align="center" class="center" id="game" style="display: none;">
      <tr><td><h1 id="gameEvent">Drawing cards</h1></td></tr>
      <tr><td><h2 id="gameEventInfo">Please Wait</h2></td></tr>
      <tr><td>
        <table align="center">
          <tr><td colspan=5><h3>Your Deck</h3></td></tr>
          <tr>
            <td>
              <table id="deck-0" align="center">
                <tr><th>Example card</th></tr>
                <tr><td>synonyms</td></tr>
                <tr><td>synonyms</td></tr>
                <tr><td>synonyms</td></tr>
                <tr><td>definition</td></tr>
                <tr><td><a href="#" onClick="playCard('Cardname')" class="btn">Remove</a></td></tr>
              </table>
            </td>
            <td>
              <table id="deck-1" align="center">
                <tr><th>Example card</th></tr>
                <tr><td>synonyms</td></tr>
                <tr><td>synonyms</td></tr>
                <tr><td>synonyms</td></tr>
                <tr><td>definition</td></tr>
                <tr><td><a href="#" onClick="playCard('Cardname')" class="btn disabled">Submit</a></td></tr>
              </table>
            </td>
            <td>
              <table id="deck-2" align="center">
                <tr><th>Example card</th></tr>
                <tr><td>synonyms</td></tr>
                <tr><td>synonyms</td></tr>
                <tr><td>synonyms</td></tr>
                <tr><td>definition</td></tr>
                <tr><td><a href="#" onClick="playCard('Cardname')" class="btn disabled">Submit</a></td></tr>
              </table>
            </td>
            <td>
              <table id="deck-3" align="center">
                <tr><th>Example card</th></tr>
                <tr><td>synonyms</td></tr>
                <tr><td>synonyms</td></tr>
                <tr><td>synonyms</td></tr>
                <tr><td>definition</td></tr>
                <tr><td><a href="#" onClick="playCard('Cardname')" class="btn disabled">Submit</a></td></tr>
              </table>
            </td>
            <td>
              <table id="deck-4" align="center">
                <tr><th>Example card</th></tr>
                <tr><td>synonyms</td></tr>
                <tr><td>synonyms</td></tr>
                <tr><td>synonyms</td></tr>
                <tr><td>definition</td></tr>
                <tr><td><a href="#" onClick="playCard('Cardname')" class="btn disabled">Submit</a></td></tr>
              </table>
            </td>
          </tr>
        </table>
      </td></tr>
      <tr><td><a class="btn" href="#" onClick="leave()">Leave Server</a></td></tr>
    </table>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script>
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
      <?php if ($user != "") { echo "connect()"; } ?>
    </script>
  </body>
</html>
