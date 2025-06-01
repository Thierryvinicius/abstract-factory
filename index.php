<?php

namespace RefactoringGuru\AbstractFactory\RealWorld;

/**
 * A interface da Fábrica Abstrata declara métodos de criação para cada tipo
 * distinto de produto.
 */
interface TemplateFactory
{
    public function createTitleTemplate(): TitleTemplate;

    public function createPageTemplate(): PageTemplate;

    public function getRenderer(): TemplateRenderer;
}

/**
 * Cada Fábrica Concreta corresponde a uma variante específica (ou família) de
 * produtos.
 *
 * Esta Fábrica Concreta cria templates do tipo Twig.
 */
class TwigTemplateFactory implements TemplateFactory
{
    public function createTitleTemplate(): TitleTemplate
    {
        return new TwigTitleTemplate();
    }

    public function createPageTemplate(): PageTemplate
    {
        return new TwigPageTemplate($this->createTitleTemplate());
    }

    public function getRenderer(): TemplateRenderer
    {
        return new TwigRenderer();
    }
}

/**
 * E esta Fábrica Concreta cria templates do tipo PHPTemplate.
 */
class PHPTemplateFactory implements TemplateFactory
{
    public function createTitleTemplate(): TitleTemplate
    {
        return new PHPTemplateTitleTemplate();
    }

    public function createPageTemplate(): PageTemplate
    {
        return new PHPTemplatePageTemplate($this->createTitleTemplate());
    }

    public function getRenderer(): TemplateRenderer
    {
        return new PHPTemplateRenderer();
    }
}

/**
 * Cada tipo distinto de produto deve ter uma interface separada.
 * Todas as variantes do produto devem seguir a mesma interface.
 *
 * Por exemplo, esta interface de Produto Abstrato descreve o comportamento dos
 * templates de título de página.
 */
interface TitleTemplate
{
    public function getTemplateString(): string;
}

/**
 * Este Produto Concreto fornece templates de título de página no formato Twig.
 */
class TwigTitleTemplate implements TitleTemplate
{
    public function getTemplateString(): string
    {
        return "<h1>{{ title }}</h1>";
    }
}

/**
 * E este Produto Concreto fornece templates de título de página no formato PHPTemplate.
 */
class PHPTemplateTitleTemplate implements TitleTemplate
{
    public function getTemplateString(): string
    {
        return "<h1><?= \$title; ?></h1>";
    }
}

/**
 * Este é outro tipo de Produto Abstrato, que descreve templates de páginas completas.
 */
interface PageTemplate
{
    public function getTemplateString(): string;
}

/**
 * O template de página usa o sub-template de título, então precisamos fornecer
 * uma forma de definir isso no objeto de sub-template.
 * A fábrica abstrata vai vincular o template de página com um template de título da mesma variante.
 */
abstract class BasePageTemplate implements PageTemplate
{
    protected $titleTemplate;

    public function __construct(TitleTemplate $titleTemplate)
    {
        $this->titleTemplate = $titleTemplate;
    }
}

/**
 * Variante Twig dos templates de página completa.
 */
class TwigPageTemplate extends BasePageTemplate
{
    public function getTemplateString(): string
    {
        $renderedTitle = $this->titleTemplate->getTemplateString();

        return <<<HTML
        <div class="page">
            $renderedTitle
            <article class="content">{{ content }}</article>
        </div>
        HTML;
    }
}

/**
 * Variante PHPTemplate dos templates de página completa.
 */
class PHPTemplatePageTemplate extends BasePageTemplate
{
    public function getTemplateString(): string
    {
        $renderedTitle = $this->titleTemplate->getTemplateString();

        return <<<HTML
        <div class="page">
            $renderedTitle
            <article class="content"><?= \$content; ?></article>
        </div>
        HTML;
    }
}

/**
 * O renderizador é responsável por converter uma string de template em código HTML real.
 * Cada renderizador se comporta de forma diferente e espera seu próprio tipo de
 * string de template. Utilizar fábricas ajuda a garantir que o renderizador receba
 * o tipo certo de template.
 */
interface TemplateRenderer
{
    public function render(string $templateString, array $arguments = []): string;
}

/**
 * O renderizador para templates Twig.
 */
class TwigRenderer implements TemplateRenderer
{
    public function render(string $templateString, array $arguments = []): string
    {
        return \Twig::render($templateString, $arguments);
    }
}

/**
 * O renderizador para templates PHPTemplate.
 * Note que esta implementação é bem básica, para não dizer precária.
 * O uso da função `eval` tem muitas implicações de segurança, então use com cuidado em projetos reais.
 */
class PHPTemplateRenderer implements TemplateRenderer
{
    public function render(string $templateString, array $arguments = []): string
    {
        extract($arguments);

        ob_start();
        eval(' ?>' . $templateString . '<?php ');
        $result = ob_get_contents();
        ob_end_clean();

        return $result;
    }
}

/**
 * Código cliente. Note que ele aceita a Fábrica Abstrata como parâmetro,
 * o que permite que o cliente trabalhe com qualquer tipo de fábrica concreta.
 */
class Page
{

    public $title;

    public $content;

    public function __construct($title, $content)
    {
        $this->title = $title;
        $this->content = $content;
    }

    // Veja como o template poderia ser usado na prática. Note que a
    // classe Page não depende de nenhuma classe concreta de template.
    public function render(TemplateFactory $factory): string
    {
        $pageTemplate = $factory->createPageTemplate();

        $renderer = $factory->getRenderer();

        return $renderer->render($pageTemplate->getTemplateString(), [
            'title' => $this->title,
            'content' => $this->content
        ]);
    }
}

/**
 * Agora, em outras partes do app, o código cliente pode aceitar qualquer tipo
 * de objeto fábrica.
 */
$page = new Page('Página de exemplo', 'Este é o corpo do conteúdo.');

echo "Testando renderização real com a fábrica PHPTemplate:\n";
echo $page->render(new PHPTemplateFactory());


// Descomente o trecho abaixo se tiver o Twig instalado:

// echo "Testando renderização com a fábrica Twig:\n";
// echo $page->render(new TwigTemplateFactory());
