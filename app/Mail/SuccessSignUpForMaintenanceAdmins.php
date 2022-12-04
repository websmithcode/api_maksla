<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SuccessSignUpForMaintenanceAdmins extends Mailable
{
	use Queueable, SerializesModels;

	public $client_name;
	public $client_email;
	public $client_phone;
	public $date;
	public $time;
	public $car;
	public $maintenance_name;
	public $works;
	public $price;
	public $extra;

	/**
	 * Create a new message instance.
	 *
	 * @return void
	 */
	public function __construct(
		string $client_name,
		string $client_email,
		string $client_phone,
		string $date,
		string $time,
		string $car,
		string $maintenance_name,
		array $works,
		int $price,
		array $extra=null
	)
	{
		$this->client_name = $client_name;
		$this->client_email = $client_email;
		$this->client_phone = $client_phone;
		$this->date = $date;
		$this->time = $time;
		$this->car = $car;
		$this->maintenance_name = $maintenance_name;
		$this->works = $works;
		$this->price = $price;
		$this->extra = $extra;
	}

	/**
	 * Get the message envelope.
	 *
	 * @return \Illuminate\Mail\Mailables\Envelope
	 */
	public function envelope()
	{
		return new Envelope(
			subject: 'Новая запись на техническое обслуживание автомобиля',
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
			view: 'mails.success_sign_up_for_maintenance_admins',
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
