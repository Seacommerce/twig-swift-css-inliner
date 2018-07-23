<?php

declare(strict_types=1);

namespace Seacommerce\TwigSwiftCssInliner\Tests;


use PHPUnit\Framework\TestCase;
use Seacommerce\TwigSwiftCssInliner\CssInliner;
use Twig_Loader_Filesystem;

class InlinerTest extends TestCase
{
    public function testComplete()
    {
        $loader = new Twig_Loader_Filesystem(__DIR__ . '/Fixtures/templates/');
        $twig = new \Twig_Environment($loader);
        $inliner = new CssInliner($twig);
        $viewData = [];
        $message = $inliner->createEmailFromTemplateFile('all-blocks.html.twig', $viewData);
        $this->assertStringEqualsFile(__DIR__ . '/Fixtures/templates/all-blocks.expected.html', $message->getBody());
    }
}