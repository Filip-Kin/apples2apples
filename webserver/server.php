<?php
$ip = $_GET["ip"];
$id = $_GET["id"];
if (!isset($id) or $id == "") {
  if (!isset($ip) or $ip == "") {
    $id = "No ip specified in url!";
  } else {
    $conn = new mysqli('filipkin.com', 'a2a', 'password', 'a2a');
    $idisunique = false;
    while (!$idisunique) {
      $id = dechex(rand(0, 1048575));
      $sql = "SELECT * FROM `ips` WHERE `code` = '$id'";
      $result = $conn->query($sql);
      if ($result->num_rows == 0) {
        $idisunique = true;
        $sql = "INSERT INTO `ips` (`code`, `ip`) VALUES ('$id', '$ip')";
        $result = $conn->query($sql);
      }
    }
  }
} else {
  if (!isset($ip) or $ip == "") {
    $id = "No ip specified in url!";
  } else {
    $conn = new mysqli('filipkin.com', 'a2a', 'password', 'a2a');
    $sql = "SELECT * FROM `ips` WHERE `code` = '$id'";
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {
      $id = $id;
    } else {
      $id = "ID does not exist";
    }
  }
}

?>
<html>
<head>
  <title>Apples2Apples Server</title>
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
      color: #c40000;
      font-size: 70px;
      margin: 0px;
    }

    h2 {
      font-size: 32px
    }

    h3 {
      font-size: 24px
    }
    body {
      background: #0C9700;
    }
    .btn,
    .btn:link,
    .btn:visited {
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
    }

    .btn:hover {
      -webkit-transition: all 1s ease-out;
      -moz-transition: all 1s ease-out;
      -ms-transition: all 1s ease-out;
      -o-transition: all 1s ease-out;
      transition: all 1s ease-out;
      background: #c40000;
      color: white;
      text-decoration: none;
    }

    table {
      margin-top: 1em;
      font-family: arial, sans-serif;
      border-collapse: collapse;
    }
    td {
      text-align: center;
      padding: 2px;
    }
    td.border {
      border: 1px solid #dddddd;
      text-align: center;
      padding: 2px;
    }

    tr {
      color: #ffffff;
    }

    tr.background {background-color: #0C9700;}

    tr.banded:nth-child(even) {background-color: #c40000;}

    th {
      color: #FFFFFF !important;
      text-align: center;
      padding: 2px;
    }

    th.border {
      border: 1px solid #dddddd;
    }

    th.background {
      background-color: #0C9700 !important;
    }
    .center {
      position: absolute;
      top: 50%;
      left: 50%;
      transform:translateX(-50%) translateY(-50%);
    }
  </style>
</head>

<body align="center">
  <table id="join" class="center">
    <tr><td><h1><?php echo $id; ?></h1></td></tr>
    <tr><td><h2>Join from a2a.filipkin.com</h2></td></tr>
    <tr><td><a href="#" class="btn" onClick="closeServer();">Close server</a></td></tr>
    <tr><td><table id="playertable" align="center"><tr><th colspan=2><h3 style="color: white">Player list<h3></th></tr></table><br></td></tr>
    <tr><td id="startGameBtn" style="display:none;"><a href="#" class="btn" onClick="startGame();">Start Game</a></td></tr>
  </table>
  <script>
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
  </script>
</body>
</html>
