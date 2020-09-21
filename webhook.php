<?php
if(!file_exists('inc/config.inc.php')) die('Konfigurationsdatei nicht gefunden!');
include_once('inc/config.inc.php');
include_once('inc/functions.php');
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__));

$data = json_decode(file_get_contents('php://input'),true);

$action = $data['action'];
$issue = $data['issue']['id'];
$username = $data['issue']['user']['username'];
$issueurl='http://'.GIT_DOM.'/'.GIT_USER.'/'.GIT_REPO.'/issues/'.$issue;

switch($action)
{
    case 'created': //comment
        $comment = $data['comment']['body'];
        $etext = "Ticket $issue - $issueurl
$username hat folgendes kommentiert:

$comment";
        $lastmail = sendMail(EMAIL_TO,'Re: [TICKET] '.$issue,EMAIL_ALTERNATIVE,$etext);
    break;

    case 'reopened':
        $etext = "Ticket $issue - $issueurl
$username hat das Ticket wiedereröffnet";
        $lastmail = sendMail(EMAIL_TO,'Re: [TICKET] '.$issue,EMAIL_ALTERNATIVE,$etext);
    break;

    case 'closed':
        $etext = "Ticket $issue - $issueurl
$username hat das Ticket geschlossen";
        $lastmail = sendMail(EMAIL_TO,'Re: [TICKET] '.$issue,EMAIL_ALTERNATIVE,$etext);
    break;

    default:
        file_put_contents('tmp/lastfail.txt',file_get_contents('php://input'));
        exit('');
}


file_put_contents('tmp/lastmail.log',$lastmail);

file_put_contents('tmp/lasthook.txt',file_get_contents('php://input'));


//[TICKET] '.$id