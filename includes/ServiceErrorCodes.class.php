<?php
class ServiceErrorCodes
{
	/**
	 * The JSON sent is not a valid Request object.
	 */
	const JSONRPC_INVALID_REQUEST = -32600;

	/**
	 * The method does not exist / is not available.
	 */
	const JSONRPC_METHOD_NOT_FOUND = -32601;

	/**
	 * Invalid method parameter(s).
	 */
	const JSONRPC_INVALID_PARAMETERS = -32602;

	/**
	 * Internal JSON-RPC error.
	 */
	const JSONRPC_INTERNAL_ERROR = -32603;

	/**
	 * Invalid JSON was received by the server.
	 * An error occurred on the server while parsing the JSON text.
	 */
	const JSONRPC_PARSE_ERROR = -32700;

	/**
	 * No error occurred.
	 */
	const OK = 0;

	/**
	 * A database error occurred.
	 */
	const DB_ERROR = 1;

	/**
	 * The requested object was not found.
	 */
	const NOT_FOUND = 2;

	/**
	 * The data could not be loaded (e.g. corrupted).
	 */
	const DATA_ERROR = 3;

	/**
	 * No more data available (e.g. while going through the track history).
	 */
	const NO_MORE_DATA = 4;

	/**
	 * The used call type is not supported by the method (e.g. using RPC in RAW-only method).
	 */
	const INVALID_CALLTYPE = 5;

	/**
	 * The entry already exists.
	 */
	const DUPLICATE_ENTRY = 6;

	/**
	 * One or more required fields are empty.
	 */
	const EMPTY_FIELD = 7;

	/**
	 * A backend service call failed.
	 */
	const BACKEND_ERROR = 8;

	/**
	 * The requested data is not ready yet.
	 */
	const NOT_READY = 9;

	/**
	 * Get the standard error message of the specified error code.
	 *
	 * @param int $code The error code.
	 * @return null|string The error message or null if there is no error message.
	 */
	public static function getErrorMessageByCode($code)
	{
		switch ($code)
		{
			case ServiceErrorCodes::JSONRPC_INVALID_REQUEST:
				return "Invalid Request";
			case ServiceErrorCodes::JSONRPC_METHOD_NOT_FOUND:
				return "Method not found";
			case ServiceErrorCodes::JSONRPC_INVALID_PARAMETERS:
				return "Invalid params";
			case ServiceErrorCodes::JSONRPC_INTERNAL_ERROR:
				return "Internal error";
			case ServiceErrorCodes::JSONRPC_PARSE_ERROR:
				return "Parse error";
			case ServiceErrorCodes::DB_ERROR:
				return "A database error occurred!";
			case ServiceErrorCodes::NOT_FOUND:
				return "The requested object was not found!";
			case ServiceErrorCodes::DATA_ERROR:
				return "An error occurred while reading the data!";
			case ServiceErrorCodes::NO_MORE_DATA:
				return "There is no more data available!";
			case ServiceErrorCodes::INVALID_CALLTYPE:
				return "The used call type is not supported by this method!";
			case ServiceErrorCodes::DUPLICATE_ENTRY:
				return "The entry already exists!";
			case ServiceErrorCodes::EMPTY_FIELD:
				return "One or more required fields are empty!";
			case ServiceErrorCodes::BACKEND_ERROR:
				return "Backend service call failed!";
			case ServiceErrorCodes::NOT_READY:
				return "The requested data is not ready yet! Please try again later.";
			default:
				return null;
		}
	}
}