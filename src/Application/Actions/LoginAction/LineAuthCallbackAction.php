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
		$this->logger = $logger;
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
		if (isset($params['error']) || isset($params['error_description'])) {
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

		// -+-+-+-+-+-+-+-+--+--+-
		//  curlでアクセストークンを取得
		// -+-+-+-+-+-+-+-+--+--+-
		// tokenをリクエストするURLを作成
		$token_request_url = http_build_query([
			'grant_type' => 'authorization_code',
			'code' => $code,
			'redirect_uri' => $_ENV['LINE_AUTH_REDIRECT_URI'],
			'client_id' => $_ENV['LINE_CHANNEL_ID'],
			'client_secret' => $_ENV['LINE_CHANNEL_SECRET']
		]);
		$ch = curl_init();
		curl_setopt_array($ch, [
			// curlで叩きに行くURLを設定
			CURLOPT_URL => "https://api.line.me/oauth2/v2.1/token",
			CURLOPT_POST => true,
			// レスポンスを文字列で返す
			CURLOPT_RETURNTRANSFER => true,
			// ヘッダー情報を設定
			CURLOPT_HTTPHEADER => "Content-Type: application/x-www-form-urlencoded",
			// POSTリクエストで送信するデータを指定
			CURLOPT_POSTFIELDS => $token_request_url,
		]);
		// curl_exec($ch)でリクエストを実行
		$response_from_line_token_server = curl_exec($ch);
		curl_close($ch);
		// -+-+-+-+-+-+-+-+--+--+-		
		// curl終了
		// -+-+-+-+-+-+-+-+--+--+-


		// -+-+-+-+-+-+-+-+--+--+-
		// -+-+-+-+-+-+-+-+--+--+-
		// 以降は次のチケットかと思う処理
		// -+-+-+-+-+-+-+-+--+--+-
		// -+-+-+-+-+-+-+-+--+--+-
		if ($response_from_line_token_server === false) { // curlによるtokenリクエストが失敗した場合
			$this->logger->error("curl使用: LINEからアクセスtokenを取得するために送信したクエリパラメータ: {$token_request_url}");
			$this->logger->error("curl使用: アクセスtokenを取得するために叩き,返されたjson: {$response_from_line_token_server}");
			return $this->twig->render($response, "error.html.twig", 
				['error_message' => "curl_execが失敗しました"]);
		}

		// レスポンスをjson形式に変換
		$response_from_line_token_server_assoc = json_decode($response_from_line_token_server, true);
		if ($response_from_line_token_server_assoc === null) // もしjson_decodeが失敗した場合
		{
			$this->logger->error("LINEからのアクセスtokenを含むレスポンスのjson_decode()に失敗しました");
			$this->logger->error("curl使用: LINEからアクセスtokenを取得するために送信したクエリパラメータ: {$token_request_url}");
			$this->logger->error("curl使用: アクセスtokenを取得するために叩き,返されたjson: {$response_from_line_token_server}");
			return $this->twig->render($response, "error.html.twig", 
				['error_message' => "LINEログインに失敗しました"]);
		}
		// LINEへアクセスtoken取得のために投げたリクエストに対するレスポンスをログに出力
		// しつつ、アクセストークンなどを取り出す
		foreach ($response_from_line_token_server_assoc as $key => $value) {
			$this->logger->info("{$key}:{$value}\n");

		}
		
		// ヘッダー情報を設定
		return $response->withHeader('Location', '/')->withStatus(200);
	}
}