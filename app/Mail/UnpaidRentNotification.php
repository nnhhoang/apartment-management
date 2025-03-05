<?php

namespace App\Mail;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class UnpaidRentNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The user instance.
     */
    public User $user;

    /**
     * The unpaid collections.
     */
    public Collection $unpaidCollections;

    /**
     * The month of the unpaid rent.
     */
    public Carbon $month;

    /**
     * Total debt amount.
     */
    public float $totalDebt;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Collection $unpaidCollections, Carbon $month)
    {
        $this->user = $user;
        $this->unpaidCollections = $unpaidCollections;
        $this->month = $month;
        
        // Tính tổng nợ
        $this->totalDebt = $unpaidCollections->sum(function ($collection) {
            return $collection->total_price - $collection->total_paid;
        });
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thông báo phòng chưa thanh toán đủ tiền trọ tháng ' . $this->month->format('m/Y'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.unpaid-rent-notification',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
