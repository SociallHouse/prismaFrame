<?php


namespace SociallHouse\prismaFrame\controller;


use SociallHouse\prismaFrame\error\runtime\RuntimeError;

final class Method
{

	/** @var string */
	private $name;

	/** @var MethodParameter[] */
	private $parameters;

	/** @var array */
	private $httpMethods = [];

	/** @var Controller */
	private $controller;

	/**
	 * Method constructor.
	 * @param string $name
	 * @param array $parameters
	 * @param array $httpMethods
	 * @param Controller $controller
	 */
	public function __construct(string $name, array $parameters, array $httpMethods, Controller $controller){
		$this->name = $name;
		$this->parameters = $parameters;
		foreach ($httpMethods as $method){
			$this->httpMethods[$method] = true; // чтобы можно было потом ускоренно проверять через isset
		}
		$this->controller = $controller;
	}

	public function isHttpMethodSupported(string $httpMethodName){
		return isset($this->httpMethods[$httpMethodName]);
	}

	public function invoke(array $args): array{
		$values = [];
		foreach ($this->parameters as $name => $param){
			if($param->required && !isset($args[$name])){
				throw RuntimeError::BAD_INPUT("Parameter \"{$name}\" is required");
			}
			if($param->validate($args[$name], $result)){
				$values[] = $result;
			}else{

			}
		}

		return $this->controller->{$this->name}(...$args);
	}

}