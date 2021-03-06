<?php declare(strict_types = 1);

namespace PHPStan\Type\Php;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\ConstantScalarType;
use PHPStan\Type\DynamicFunctionReturnTypeExtension;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;

class SprintfFunctionDynamicReturnTypeExtension implements DynamicFunctionReturnTypeExtension
{

	public function isFunctionSupported(FunctionReflection $functionReflection): bool
	{
		return $functionReflection->getName() === 'sprintf';
	}

	public function getTypeFromFunctionCall(
		FunctionReflection $functionReflection,
		FuncCall $functionCall,
		Scope $scope
	): Type
	{
		$values = [];
		$returnType = new StringType();
		foreach ($functionCall->args as $arg) {
			$argType = $scope->getType($arg->value);
			if (!$argType instanceof ConstantScalarType) {
				return $returnType;
			}

			$values[] = $argType->getValue();
		}

		if (count($values) === 0) {
			return ParametersAcceptorSelector::selectSingle($functionReflection->getVariants())->getReturnType();
		}

		$format = array_shift($values);
		if (!is_string($format)) {
			return ParametersAcceptorSelector::selectSingle($functionReflection->getVariants())->getReturnType();
		}

		try {
			$value = @sprintf($format, ...$values);
		} catch (\Throwable $e) {
			return $returnType;
		}

		return $scope->getTypeFromValue($value);
	}

}
