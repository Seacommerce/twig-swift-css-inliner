<?php

declare(strict_types=1);

namespace Seacommerce\TwigSwiftCssInliner;

use Swift_Message;
use Symfony\Component\DomCrawler\Crawler;
use Twig_Environment;
use Twig_TemplateWrapper;

class CssInliner
{
    /** @var string */
    private $textBlockName = 'text';

    /** @var string */
    private $htmlBlockName = 'html';

    /** @var string */
    private $subjectBlockName = 'subject';

    /** @var string */
    private $stylesBlockName = 'styles';


    /** @var Twig_Environment */
    private $twigEnvironment;

    /**
     * CssInliner constructor.
     * @param Twig_Environment $twigEnvironment
     */
    public function __construct(Twig_Environment $twigEnvironment)
    {
        $this->twigEnvironment = $twigEnvironment;
    }

    /**
     * @return string
     */
    public function getTextBlockName(): string
    {
        return $this->textBlockName;
    }

    /**
     * @param string $textBlockName
     * @return CssInliner
     */
    public function setTextBlockName(string $textBlockName): CssInliner
    {
        $this->textBlockName = $textBlockName;
        return $this;
    }

    /**
     * @return string
     */
    public function getHtmlBlockName(): string
    {
        return $this->htmlBlockName;
    }

    /**
     * @param string $htmlBlockName
     * @return CssInliner
     */
    public function setHtmlBlockName(string $htmlBlockName): CssInliner
    {
        $this->htmlBlockName = $htmlBlockName;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubjectBlockName(): string
    {
        return $this->subjectBlockName;
    }

    /**
     * @param string $subjectBlockName
     * @return CssInliner
     */
    public function setSubjectBlockName(string $subjectBlockName): CssInliner
    {
        $this->subjectBlockName = $subjectBlockName;
        return $this;
    }

    /**
     * @return string
     */
    public function getStylesBlockName(): string
    {
        return $this->stylesBlockName;
    }

    /**
     * @param string $stylesBlockName
     * @return CssInliner
     */
    public function setStylesBlockName(string $stylesBlockName): CssInliner
    {
        $this->stylesBlockName = $stylesBlockName;
        return $this;
    }

    /**
     * @return Twig_Environment
     */
    public function getTwigEnvironment(): Twig_Environment
    {
        return $this->twigEnvironment;
    }

    /**
     *
     * @param string $twigTemplatePath
     * @param array $viewData
     * @param string ...$additionalCss
     * @return Swift_Message
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function createEmailFromTemplateFile(string $twigTemplatePath, array $viewData, string ...$additionalCss): Swift_Message
    {
        $template = $this->twigEnvironment->load($twigTemplatePath);
        return $this->createEmailFromTemplate($template, $viewData, $additionalCss);
    }

    /**
     * @param Twig_TemplateWrapper $twigTemplate
     * @param array $viewData
     * @param string|string[] ...$additionalCss
     * @return Swift_Message
     * @throws \Throwable
     */
    public function createEmailFromTemplate(Twig_TemplateWrapper $twigTemplate, array $viewData, ...$additionalCss): Swift_Message
    {
        $additionalCssArray = [];
        foreach ($additionalCss as $a) {
            if (is_string($a)) {
                $additionalCssArray[] = $a;
            } else if (is_array($a)) {
                $additionalCssArray = array_merge($additionalCssArray, $a);
            }
        }
        $blocks = $this->extractTemplateBlocks($twigTemplate, $viewData);
        $additionalCss = self::getAdditionalCss($additionalCssArray);
        $allCss = $additionalCss;
        if (isset($blocks['css'])) {
            $allCss .= PHP_EOL . $blocks['css'] ?? null;
        }
        $message = new Swift_Message();
        if (isset($blocks['html'])) {
            $bodyHtml = !empty($allCss) ? self::inlineCssIntoHtml($blocks['html'], $allCss) : $blocks['html'];
            $message->setBody($bodyHtml, 'text/html');
            if (isset($blocks['text'])) {
                $message->addPart($blocks['text'], 'text/plain');
            }
        } else {
            $message->setBody($blocks['text'], 'text/plain');
        }
        if (isset($blocks['subject'])) {
            $message->setSubject($blocks['subject']);
        }

        return $message;
    }

    /**
     * @param string $html
     * @param null|string ...$css
     * @return string
     */
    public static function inlineCssIntoHtml(string $html, ?string ...$css): string
    {
        $css = implode('', $css);
        $emogrifier = new \Pelago\Emogrifier($html, $css);
        $inlined = $emogrifier->emogrify();
        return $inlined;
    }


    /**
     * @param Twig_TemplateWrapper $template
     * @param $viewData
     * @return array
     * @throws \Throwable
     */
    public function extractTemplateBlocks(Twig_TemplateWrapper $template, $viewData): array
    {
        $viewData = $this->twigEnvironment->mergeGlobals($viewData);
        $subject = $template->hasBlock($this->subjectBlockName, $viewData) ? $template->renderBlock($this->subjectBlockName, $viewData) : null;
        $html = $template->hasBlock($this->htmlBlockName, $viewData) ? $template->renderBlock($this->htmlBlockName, $viewData) : null;
        $text = $template->hasBlock($this->textBlockName, $viewData) ? $template->renderBlock($this->textBlockName, $viewData) : null;
        $style = $template->hasBlock($this->stylesBlockName, $viewData) ? $template->renderBlock($this->stylesBlockName, $viewData) : null;

        if(!empty($subject)) {
            $subject = trim($subject);
        }

        $crawler = new Crawler($style);
        $elements = $crawler->filter('style');
        $css = '';
        if ($elements->count() !== 0) {
            foreach ($elements as $element) {
                $css .= $element->textContent;
            }
        }

        return ['subject' => $subject, 'css' => $css, 'text' => $text, 'html' => $html];
    }

    private static function getAdditionalCss(array $additionalCss): ?string
    {
        if (empty($additionalCss)) {
            return null;
        }

        $all = '';
        foreach ($additionalCss as $a) {
            if (file_exists($a)) {
                $all .= file_get_contents($a);
            } else {
                $all .= $a;
            }
        }
        return $all;
    }


}