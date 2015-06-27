<?php

namespace Libs;

/**
 * Request from the server
 *
 * @author José María Valera Reales
 */
class KeysRequest {

	/**
	 * NOT CORRECT
	 */
	const NOT_CORRECT = 0;

	/**
	 * CORRECT
	 */
	const CORRECT = 1;

	/**
	 * Response OK
	 */
	const OK = 200;

	/**
	 * La petición ha sido completada y ha resultado en la creación de un nuevo recurso.
	 */
	const CREATED = 201;

	/**
	 * La petición ha sido aceptada para procesamiento, pero este no ha sido completado.
	 */
	const ACCEPTED = 202;

	/**
	 * Información no autoritativa (desde HTTP/1.1)
	 */
	const NON_AUTHORITAVIVE_INFORMATION = 203;

	/**
	 * Sin contenido
	 */
	const NO_CONTENT = 204;

	/**
	 * Recargar contenido
	 */
	const RESET_CONTENT = 205;

	/**
	 * La petición servirá parcialmente el contenido solicitado
	 */
	const PARTIAL_CONTENT = 206;
	//
	// 3xx Redirection
	//
	/**
	 * No modificado
	 */
	const NOT_MODIFIED = 304;
	//
	// 4xx Client Error
	//
	/**
	 * Solicitud incorrecta
	 */
	const BAD_REQUEST = 400;

	/**
	 * No autorizado
	 */
	const UNAUTHORIZED = 401;

	/**
	 * Prohibido
	 */
	const FORBIDDEN = 403;

	/**
	 * Recurso no encontrado.
	 * Se utiliza cuando el servidor web
	 * no encuentra la página o recurso solicitado.
	 */
	const NOT_FOUND = 404;

	/**
	 * Una petición fue hecha a una URI utilizando un método de solicitud
	 * no soportado por dicha URI; por ejemplo, cuando se utiliza GET en una
	 * forma que requiere que los datos sean presentados vía POST,
	 * o utilizando PUT en un recurso de sólo lectura
	 */
	const METHOD_NOT_ALLOWD = 405;

	/**
	 * Tiempo de espera agotado
	 */
	const REQUEST_TIMEOUT = 408;

	/**
	 * Indica que el recurso solicitado ya no está disponible y no lo
	 * estará de nuevo.
	 * Debería ser utilizado cuando un recurso ha
	 * sido quitado de forma permanente.
	 */
	const GONE = 410;

	/**
	 * URI demasiado larga
	 */
	const REQUEST_UNI_TOO_LONG = 414;

	/**
	 * Tipo de medio no soportado
	 */
	const UNSUPPORTED_MEDIA_TYPE = 415;

	/**
	 * La solicitud está bien formada pero fue imposible seguirla debido
	 * a errores semánticos.
	 * FALTAN PARÁMETROS.
	 */
	const UNPROCESSABLE_ENTITY = 422;

	/**
	 */
	const TOO_MANY_REQUEST = 429;
	//
	// 5xx Server Error
	//
	/**
	 * Error interno
	 */
	const INTERNAL_SERVER_ERROR = 500;

	/**
	 * No implementado
	 */
	const NOT_IMPLEMENTED = 501;
}