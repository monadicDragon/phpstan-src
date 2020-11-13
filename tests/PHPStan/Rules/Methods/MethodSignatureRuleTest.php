<?php declare(strict_types = 1);

namespace PHPStan\Rules\Methods;

use PHPStan\Php\PhpVersion;
use const PHP_VERSION_ID;

/**
 * @extends \PHPStan\Testing\RuleTestCase<OverridingMethodRule>
 */
class MethodSignatureRuleTest extends \PHPStan\Testing\RuleTestCase
{

	/** @var bool */
	private $reportMaybes;

	/** @var bool */
	private $reportStatic;

	protected function getRule(): \PHPStan\Rules\Rule
	{
		return new OverridingMethodRule(
			new PhpVersion(PHP_VERSION_ID),
			new MethodSignatureRule($this->reportMaybes, $this->reportStatic),
			true
		);
	}

	public function testReturnTypeRule(): void
	{
		$this->reportMaybes = true;
		$this->reportStatic = true;
		$this->analyse(
			[
				__DIR__ . '/data/method-signature.php',
			],
			[
				[
					'Parameter #1 $animal (MethodSignature\Dog) of method MethodSignature\SubClass::parameterTypeTest4() should be contravariant with parameter $animal (MethodSignature\Animal) of method MethodSignature\BaseClass::parameterTypeTest4()',
					298,
				],
				[
					'Parameter #1 $animal (MethodSignature\Dog) of method MethodSignature\SubClass::parameterTypeTest4() should be contravariant with parameter $animal (MethodSignature\Animal) of method MethodSignature\BaseInterface::parameterTypeTest4()',
					298,
				],
				[
					'Parameter #1 $animal (MethodSignature\Dog) of method MethodSignature\SubClass::parameterTypeTest5() should be compatible with parameter $animal (MethodSignature\Cat) of method MethodSignature\BaseClass::parameterTypeTest5()',
					305,
				],
				[
					'Parameter #1 $animal (MethodSignature\Dog) of method MethodSignature\SubClass::parameterTypeTest5() should be compatible with parameter $animal (MethodSignature\Cat) of method MethodSignature\BaseInterface::parameterTypeTest5()',
					305,
				],
				[
					'Return type (MethodSignature\Animal) of method MethodSignature\SubClass::returnTypeTest4() should be covariant with return type (MethodSignature\Dog) of method MethodSignature\BaseClass::returnTypeTest4()',
					351,
				],
				[
					'Return type (MethodSignature\Animal) of method MethodSignature\SubClass::returnTypeTest4() should be covariant with return type (MethodSignature\Dog) of method MethodSignature\BaseInterface::returnTypeTest4()',
					351,
				],
				[
					'Return type (MethodSignature\Cat) of method MethodSignature\SubClass::returnTypeTest5() should be compatible with return type (MethodSignature\Dog) of method MethodSignature\BaseClass::returnTypeTest5()',
					358,
				],
				[
					'Return type (MethodSignature\Cat) of method MethodSignature\SubClass::returnTypeTest5() should be compatible with return type (MethodSignature\Dog) of method MethodSignature\BaseInterface::returnTypeTest5()',
					358,
				],
				[
					'Parameter #1 $node (PhpParser\Node\Expr\StaticCall) of method MethodSignature\Rule::processNode() should be contravariant with parameter $node (PhpParser\Node) of method MethodSignature\GenericRule<PhpParser\Node>::processNode()',
					454,
				],
			]
		);
	}

	public function testReturnTypeRuleTrait(): void
	{
		$this->reportMaybes = true;
		$this->reportStatic = true;
		$this->analyse(
			[
				__DIR__ . '/data/method-signature-trait.php',
			],
			[
				[
					'Parameter #1 $animal (MethodSignature\Dog) of method MethodSignature\SubClassUsingTrait::parameterTypeTest4() should be contravariant with parameter $animal (MethodSignature\Animal) of method MethodSignature\BaseClass::parameterTypeTest4()',
					43,
				],
				[
					'Parameter #1 $animal (MethodSignature\Dog) of method MethodSignature\SubClassUsingTrait::parameterTypeTest4() should be contravariant with parameter $animal (MethodSignature\Animal) of method MethodSignature\BaseInterface::parameterTypeTest4()',
					43,
				],
				[
					'Parameter #1 $animal (MethodSignature\Dog) of method MethodSignature\SubClassUsingTrait::parameterTypeTest5() should be compatible with parameter $animal (MethodSignature\Cat) of method MethodSignature\BaseClass::parameterTypeTest5()',
					50,
				],
				[
					'Parameter #1 $animal (MethodSignature\Dog) of method MethodSignature\SubClassUsingTrait::parameterTypeTest5() should be compatible with parameter $animal (MethodSignature\Cat) of method MethodSignature\BaseInterface::parameterTypeTest5()',
					50,
				],
				[
					'Return type (MethodSignature\Animal) of method MethodSignature\SubClassUsingTrait::returnTypeTest4() should be covariant with return type (MethodSignature\Dog) of method MethodSignature\BaseClass::returnTypeTest4()',
					96,
				],
				[
					'Return type (MethodSignature\Animal) of method MethodSignature\SubClassUsingTrait::returnTypeTest4() should be covariant with return type (MethodSignature\Dog) of method MethodSignature\BaseInterface::returnTypeTest4()',
					96,
				],
				[
					'Return type (MethodSignature\Cat) of method MethodSignature\SubClassUsingTrait::returnTypeTest5() should be compatible with return type (MethodSignature\Dog) of method MethodSignature\BaseClass::returnTypeTest5()',
					103,
				],
				[
					'Return type (MethodSignature\Cat) of method MethodSignature\SubClassUsingTrait::returnTypeTest5() should be compatible with return type (MethodSignature\Dog) of method MethodSignature\BaseInterface::returnTypeTest5()',
					103,
				],
			]
		);
	}

	public function testReturnTypeRuleTraitWithoutMaybes(): void
	{
		$this->reportMaybes = false;
		$this->reportStatic = true;
		$this->analyse(
			[
				__DIR__ . '/data/method-signature-trait.php',
			],
			[
				[
					'Parameter #1 $animal (MethodSignature\Dog) of method MethodSignature\SubClassUsingTrait::parameterTypeTest5() should be compatible with parameter $animal (MethodSignature\Cat) of method MethodSignature\BaseClass::parameterTypeTest5()',
					50,
				],
				[
					'Parameter #1 $animal (MethodSignature\Dog) of method MethodSignature\SubClassUsingTrait::parameterTypeTest5() should be compatible with parameter $animal (MethodSignature\Cat) of method MethodSignature\BaseInterface::parameterTypeTest5()',
					50,
				],
				[
					'Return type (MethodSignature\Cat) of method MethodSignature\SubClassUsingTrait::returnTypeTest5() should be compatible with return type (MethodSignature\Dog) of method MethodSignature\BaseClass::returnTypeTest5()',
					103,
				],
				[
					'Return type (MethodSignature\Cat) of method MethodSignature\SubClassUsingTrait::returnTypeTest5() should be compatible with return type (MethodSignature\Dog) of method MethodSignature\BaseInterface::returnTypeTest5()',
					103,
				],
			]
		);
	}

	public function testReturnTypeRuleWithoutMaybes(): void
	{
		$this->reportMaybes = false;
		$this->reportStatic = true;
		$this->analyse(
			[
				__DIR__ . '/data/method-signature.php',
			],
			[
				[
					'Parameter #1 $animal (MethodSignature\Dog) of method MethodSignature\SubClass::parameterTypeTest5() should be compatible with parameter $animal (MethodSignature\Cat) of method MethodSignature\BaseClass::parameterTypeTest5()',
					305,
				],
				[
					'Parameter #1 $animal (MethodSignature\Dog) of method MethodSignature\SubClass::parameterTypeTest5() should be compatible with parameter $animal (MethodSignature\Cat) of method MethodSignature\BaseInterface::parameterTypeTest5()',
					305,
				],
				[
					'Return type (MethodSignature\Cat) of method MethodSignature\SubClass::returnTypeTest5() should be compatible with return type (MethodSignature\Dog) of method MethodSignature\BaseClass::returnTypeTest5()',
					358,
				],
				[
					'Return type (MethodSignature\Cat) of method MethodSignature\SubClass::returnTypeTest5() should be compatible with return type (MethodSignature\Dog) of method MethodSignature\BaseInterface::returnTypeTest5()',
					358,
				],
			]
		);
	}

	public function testRuleWithoutStaticMethods(): void
	{
		$this->reportMaybes = true;
		$this->reportStatic = false;
		$this->analyse([__DIR__ . '/data/method-signature-static.php'], []);
	}

	public function testRuleWithStaticMethods(): void
	{
		$this->reportMaybes = true;
		$this->reportStatic = true;
		$this->analyse([__DIR__ . '/data/method-signature-static.php'], [
			[
				'Parameter #1 $value (string) of method MethodSignature\Bar::doFoo() should be compatible with parameter $value (int) of method MethodSignature\Foo::doFoo()',
				24,
			],
		]);
	}

	public function testBug2950(): void
	{
		$this->reportMaybes = true;
		$this->reportStatic = true;
		$this->analyse([__DIR__ . '/data/bug-2950.php'], []);
	}

	public function testBug3997(): void
	{
		$this->reportMaybes = true;
		$this->reportStatic = true;
		$this->analyse([__DIR__ . '/data/bug-3997.php'], [
			[
				'Return type (string) of method Bug3997\Ipsum::count() should be compatible with return type (int) of method Countable::count()',
				59,
			],
		]);
	}

	public function testBug4003(): void
	{
		if (PHP_VERSION_ID < 70200 && !self::$useStaticReflectionProvider) {
			$this->markTestSkipped('Test requires PHP 7.2 or later.');
		}
		$this->reportMaybes = true;
		$this->reportStatic = true;
		$this->analyse([__DIR__ . '/data/bug-4003.php'], [
			[
				'Return type (string) of method Bug4003\Baz::foo() should be compatible with return type (int) of method Bug4003\Boo::foo()',
				15,
			],
			[
				'Parameter #1 $test (string) of method Bug4003\Ipsum::doFoo() should be compatible with parameter $test (int) of method Bug4003\Lorem::doFoo()',
				38,
			],
		]);
	}

	public function testBug4017(): void
	{
		if (PHP_VERSION_ID < 70400) {
			$this->markTestSkipped('Test requires PHP 7.4.');
		}
		$this->reportMaybes = true;
		$this->reportStatic = true;
		$this->analyse([__DIR__ . '/data/bug-4017.php'], []);
	}

	public function testBug4017Two(): void
	{
		$this->reportMaybes = true;
		$this->reportStatic = true;
		$this->analyse([__DIR__ . '/data/bug-4017_2.php'], [
			[
				'Parameter #1 $a (Bug4017_2\Foo) of method Bug4017_2\Lorem::doFoo() should be compatible with parameter $a (stdClass) of method Bug4017_2\Bar<stdClass>::doFoo()',
				51,
			],
		]);
	}

	public function testBug4017Three(): void
	{
		$this->reportMaybes = true;
		$this->reportStatic = true;
		$this->analyse([__DIR__ . '/data/bug-4017_3.php'], [
			[
				'Parameter #1 $a (T of stdClass) of method Bug4017_3\Lorem::doFoo() should be compatible with parameter $a (Bug4017_3\Foo) of method Bug4017_3\Bar::doFoo()',
				45,
			],
		]);
	}

	public function testBug4023(): void
	{
		$this->reportMaybes = true;
		$this->reportStatic = true;
		$this->analyse([__DIR__ . '/data/bug-4023.php'], []);
	}

	public function testBug4006(): void
	{
		$this->reportMaybes = true;
		$this->reportStatic = true;
		$this->analyse([__DIR__ . '/data/bug-4006.php'], []);
	}

	public function testBug4084(): void
	{
		$this->reportMaybes = true;
		$this->reportStatic = true;
		$this->analyse([__DIR__ . '/data/bug-4084.php'], []);
	}

}
