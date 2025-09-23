<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResultNotification extends Notification
{
    use Queueable;

    protected $termSummary;
    protected $message;

    public function __construct($termSummary, $message)
    {
        $this->termSummary = $termSummary;
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Child\'s Term Results')
            ->greeting('Dear ' . ($notifiable->parent_name ?? 'Parent/Guardian'))
            ->line('We are pleased to share the term results for your child, ' . $notifiable->full_name . '.')
            ->line('**Term Summary**')
            ->line('Total Score: ' . ($this->termSummary->total_score ?? '-'))
            ->line('Average Score: ' . ($this->termSummary->average_score ? number_format($this->termSummary->average_score, 2) : '-'))
            ->line('Principal\'s Remark: ' . ($this->termSummary->principal_remark ?? '-'))
            ->line($this->message)
            ->action('View Full Results', url('/')) // Update with actual results page URL
            ->line('Thank you for your continued support.');
    }
}