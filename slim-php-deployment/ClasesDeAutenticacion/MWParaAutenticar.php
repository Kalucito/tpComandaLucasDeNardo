<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;

include_once("AutentificadorJWT.php");

class MWParaAutenticar
{


	public function VerificarUsuario(Request $request, RequestHandler $handler) : ResponseMW
	{

		$objDelaRespuesta = new stdclass();
		$objDelaRespuesta->respuesta = "";

		$method = $request->getMethod();
		$getToken = false;
		

		$response = new ResponseMW();

		if ($method === 'GET') 
		{
			$rutaActual = $request->getUri()->getPath();

			if($rutaActual == "/usuarios" ||$rutaActual == "/productos"||$rutaActual == "/mesas")
			{
				$getToken = true;
			}
			else
			{
				$response = $handler->handle($request);
			}

			
		}

		if($method ==="POST" || $method === 'PUT' || $getToken)
		{

			$header = $request->getHeaderLine('Authorization');
			$token = null;

			if(!empty($header))
			{
				$token = trim(explode("Bearer", $header)[1]);
			}

			try 
			{

				AutentificadorJWT::verificarToken($token);
				$objDelaRespuesta->esValido = true;

			}
			catch (Exception $e) 
			{
				$objDelaRespuesta->excepcion = $e->getMessage();
				$objDelaRespuesta->esValido = false;
			}


			if ($objDelaRespuesta->esValido) 
			{
				$payload = AutentificadorJWT::ObtenerData($token);

				$rutaActual = $request->getUri()->getPath();

				if($rutaActual == "/usuarios" ||$rutaActual == "/productos")
				{

					if ($payload->mail == "socio@socio.com") 
					{
						$response = $handler->handle($request);
					} 
					else 
					{
						$objDelaRespuesta->respuesta = "ERROR. Solo los socios pueden realizar esa acción.";
					}

				}

				if($rutaActual != "/estimado" && strtolower($payload->rol) == strtolower("cliente"))
				{
					$objDelaRespuesta->respuesta = "ERROR. Los clientes no pueden realizas esa accion.";
				}
				else
				{

					$response = $handler->handle($request);
				}
			


			}
			else 
			{
				$objDelaRespuesta->respuesta = "Usuario no registrado";
				
			}


			if ($objDelaRespuesta->respuesta != "") 
			{

				$objDelaRespuestaJson = json_encode($objDelaRespuesta);


				$response->getBody()->write($objDelaRespuestaJson);
				$response = $response->withHeader('Content-Type', 'application/json');
				$response = $response->withStatus(401);

			}
		}


		return $response;
	}

	public function EsAdmin($request, $response, $next)
	{
		$objDelaRespuesta = new stdclass();
		$objDelaRespuesta->respuesta = "";

		$header = $request->getHeaderLine('Authorization');
		$token = trim(explode("Bearer", $header)[1]);


		try {
			AutentificadorJWT::verificarToken($token);
			$objDelaRespuesta->esValido = true;
		} catch (Exception $e) {
			//guardar en un log
			$objDelaRespuesta->excepcion = $e->getMessage();
			$objDelaRespuesta->esValido = false;
		}


		if ($objDelaRespuesta->esValido) {
			$payload = AutentificadorJWT::ObtenerData($token);

			if ($payload->empleo == "Socio") {
				$response = $next($request, $response);
			} else {
				$objDelaRespuesta->respuesta = "ERROR. Solo socios puede alterar la base de datos de productos.";
			}
		} else {
			$objDelaRespuesta->respuesta = "Usuario no registrado";
		}

		if ($objDelaRespuesta->respuesta != "") 
		{
			$nueva = $response->withJson($objDelaRespuesta, 401);
			return $nueva;
		}



		return $response;
	}

	public function VerificarEmpleoParaPendientes($request, $response, $next)
	{


		if ($request->isGet()) {
			$response->getBody()->write('<p>Verifico credenciales</p>');
			$mail = "d";



			if ($mail == "x") {
				$response->getBody()->write("<h3>Bienvenido $mail </h3>");
				$response = $next($request, $response);
			} else {
				$response->getBody()->write('<p>no tenes habilitado el ingreso</p>');
			}

			$response->getBody()->write('<p>vuelvo del verificador de credenciales</p>');
		}
		return $response;
	}
}