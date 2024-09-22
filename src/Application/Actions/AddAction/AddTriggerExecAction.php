<?php

declare(strict_types=1);

namespace App\Application\Actions\AddAction;

use App\Application\Actions\Action;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Settings\SettingsInterface;
use App\Models\Trigger;

class AddTriggerExecAction extends Action{
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
		$template  = 'show_trigger.html.twig';

    $params = $request->getParsedBody();
    
    // リクエストパラメータ
    $trigger_name = $params["trigger_name"] ?? null;
    
    // $trigger_name = $params->trigger_name;

    $trigger = new Trigger();
    $trigger->name = $trigger_name;
    $trigger->save();

		// $triggers = Trigger::all();

		return $this->twig->render($this->response, $template, 
			[]);
	}
}
