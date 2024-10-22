<?php

declare(strict_types=1);

namespace App\Application\Actions\EditAction;

use App\Application\Actions\Action;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Settings\SettingsInterface;
use App\Models\Trigger;


class EditTriggerNameAction extends Action{
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

		$triggers = Trigger::all();
		$template  = 'edit_trigger_name.html.twig';

		return $this->twig->render($this->response, $template, 
				['triggers' => $triggers]);
	}
}
