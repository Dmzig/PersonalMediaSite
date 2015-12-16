<html>
<head>
<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">
<title>Login</title>

<style>
    body {
        background-image: url(bg.jpg);
        background-size: cover;
    }
    #title{
        text-align: center;
    }
    .list-group{
        float: left;
    }
    .container{
		padding: 0px;
		margin: 0 auto;
		width: 260px;
	}
	#out{
		margin: 0 auto;
		text-align: center; 
	}
	</style>    
</head
<body>
<div class = "MenuBar">
</br>
</br>
</br>
<nav class="navbar navbar-inverse navbar-fixed-top">
  <div>
    <ul class="nav navbar-nav">
        <li>
            <a></a>
        </li>
        <li>
            <img src="WD.png" height="50px" width="80px">
        </li>
        <li>
            <a></a>
        </li>
        <li>
            <a href="HackLogin.php">Home</a>
        </li>
        <li>
            <a href="HackProject.html">Movies</a>
        </li>
        <li>
            <a href="TVshows.html">TV Shows</a>
        </li>
        <li>
            <a href="#">About Us</a>
        </li>
    </ul>
  </div>
</nav>
</div>
<h1 id = title>Welcome!</h1>
	<form method="post">
		<div class=container>
			<b>Username:</b> <input type = text name=usr></input></br>
            <b>Password:</b> <input type = password name=pwd></input></html>
		</div>
</br>
		<div class=container>
			<input type="submit" name ="submit" value = "Login"></input>
			<input type="button" id=register value = "Register" onclick = "register()"></input>
		</div>
	</form>
</body>
</html>

<?php
	session_start();
	//if session already exists, redirect to profile
	if(isset($_SESSION['login_user'])){
		header("location: HackProject.html");
	}
	//if submit button is pressed, login
	if(isset($_POST['submit'])){
		$user = $_POST['usr'];
		$pass = $_POST{'pwd'};
		$error='';
		//if username or password field is empty, print error
		if (empty($user) || empty($pass)){
			$error = 'Username or Password field is empty';
			echo "<div id=out>$error</div>";
		}
		else{
			//connect to database
			$conn = pg_connect("host=lightbridge-71838o63.cloudapp.net dbname=ziggy user=ziggy password=Commander1") or die('Could not connect: ' . pg_last_error());
			//check if user exists in authentication table
			$result = pg_prepare($conn,"User_Check", "SELECT * FROM users.authentication WHERE username = $1");
			$result = pg_execute($conn,"User_Check", array($user));
			$rows = pg_num_rows($result);
			//if username exists, check password attempt
			if ($rows == 1) {
				$salt = pg_fetch_result($result,'salt');
				$pwh = sha1($pass);
				$attempt = sha1($pwh + $salt);
				//attempt to login using supplied credentials
				$result = pg_prepare($conn, "Login_Attempt", "SELECT * FROM users.authentication WHERE username = $1 AND password_hash = $2");
				$result = pg_execute($conn, "Login_Attempt", array($user,$attempt));
				$rows = pg_num_rows($result);
				//if credentials match, log the action of user
				if ($rows == 1) {
					$ip = $_SERVER['REMOTE_ADDR'];
					$result = pg_prepare($conn,"Log_Activity","INSERT INTO users.log (username,ip_address,action) VALUES($1,$2,'Account Login')");
					$result = pg_execute($conn, "Log_Activity",array($user,$ip));
					session_start();
					$_SESSION['login_user']=$user;
					//redirect to profile
					header("location: profile.php");
				}
				else{
					//if password is invalid, issue error
					$error = 'Password is invalid';
					echo "<div id=out>$error</div>";
				}
			}
			else{
				//if username is invalid, issue error
				$error = 'Username is invalid';
				echo "<div id='out'>$error</div>";
			}
			//free result and close connection to database
			pg_free_result($result);
			pg_close($conn);
		}
	}
?>