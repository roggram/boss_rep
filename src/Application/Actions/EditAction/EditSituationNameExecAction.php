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
use App\Models\Situation;

class EditSituationNameExecAction extends Action{
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

		$update_situation_name_text = $request->getParsedBody()["update_situation_name_text"] ?? null;
		$trigger_id = $request->getParsedBody()["trigger_id"] ?? null;
		$situation_id = $request->getParsedBody()["situation_id"] ?? null;

		$target_situation = Situation::find($situation_id);
		$target_situation->situation_name = $update_situation_name_text;
		$target_situation->save();

		return $this->response
			->withHeader("Location", "/show_situation?trigger_id={$trigger_id}")
			->withStatus(303);
	}
}
