<?php

declare(strict_types=1);

namespace App\Application\Actions\AddAction;

use App\Application\Actions\Action;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Settings\SettingsInterface;
use App\Models\Message;
use App\Models\Trigger;

class AddMessageExecAction extends Action{
	private $twig;

	public function __construct(LoggerInterface $logger, Twig $twig, SettingsInterface $settings) {
		parent::__construct($logger, $twig, $settings);
		$this->twig = $twig;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function action(): Response {
		$params = $this->request->getParsedBody();

		$add_message_text = $params["add_message_text"] ?? null;
		$trigger_id = $params["trigger_id"] ?? null;
		$situation_id = $params["situation_id"] ?? null;
		
    $message = new Message();
		$message->message = $add_message_text;
		$message->trigger_id = $trigger_id;
		$message->situation_id = $situation_id;
		$message->created_at = Date("now");
		$message->deleted_at = null;

		$message->save();

		return $this->response
			->withHeader("Location", "http://localhost:8080/show_trigger")
			->withStatus(303);
	}
}
