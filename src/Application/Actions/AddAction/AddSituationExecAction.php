<?php

declare(strict_types=1);

namespace App\Application\Actions\AddAction;

use App\Application\Actions\Action;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Settings\SettingsInterface;
use App\Models\Situation;
use App\Models\Trigger;

class AddSituationExecAction extends Action{
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
    $situation_name = $params["add_situation_name"] ?? null;
    $trigger_id = $params["trigger_id"] ?? null;

    $situation = new Situation();
    $situation->situation_name = $situation_name;
    $situation->trigger_id = $trigger_id;
    $situation->created_at = date("now");
    $situation->deleted_at = null;
    $situation->save();

		$triggers = Trigger::all();

		return $this->twig->render($this->response, $template, 
			['trigger_id' => $trigger_id, 'triggers' => $triggers]);
	}
}
