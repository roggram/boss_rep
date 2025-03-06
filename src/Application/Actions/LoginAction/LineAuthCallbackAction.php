<?php

declare(strict_types=1);

namespace App\Application\Actions\LoginAction;

use App\Application\Actions\Action;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Application\Settings\SettingsInterface;
use Illuminate\Support\Facades\Redis;

class LineAuthCallbackAction extends Action{
	private const LINE_WEB_LOGIN_STATE = 'line_web_login_state';
	// private $channel_id;
	// private $redirect_uri;
	private $twig;
	public function __construct(LoggerInterface $logger, SettingsInterface $settings, Twig $twig) {
		parent::__construct($logger, $settings);
		$this->$twig = $twig;
		// $this->channel_id = $_ENV['LINE_CHANNEL_ID'];
		// $this->redirect_uri = $_ENV['LINE_AUTH_REDIRECT_URI'];
	}

	/**
	 * {@inheritdoc}
	 */
	protected function action(): Response {
		return $this->auth($this->request, $this->response);
	}

	// ここのタスクに関する公式解説URL
	// https: developers.line.biz/ja/docs/line-login/integrate-line-login#receiving-the-authorization-code-or-error-response-with-a-web-app
	private function auth(Request $request, Response $response):Response
	{
		$params = $request->getQueryParams();
		// -+-+-+-+-+-+-+-+--+--+--+-+-+-+-+-+-+-+--+--+-		
		// 確認のため、LINE認証サーバからのコールバックURLのクエリパラメータをログに出力
		// -+-+-+-+-+-+-+-+--+--+--+-+-+-+-+-+-+-+--+--+-
		$now = new \DateTime();
		$this->logger->info($now->format("Y-m-d H:i:s\n"));
		foreach ($params as $key => $value) {
			$this->logger->info("{$key}:{$value}\n");
		}
		// -+-+-+-+-+-+-+-+--+--+-
		// 正常の場合のパラメータ取得
		// -+-+-+-+-+-+-+-+--+--+-
		$code = $params['code'] ?? null;
		$state = $params['state'] ?? null;
		$friendship_status_changed = $params['friendship_status_changed'] ?? null;
		
		// -+-+-+-+-+-+-+-+--+--+-
		// エラー時のパラメータがあれば対応
		// -+-+-+-+-+-+-+-+--+--+-
		if (isset($params['error']) || isset($params['error_description']) ||isset($params['state'])) {
			// エラーがあればエラーページにリダイレクト
			return $this->twig->render($response, "error.html.twig", 
				['error' => ""]);
		}

		// 作成したstateとコールバックURLのstateが一致しない場合はエラー
		if ($state !== $_SESSION["LINE_LOGIN_STATE"]) {
			return $this->twig->render($response, "error.html.twig", 
				['error' => ""]);
		}

		unset($_SESSION["LINE_LOGIN_STATE"]);
		return $response->withHeader('Location', '/')
						->withStatus(200);
	}
}