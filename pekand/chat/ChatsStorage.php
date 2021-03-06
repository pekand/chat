<?php

namespace pekand\Chat;

class ChatsStorage
{
    private $chats = [];
    private $storagePath = 'storage/chats/';
    
    public function __construct() {
           
    }

    public function uid($length = 32) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $bytes = openssl_random_pseudo_bytes($length);
        $string = "";

        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[ord($bytes[$i]) % $charactersLength];
        }

        return $string;
    }

    public function isUid($length = 32) {
        return true;
    }
    
    public function isChatOpen($chatUid)
    {       
        if ($chatUid != null && $chatUid != "" && isset($this->chats[$chatUid])) {
            return true;
        }

        return false;
    }
    
    public function openChat($chatUid = null)
    {
        if(empty($chatUid)) {           
           return $this->createChat();
        }
        
        if (!$this->isUid($chatUid)) {
            return null;
        }
        
        if (isset($this->chats[$chatUid])) {
            return $chatUid;
        }
        
        $chatStorageFile = $this->storagePath.$chatUid.".json";

        if(!file_exists($chatStorageFile)) {
           return $this->createChat();
        }
        
        $this->chats[$chatUid] = json_decode(file_get_contents($chatStorageFile), true);
        
        $this->chats[$chatUid]['participants']['operators'] = [];
        $this->chats[$chatUid]['participants']['clients'] = [];
        
        return $chatUid;
    }
    
    public function createChat()
    {
        $chatUid = $this->uid();
        $this->chats[$chatUid] = [
           'messages' => [],
           'participants' => [
                'operators' => [],
                'clients' => [],
           ],
        ];

        return $chatUid;
    }
    
    public function getChats()
    {       
        $openedChats = [];
        foreach ($this->chats as $chatUid => $chats) {
            $openedChats[] = $chatUid;
        }
        
        return $openedChats;
    }
        
    public function getChat($chatUid)
    {       
        if (!$this->isUid($chatUid)) {
            return null;
        }
        
        if (!isset($this->chats[$chatUid])) {
            return null;
        }
        
        $this->chats[$chatUid];
        
        return $this->chats[$chatUid];
    }
    
    public function getChatOperators($chatUid)
    {       
        if (!$this->isUid($chatUid)) {
            return [];
        }
        
        if (!isset($this->chats[$chatUid])) {
            return [];
        }
        
        $this->chats[$chatUid];
        
        return $this->chats[$chatUid]['participants']['operators'];
    }
    
    public function getChatClients($chatUid)
    {       
        if (!$this->isUid($chatUid)) {
            return [];
        }
        
        if (!isset($this->chats[$chatUid])) {
            return [];
        }
        
        $this->chats[$chatUid];
        
        return $this->chats[$chatUid]['participants']['clients'];
    }
    
    public function getChatHistory($chatUid)
    {
        if(empty($chatUid)) {
           return null;
        }
        
        if (!$this->isUid($chatUid)) {
            return null;
        }
        
        if (!isset($this->chats[$chatUid])) {
            return null;
        }
        
        return $this->chats[$chatUid];
    }
    
    public function addMessage($chatUid, $clientUid, $message, $role, $type)
    {
        if (!isset($this->chats[$chatUid])){
            return false;
        }
        
        $this->chats[$chatUid]['messages'][] = [
            'role' => $role,
            'from' => $clientUid,
            'time' => microtime(true),
            'message' => $message,
            'type' => $type,
        ] ;
        
        return true;
    }
    
    public function addOperatorMessage($chatUid, $operatorUid, $message, $type)
    {        
        if (!isset($this->chats[$chatUid])){
            return false;
        }
        
        if(!in_array($operatorUid, $this->chats[$chatUid]['participants']['operators'])){
            $this->chats[$chatUid]['participants']['operators'][] = $operatorUid;
        }
        
        return $this->addMessage($chatUid, $operatorUid, $message, 'operator', $type);
    }
    
    public function addClientToChat($chatUid, $clientUid)
    {     
        if ($chatUid === null || $clientUid === null){
            return false;
        }

        if (!isset($this->chats[$chatUid])){
            return false;
        }

        if(in_array($clientUid, $this->chats[$chatUid]['participants']['operators'])){
            return false;
        }
        
        if(in_array($clientUid, $this->chats[$chatUid]['participants']['clients'])){
            return false;
        }

        $this->chats[$chatUid]['participants']['clients'][] = $clientUid;
        
        return true;
    }

    public function addClientMessage($chatUid, $clientUid, $message, $type)
    {        
        if (!isset($this->chats[$chatUid])){
            return false;
        }
        
        if(!in_array($clientUid, $this->chats[$chatUid]['participants']['clients'])){
            return false;
        }
        
        return $this->addMessage($chatUid, $clientUid, $message, 'client', $type);
    }
    
    public function saveChat($chatUid)
    {        
        if (!isset($this->chats[$chatUid])){
            return false;
        }
        
        $chatStorageFile = $this->storagePath.$chatUid.".json";
        
        file_put_contents($chatStorageFile, json_encode($this->chats[$chatUid]));
        
        return true;
    }
    
    public function saveAllChats()
    {        
        foreach ($this->chats as $chatUid => $chat) {
            $this->saveChat($chatUid);
        }
    }
    
    public function closeChat($chatUid)
    {        
        if(!isset($this->chats[$chatUid])){
            return;
        }
        
        $this->saveChat($chatUid);
        unset($this->chats[$chatUid]);
    }
    
    public function addOperatorToChat($chatUid, $clientUid)
    {
        if (!isset($this->chats[$chatUid])){
            return;
        }
        
        if (!in_array($clientUid, $this->chats[$chatUid]['participants']['clients'])) {
            $this->chats[$chatUid]['participants']['clients'][] = $clientUid;
        }
    }
    
    public function addOperatorToAllChats($operatorUid)
    {       
        foreach ($this->chats as $chatUid => $chats) {
            if (!in_array($operatorUid, $this->chats[$chatUid]['participants']['operators'])) {
                $this->chats[$chatUid]['participants']['operators'][] = $operatorUid;
           }
        }
    }
    
    public function removeOperatorFromAllChats($operatorUid)
    {       
        $chatsWithoutOperator = [];
        foreach ($this->chats as $chatUid => $chat) {          
            if (($key = array_search($operatorUid, $this->chats[$chatUid]['participants']['operators'])) !== false) {
                unset($this->chats[$chatUid]['participants']['operators'][$key]);
                
                if(count($this->chats[$chatUid]['participants']['operators']) == 0){
                    $chatsWithoutOperator[] = $chatUid;
                }
            }
        }
        return $chatsWithoutOperator;
    }
    
    public function isClientInChat($chatUid, $clientUid)
    {
        if (!isset($this->chats[$chatUid])){
            return false;
        }
        
        if (!in_array($clientUid, $this->chats[$chatUid]['participants']['clients'])) {
            return false;
        }
        
        return true;
    }

    public function removeClientFromAllChats($clientUid)
    {       
        $chatsWithoutClients = [];
        foreach ($this->chats as $chatUid => $chat) {          
            if (($key = array_search($clientUid, $this->chats[$chatUid]['participants']['clients'])) !== false) {
                unset($this->chats[$chatUid]['participants']['clients'][$key]);
                
                if(count($this->chats[$chatUid]['participants']['clients']) == 0){
                    $chatsWithoutClients[] = $chatUid;
                }
            }
        }
        return $chatsWithoutClients;
    }
}
