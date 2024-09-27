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


class DeleteMessageAction extends Action{
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

		$trigger_id = $request->getQueryParams()["trigger_id"] ?? null;
		$situation_id = $request->getQueryParams()["situation_id"] ?? null;
		// $trigger_id = $request->getQueryParams();
		$template  = 'edit_situation_message.html.twig';
		
		$messages = Message::query()
			->where("trigger_id", $trigger_id)
			->where("situation_id", $situation_id)
			->get();

		return $this->twig->render($this->response, $template, 
			[ 'trigger_id' => $trigger_id,
        'situation_id' => $situation_id, 
        'messages' => $messages
      ]);
	}
}