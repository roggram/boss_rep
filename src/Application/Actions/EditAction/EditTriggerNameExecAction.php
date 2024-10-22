<?php

declare(strict_types=1);

namespace App\Application\Actions\EditAction;

use App\Application\Actions\Action;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Settings\SettingsInterface;
use App\Models\Trigger;

class EditTriggerNameExecAction extends Action{
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

		$update_trigger_name_text = $request->getParsedBody()["update_trigger_name_text"] ?? null;
		$trigger_id = $request->getParsedBody()["trigger_id"] ?? null;

		$target_trigger = Trigger::find($trigger_id);
		$target_trigger->trigger_name = $update_trigger_name_text;
		$target_trigger->save();

		return $this->response
			->withHeader("Location", "show_trigger")
			->withStatus(303);
	}
}
