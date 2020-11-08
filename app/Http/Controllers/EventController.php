<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{

    function sms_send($params,$backup = false)
    {
        static $content;

        $token = env('SMS_TOKEN'); //https://ssl.smsapi.pl/react/oauth/manage



        if ($backup == true) {
            $url = 'https://api2.smsapi.pl/sms.do';
        } else {
            $url = 'https://api.smsapi.pl/sms.do';
        }

        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, $params);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer $token"
        ));

        $content = curl_exec($c);
        $http_status = curl_getinfo($c, CURLINFO_HTTP_CODE);

        if ($http_status != 200 && $backup == false) {
            $backup = true;
            sms_send($params, $token, $backup);
        }

        curl_close($c);
        return $content;
    }

    public function index()
    {
        return Event::all();
    }

    public function test()
    {
        echo 'dupa';
    }

    public function store(Request $request)
    {
        Event::create($request->all());
    }

    public function update(Request $request, Event $event)
    {
        Event::update($request->all());
    }

    public function destroy(Event $event)
    {
        $event->delete();
    }

    public function handleEvents()
    {
        $events = Event::where('dispatched', '=', 0)->get();

        foreach ($events as $event) {
            date_default_timezone_set('Europe/Warsaw');

            $curdate = date('Y-m-d H:i:s');
            if ($event->date . ' ' . $event->time < $curdate) {
                echo 'data ' . $event->date . ' ' . $event->time . ' jest mniejsza niż aktualny czas' . '<br>';
                Event::find($event->id)->update([
                    'dispatched' => true
                ]);
                echo $event->dispatched;
                echo '<br>';


                $params = array(
                    'to' => '506829865', //numery odbiorców rozdzielone przecinkami
                    'from' => 'Test', //pole nadawcy stworzone w https://ssl.smsapi.pl/sms_settings/sendernames
                    'message' => $event->description, //treść wiadomości
                    'format' => 'json',
                    'test' => 1
                );

                echo 'dziala';

                echo $this->sms_send($params);
            } else {
                echo 'nie dzala';
                echo 'data ' . $event->date . ' ' . $event->time . ' jest większa niż aktualny czas';
            }
        }
    }
}
