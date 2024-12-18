<?php

declare(strict_types=1);

namespace App\Application\Actions\AddAction;

use App\Application\Actions\Action;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Settings\SettingsInterface;
use App\Models\Trigger;

class AddSituationAction extends Action{
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

		$template  = 'add_situation.html.twig';
		$trigger_id = $request->getQueryParams()["trigger_id"] ?? null;


		// $triggers = Trigger::all();

		return $this->twig->render($this->response, $template, 
			['trigger_id' => $trigger_id]);
	}
}
