<?php

declare(strict_types=1);

use PHP_CodeSniffer\Standards\Squiz\Sniffs\Arrays\ArrayDeclarationSniff;
use SlevomatCodingStandard\Sniffs\Classes\UnusedPrivateElementsSniff;
use PhpCsFixer\Fixer\Operator\UnaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(LineLengthFixer::class);
    $parameters = $containerConfigurator->parameters();
    $parameters->set(
        Option::SETS,
        [
            SetList::PHP_70,
            SetList::PHP_71,
            SetList::CLEAN_CODE,
            SetList::SYMPLIFY,
            SetList::COMMON,
            SetList::PSR_12,
            SetList::DEAD_CODE,
            SetList::DOCTRINE_ANNOTATIONS,
            SetList::ARRAY,
        ]
    );
    $parameters->set(
        Option::PATHS,
        [
            __DIR__ . '/packages',
//            __DIR__ . '/config',
//            __DIR__ . '/ecs.php',
//            __DIR__ . '/bootstrap.php',
//            __DIR__ . '/server.php',
//            __DIR__ . '/public/index.php',
        ]
    );
    $parameters->set(
        Option::EXCLUDE_PATHS,
        ['*/Fixture/*', '*/Source/*', __DIR__ . '/packages/route/src/Benchmark/*']
    );
    $parameters->set(
        Option::SKIP,
        [
            ArrayDeclarationSniff::class => null,
            BlankLineAfterOpeningTagFixer::class => null,
            UnaryOperatorSpacesFixer::class => null,
            UnusedPrivateElementsSniff::class . '.UnusedMethod' => null,
        ]
    );
};
