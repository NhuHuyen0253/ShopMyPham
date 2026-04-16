<?php
namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewChatMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Message $message) {}

    public function broadcastOn(): Channel
    {
        // public channel đơn giản; có thể đổi sang private('admin.notifications')
        return new Channel('chatbox');
    }

    public function broadcastAs(): string
    {
        return 'NewChatMessage';
    }

    public function broadcastWith(): array
    {
        return [
            'id'      => $this->message->id,
            'name'    => $this->message->name,
            'phone'   => $this->message->phone,
            'content' => $this->message->content,
            'time'    => $this->message->created_at?->toDateTimeString(),
        ];
    }
}
