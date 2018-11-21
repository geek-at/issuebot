<?php include_once('config.inc.php') or die ("Konfigurationsdatei fehlt!"); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo TITLE; ?> Ticket System</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <a class="navbar-brand" href="#"><?php echo TITLE; ?> Ticket System</a>
        </div>
      </div>
    </nav>

    

    <!-- Main jumbotron for a primary marketing message or call to action -->
    <div class="jumbotron">
      <div class="container">

        <?php 
        
        if($_REQUEST['submit'])
        {
            $name = trim($_REQUEST['name']);
            $email = trim($_REQUEST['email']);
            $text = $_REQUEST['text'];

            if(!$name && !$email)
                echo '<div class="alert alert-danger">
                        <strong>Fehler!</strong> Bitte geben Sie einen Namen oder eine Email Adresse, sowie eine genaue Beschreibung ein.
                    </div>';
            else
            {

                $url = 'https://'.GIT_DOM.'/api/v1/repos/'.GIT_USER.'/'.GIT_REPO.'/issues?token='.GIT_TOKEN;

                $body = "- **Name:** $name\n- **Email:** $email\n- **IP:** ".getUserIP()."\n## Nachricht:\n$text";

                $data = array('title' => 'Meldung von '.$name.' ('.$email.')', 'body' => $body);
                $options = array(
                        'http' => array(
                        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                        'method'  => 'POST',
                        'content' => http_build_query($data),
                    )
                );

                $context  = stream_context_create($options);
                $result = file_get_contents($url, false, $context);
                if($result!==false)
                    echo '<div class="alert alert-success">
                            <strong>Nachricht übermittelt!</strong> Ihre Nachricht wurde erfolgreich übermittelt
                            <script>alert("Ihre Nachricht wurde erfolgreich übermittelt!"); window.location.href="?";</script>
                        </div>';
                else
                    echo '<div class="alert alert-danger">
                            <strong>Fehler!</strong> Wegen eines internen Fehlers konnte die Nachricht nicht gespeichert werden. Bitte direkt eine Email an it@g19.at schreiben.
                        </div>';
            }
            
        }
    ?>

        <h1>Problem / Wunsch melden</h1>
        <form method="POST" action="?" class="form" role="form">
            <div class="form-group">
                <strong>Ihr Name</strong>
              <input autocomplete="off" name="name" type="text" placeholder="" class="form-control">
            </div>
            <div class="form-group">
            <strong>Ihre Email Adresse</strong>
              <input autocomplete="off" name="email" type="email" placeholder="" class="form-control">
            </div>

            <div class="form-group">
            <strong>Ihre Nachricht</strong>
              <textarea name="text" class="form-control" rows="5"></textarea>
            </div>
              

            <input type="submit" name="submit" class="btn btn-success" value="Meldung absenden" />
        </form>


      </div>
    </div>

<div class="container">
  </div>
      <hr>

      <footer>
        <p>&copy; <?php echo TITLE; ?></p>
      </footer>
    </div> <!-- /container -->

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>
<?php
function getUserIP()
{
	$client  = @$_SERVER['HTTP_CLIENT_IP'];
	$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
	$remote  = $_SERVER['REMOTE_ADDR'];
	
    if(strpos($forward,','))
    {
        $a = explode(',',$forward);
        $forward = trim($a[0]);
    }
	if(filter_var($forward, FILTER_VALIDATE_IP))
	{
		$ip = $forward;
	}
    elseif(filter_var($client, FILTER_VALIDATE_IP))
	{
		$ip = $client;
	}
	else
	{
		$ip = $remote;
	}
	return $ip;
}