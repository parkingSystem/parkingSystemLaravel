<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\ServiceAccount;

class FirebaseController extends Controller
{
    public static function index(String $id,String $messages)
    {
		//topic need to know to which user to send message
		$topic = $id;
		//factory need to connect firebase
		$factory = (new Factory())->withServiceAccount(__DIR__.'/parkingsystem-f0e17-firebase-adminsdk-mpz8z-bb24f203c6.json');
		$messaging = ($factory)->createMessaging();
		//creating message
		$message = CloudMessage::fromArray([
		'topic' => $topic,
		'notification' => [
		'title' => $messages,
		'body' => $messages,
		],]);
		//sending message
		$messaging->send($message);
    }
}
