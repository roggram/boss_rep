<?php

declare(strict_types=1);

namespace App\Application\Actions\DeleteAction;

use App\Application\Actions\Action;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Settings\SettingsInterface;
use App\Models\Trigger;
use App\Models\Situation;
use App\Models\Message;


class DeleteTriggerAction extends Action{
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

		$trigger_id = $request->getParsedBody()["trigger_id"] ?? null;

		$target_trigger = Trigger::find($trigger_id);
		$target_trigger->delete();

		$target_situations = Situation::query()
      ->where("trigger_id", $trigger_id)
      ->delete();
    $this->logger->info("Deleted situations for trigger_id: {$trigger_id}");

    
		$target_messages = Message::query()
      ->where("trigger_id", $trigger_id)
      ->delete();
      $this->logger->info("Deleted messages for trigger_id: {$trigger_id}");

		return $this->response
			// ->withHeader("Location", "/edit_message")
			->withHeader("Location", "/show_trigger")
			// ->withHeader("Location", "/edit_message?trigger_id={$trigger_id}&situation_id={$situation_id}")
			->withStatus(303);
	}
}
