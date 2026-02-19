<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewOrderNotification extends Notification
{
    use Queueable;

    public $orderId;
    public $customerName;
    public $total;
    public $orderType;
    public $phone;

    public function __construct($orderId, $customerName, $total, $orderType, $phone)
    {
        $this->orderId      = $orderId;
        $this->customerName = $customerName;
        $this->total        = $total;
        $this->orderType    = $orderType;
        $this->phone        = $phone;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🔔 Pesanan Baru #' . $this->orderId)
            ->greeting('Halo Admin!')
            ->line('Ada pesanan baru masuk:')
            ->line('**Order ID:** ' . $this->orderId)
            ->line('**Nama Customer:** ' . $this->customerName)
            ->line('**No HP:** ' . $this->phone)
            ->line('**Tipe Order:** ' . ($this->orderType === 'dine-in' ? 'Makan di Sini' : 'Bawa Pulang'))
            ->line('**Total:** Rp ' . number_format($this->total, 0, ',', '.'))
            ->action('Lihat Detail Order', url('/admin/history'))
            ->line('Segera proses pesanan ini!')
            ->salutation('Salam, Classic Coffee 789');
    }
}