<?php

namespace pekand\Chat;

use pekand\Log\Log;
use pekand\Chat\Services;

class NotificationManager
{

    private $lastNotification = null;
    private $chats = [];
    private $reportedChats = [];
    
    function __construct()
    {
    }

    function cleanReportedChats() {
        $reportedChatsToRemove = [];

        foreach ($this->reportedChats as $chatUid => $value){
            if((time() + 3600) < $value['time']) {
                $reportedChatsToRemove[] = $chatUid;
            }
        }

        foreach ($reportedChatsToRemove as $chatUid){
            if(isset($this->reportedChats[$chatUid])) {
                unset($this->reportedChats[$chatUid]);
            }
        }

    }

    function sendNotification($clientUid = null , $chatUid = null){

        if(\Config::EMAIL_API_SENDEMAIL_ENDPOINT === null) {
            return;
        }

        if($chatUid !== null && !in_array($chatUid, $this->chats) && !isset($this->reportedChats[$chatUid])) {
            $this->chats[] = $chatUid;
            $this->reportedChats[$chatUid] = [
                'time' => time()
            ];
        }

        if(count($this->chats) == 0){
            return;
        }

        if ( $this->lastNotification !== null && time() < ($this->lastNotification + 300)) {
            return;
        }

        $emailApi = Services::getEmailApiConnection();

        $subject = 'Client connected to chat';
        $body = '<h1>Client connected to chat</h1> <p>chat: '.print_r($this->chats, true).'</p> <br> <p><a href="https://socket.pekand.com/">link to page</a></p>';

        $out = null;
        if(!\Config::DEBUG_MODE) {
            $out = $emailApi->post(\Config::EMAIL_API_SENDEMAIL_ENDPOINT, [], [
                'action' => "sendEmail",
                'subject' => $subject,
                'body' => $body,
            ]);
        }

        Log::write("({$clientUid}) EMAILAPI: ".print_r([
            'output' => $out,
            'subject' => $subject,
            'body' => $body,
        ], true));

        $this->chats = [];
        $this->lastNotification = time();
    }
}
