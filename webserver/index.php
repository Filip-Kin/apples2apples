<?php include("/var/www/analytics.php"); //Just some analytics stuff ?>
<html>
  <head>
    <link href="https://fonts.googleapis.com/css?family=Saira+Extra+Condensed:100,200,300,400,500,600,700,800,900" rel="stylesheet">
    <style>
      h1,
      h2,
      h3,
      h4,
      h5,
      h6 {
        font-family: 'Saira Extra Condensed', serif;
      }

      h1 {
        color: #0C9700;
        font-size: 70px;
        margin: 0px;
      }

      h2 {
        margin: 0px;
      }

      #startGameBtn {
        transition: opacity linear 1s;
      }

      .btn, .btn:link, .btn:visited {
        -webkit-transition: all 1s ease-out;
        -moz-transition: all 1s ease-out;
        -ms-transition: all 1s ease-out;
        -o-transition: all 1s ease-out;
        transition: all 1s ease-out;
        -webkit-border-radius: 9;
        -moz-border-radius: 9;
        border-radius: 9px;
        font-family: Arial;
        color: #ffffff;
        background: #495057;
        font-size: 20px;
        padding: 10px 20px 10px 20px;
        text-decoration: none;
        width: 100%;
      }

      .btn:hover {
        -webkit-transition: all 1s ease-out;
        -moz-transition: all 1s ease-out;
        -ms-transition: all 1s ease-out;
        -o-transition: all 1s ease-out;
        transition: all 1s ease-out;
        background: #0C9700;
        color: white;
        text-decoration: none;
      }
      table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        -webkit-transition: all 1s ease-out;
        -moz-transition: all 1s ease-out;
        -ms-transition: all 1s ease-out;
        -o-transition: all 1s ease-out;
        transition: all 1s ease-out;
      }
      .center {
        position: absolute;
        top: 50%;
        left: 50%;
        transform:translateX(-50%) translateY(-50%);
      }
      td{
          border: 0px solid #dddddd;
          text-align: center;
      	  padding: 15px;
      }
      tr {
        color: #ffffff;
      }
      .banding:nth-child(even) {
          color: #ffffff;
      }
      th {
          background-color: #0C9700 !important;
          color: #FFFFFF !important;
          border: 1px solid #dddddd;
          text-align: center;
          padding: 2px;
      }
      body {
        background: #C40000
      }
      input {
      	background: #0C9700;
      	border: 0px;
      	color: #ffffff;
      	font-size: 20px;
      	font-family: 'Saira Extra Condensed', serif;
      	padding: 5px;
      }
      ::-webkit-input-placeholder { /* WebKit, Blink, Edge */
      	color: #ffffff;
      }
      :-moz-placeholder { /* Mozilla Firefox 4 to 18 */
         color: #ffffff;
         opacity:  1;
      }
      ::-moz-placeholder { /* Mozilla Firefox 19+ */
         color: #ffffff;
         opacity:  1;
      }
      :-ms-input-placeholder { /* Internet Explorer 10-11 */
         color: #ffffff;
      }
      ::-ms-input-placeholder { /* Microsoft Edge */
         color: #ffffff;
      }

      ::placeholder { /* Most modern browsers support this now. */
         color: #ffffff;
      }

      #loader {
        position: relative;
        top: 50%;
        left: 50%;
        margin-top: 2.7em;
        margin-left: -2.7em;
        width: 5.4em;
        height: 5.4em;
        background-color: transparent;
      }

      #hill {
        position: absolute;
        width: 7.1em;
        height: 7.1em;
        top: 1.7em;
        left: 1.7em;
        background-color: transparent;
        border-left: .25em solid #ffffff;
        transform: rotate(45deg);
      }

      #hill:after {
        content: '';
        position: absolute;
        width: 7.1em;
        height: 7.1em;
        left: 0;
        background-color: transparent;
      }

      #box {
        position: absolute;
        left: 0;
        bottom: -.1em;
        width: 1em;
        height: 1em;
        background-color: transparent;
        border: .25em solid #ffffff;
        border-radius: 15%;
        transform: translate(0, -1em) rotate(-45deg);
        animation: push 2.5s cubic-bezier(.79, 0, .47, .97) infinite;
      }

      @keyframes push {
        0% {
          transform: translate(0, -1em) rotate(-45deg);
        }
        5% {
          transform: translate(0, -1em) rotate(-50deg);
        }
        20% {
          transform: translate(1em, -2em) rotate(47deg);
        }
        25% {
          transform: translate(1em, -2em) rotate(45deg);
        }
        30% {
          transform: translate(1em, -2em) rotate(40deg);
        }
        45% {
          transform: translate(2em, -3em) rotate(137deg);
        }
        50% {
          transform: translate(2em, -3em) rotate(135deg);
        }
        55% {
          transform: translate(2em, -3em) rotate(130deg);
        }
        70% {
          transform: translate(3em, -4em) rotate(217deg);
        }
        75% {
          transform: translate(3em, -4em) rotate(220deg);
        }
        100% {
          transform: translate(0, -1em) rotate(-225deg);
        }
      }
      a.disabled {
         pointer-events: none;
         cursor: default;
         color: #a2a0a0 !important;
         background-color: #2E3439 !important;
      }
    </style>
    <title>Apples2Apples</title>
  </head>
  <body align="center">
    <table align="center" id="join" class="center">
      <tr><td colspan=2><h1>Apples 2 Apples</h1></td></tr>
      <tr><td><input type="text" id="serverip" value="192.168.1."></td><td><input type="text" id="name" placeholder="Username"></td></tr>
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
    <script>
      var ip = ""
      function connect() {
        ip = document.getElementById("serverip").value
        var name = document.getElementById("name").value
        document.getElementById("connErr").innerHTML = ""
        if (name !== "") {
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
        } else {
          var errorElm = document.getElementById("connErr").innerHTML = "Please enter a username"
        }
      }
    </script>
  </body>
</html>
