<?php

declare(strict_types=1);

namespace App\Application\Actions\ShowAction;

use App\Application\Actions\Action;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Settings\SettingsInterface;
use App\Models\Trigger;

class ShowTriggerAction extends Action{
	private $twig;

	public function __construct(LoggerInterface $logger, Twig $twig, SettingsInterface $settings) {
		parent::__construct($logger, $twig, $settings);
		$this->twig = $twig;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function action(): Response {
		$template  = './show_trigger.html.twig';
		// $template  = 'templates/show_trigger.html.twig';
		// $title     = 'ShowTrigger';

		$triggers = Trigger::get();
		// if (count($triggers) === 0){
			
		// }

		return $this->twig->render($this->response, $template, 
			['triggers' => $triggers]);
	}
}
