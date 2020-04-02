<?php
namespace Crater\Http\Controllers\Auth;

use Crater\Proxy\HttpKernelProxy;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Validator;
use Hash;
use Crater\User;
use Auth;
use Crater\Http\Controllers\Controller;

class AccessTokensController extends Controller
{
	use ThrottlesLogins;

	/**
	 * A tool for proxying requests to the existing application.
	 *
	 * @var HttpKernelProxy
	 */
	protected $proxy;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(HttpKernelProxy $proxy)
	{
		$this->middleware('api')->except(['store', 'update']);
		$this->proxy = $proxy;
	}

	/**
	 * Get the login username to be used by the controller.
	 *
	 * @return string
	 */
	public function username()
	{
		return 'email';
	}

	/**
	 * Generate a new access token.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$request->validate([
			'username' => 'required|email',
			'password' => 'required|string|min:8',
		]);

		if ($this->hasTooManyLoginAttempts($request)) {
			$this->fireLockoutEvent($request);

			return $this->sendLockoutResponse($request);
		}

		return $this->requestPasswordGrant($request);
	}

	/**
	 * Refresh an access token.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request)
	{
		$token = $request->cookie('refresh_token');

		if (!$token) {
			throw ValidationException::withMessages([
				'refresh_token' => trans('oauth.missing_refresh_token')
			]);
		}

		$response = $this->proxy->postJson('oauth/token', [
			'client_id' => config('auth.proxy.client_id'),
			'client_secret' => config('auth.proxy.client_secret'),
			'grant_type' => 'refresh_token',
			'refresh_token' => $token,
			'scopes' => '[*]',
		]);

		if ($response->isSuccessful()) {
			return $this->sendSuccessResponse($response);
		}

		return response($response->getContent(), $response->getStatusCode());
	}

   /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('api');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $accessToken = Auth::user()->token();

        \DB::table('oauth_refresh_tokens')
                ->where('access_token_id', $accessToken->id)
                ->update([
                        'revoked' => true
                ]);

        $accessToken->revoke();

        return response()->json(null, 200);
    }

	/**
	 * Create a new access token from a password grant client.
	 *
	 * @param \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function requestPasswordGrant(Request $request)
	{
		$response = $this->proxy->postJson('oauth/token', [
			'client_id' => config('auth.proxy.client_id'),
			'client_secret' => config('auth.proxy.client_secret'),
			'grant_type' => config('auth.proxy.grant_type'),
			'username' => $request->username,
			'password' => $request->password,
			'scopes' => '[*]'
		]);

        $user = User::where('email', $request->username)->first();

        if ($response->isSuccessful()) {
            $this->clearLoginAttempts($request);
			return $this->sendSuccessResponse($response, $user);
		}

		$this->incrementLoginAttempts($request);

		return response($response->getContent(), $response->getStatusCode());
	}

	/**
	 * Return a successful response for requesting an api token.
	 *
	 * @param \Illuminate\Http\Response $response
	 * @return \Illuminate\Http\Response
	 */
	public function sendSuccessResponse(Response $response, $user)
	{
		$data = json_decode($response->getContent());

        $content = [
			'access_token' => $data->access_token,
			'expires_in' => $data->expires_in,
		];

		return response($content, $response->getStatusCode())->cookie(
			'refresh_token',
			$data->refresh_token,
			10 * 24 * 60,
			"",
			"",
			true,
			true
		);
    }

    public function isRegistered(Request $request)
    {
        if (User::whereEmail($request->email)->first()) {
            return 'true';
        } else {
            return 'false';
        }
    }
}
