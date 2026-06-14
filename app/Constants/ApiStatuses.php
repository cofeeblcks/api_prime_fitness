<?php

namespace App\Constants;

class ApiStatuses
{
	public const STATUS_CONTINUE = 100;

	public const STATUS_OK = 200;

	public const STATUS_CREATED = 201;

	public const STATUS_ACCEPTED = 202;

	public const STATUS_NO_MODIFIED = 203;

	public const STATUS_NO_CONTENT = 204;

	public const STATUS_BAD_REQUEST = 400;

	public const STATUS_UNAUTHORIZED = 401;

	public const STATUS_FORBIDDEN = 403;

	public const STATUS_NOT_FOUND = 404;

	public const STATUS_METHOD_NOT_ALLOWED = 405;

	public const STATUS_UNPROCESSABLE_ENTITY = 422;

	public const STATUS_INTERNAL_SERVER_ERROR = 500;

	public const STATUS_SERVICE_UNAVAILABLE = 503;
}
