<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Order $order
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $url = route('admin.inventory.orders.show', $this->order);
        return [
            'type' => 'new_order',
            'title' => 'New order #' . $this->order->order_number,
            'message' => 'Order from ' . $this->order->customer_name . ' — Total: ' . number_format($this->order->total, 2),
            'url' => $url,
            'order_id' => $this->order->id,
        ];
    }
}
