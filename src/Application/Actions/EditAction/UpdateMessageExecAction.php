<?php

declare(strict_types=1);

namespace App\Application\Actions\EditAction;

use App\Application\Actions\Action;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Settings\SettingsInterface;
use App\Models\Message;


class UpdateMessageExecAction extends Action{
	private $twig;

	public function __construct(LoggerInterface $logger, Twig $twig, SettingsInterface $settings) {
		parent::__construct($logger, $twig, $settings);
		$this->twig = $twig;
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function action(): Response {

		$request = $this->request;

		$update_message_text = $request->getParsedBody()["update_message_text"] ?? null;
		$message_id = $request->getParsedBody()["message_id"] ?? null;

		$target_message = Message::find($message_id);
		$target_message->message = $update_message_text;
		$target_message->save();
		
		$trigger_id = $target_message->trigger_id;
		$situation_id = $target_message->situation_id;

		return $this->response
			// ->withHeader("Location", "/edit_message")
			->withHeader("Location", "/edit_message?trigger_id={$trigger_id}&situation_id={$situation_id}")
			->withStatus(303);
	}
}
