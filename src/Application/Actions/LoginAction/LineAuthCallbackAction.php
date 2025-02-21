<?php

declare(strict_types=1);

namespace App\Application\Actions\LoginAction;

use App\Application\Actions\Action;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Settings\SettingsInterface;


class LineAuthCallbackAction extends Action{
	private $twig;
	private $channel_id;
	private $redirect_uri;

	public function __construct(LoggerInterface $logger, Twig $twig, SettingsInterface $settings) {
		parent::__construct($logger, $twig, $settings);
		$this->twig = $twig;
		$this->channel_id = $_ENV['LINE_CHANNEL_ID'];
		$this->redirect_uri = $_ENV['LINE_AUTH_REDIRECT_URI'];
	}


	/**
	 * {@inheritdoc}
	 */
	protected function action(): Response {
		return $this->goToAuthPage();
	}
}
