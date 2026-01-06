<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EnrollmentConfirmed extends Notification
{
    use Queueable;

    public $enrollment;

    /**
     * Create a new notification instance.
     */
    public function __construct($enrollment)
    {
        $this->enrollment = $enrollment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // FIX: Use the student's first name from the enrollment record
        // instead of $notifiable->name (which caused the error)
        $studentName = $this->enrollment->student->first_name ?? 'Student';
        $courseName = $this->enrollment->course->name ?? 'Course';

        return (new MailMessage)
            ->subject('Enrollment Confirmed')
            ->greeting('Hello ' . $studentName . '!')
            ->line('Your enrollment has been successfully confirmed.')
            ->line('Course: ' . $courseName)
            ->action('Login to Portal', url('/login'))
            ->line('Thank you for choosing Moyo Safi Tailoring Center!');
    }
}