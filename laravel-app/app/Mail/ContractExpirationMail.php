<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContractExpirationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contractData;
    public $tenantData;

    public function __construct(array $contractData, array $tenantData)
    {
        $this->contractData = $contractData;
        $this->tenantData = $tenantData;
    }

        public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Quản Lý Phòng Trọ] Thông báo sắp hết hạn hợp đồng thuê phòng - ' . $this->contractData['maPhong'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contract_expiration',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
