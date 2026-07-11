<?php

namespace App\Mail;

use App\Models\MonthlyAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MonthlySalaryAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public MonthlyAlert $alert)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Monthly Salary & Expense Alert',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.monthly-salary-alert',
            with: [
                'alert' => $this->alert,
            ],
        );
    }
}
