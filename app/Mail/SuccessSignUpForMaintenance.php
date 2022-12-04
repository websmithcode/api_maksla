<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

class SuccessSignUpForMaintenance extends Mailable
{
	use Queueable, SerializesModels;

	public $client_name;
	public $date;
	public $time;
	public $car;
	public $works;

	/**
	 * Create a new message instance.
	 *
	 * @return void
	 */
	public function __construct(
		string $client_name,
		string $date,
		string $time,
		string $car,
		array $works
	) {
		$this->client_name = $client_name;
		$this->date = $date;
		$this->time = $time;
		$this->car = $car;
		$this->works = $works;
	}

	/**
	 * Get the message envelope.
	 *
	 * @return \Illuminate\Mail\Mailables\Envelope
	 */
	public function envelope()
	{
		return new Envelope(
			subject: 'Запись на техническое обслуживание автомобиля',
		);
	}

	/**
	 * Get the message content definition.
	 *
	 * @return \Illuminate\Mail\Mailables\Content
	 */
	public function content()
	{
		return new Content(
			view: 'mails.success_sign_up_for_maintenance',
		);
	}

	/**
	 * Get the attachments for the message.
	 *
	 * @return array
	 */
	public function attachments()
	{
		return [];
	}
}
