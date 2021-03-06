<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $response;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        //colocamos un if para enviar las variables que le interesa a cada mensaje web socket segun lo que pide el cliente
        if (isset($message['idSessionGame'])) {
            $this->response = [
                'idSessionGame' => $message['idSessionGame'],
                'firstGamer' => $message['firstGamer'],
                'mensaje'   => $message['mensaje'],
                'to'           => $message['to'],
                'from'         => auth()->user(),
            ];
        }else{
            $this->response = [
                'idMesa' => $message['idMesa'],
                'mensaje'   => $message['mensaje'],
                'to'           => $message['to'],
                'from'         => auth()->user(),
            ];
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel("MessageEvent.{$this->response['to']}");   
    }
}
