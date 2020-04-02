<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* modules/contrib/linkicon/templates/linkicon.html.twig */
class __TwigTemplate_60d1811f2689627ba02169d7f44343d95b488745e6bb346f23293607a908113f extends \Twig\Template
{
    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $tags = ["set" => 18, "if" => 24, "spaceless" => 39, "for" => 42];
        $filters = ["clean_class" => 21, "escape" => 40];
        $functions = [];

        try {
            $this->sandbox->checkSecurity(
                ['set', 'if', 'spaceless', 'for'],
                ['clean_class', 'escape'],
                []
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->getSourceContext());

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        // line 18
        $context["wrapper_classes"] = [0 => "item-list", 1 => "item-list--linkicon", 2 => (($this->getAttribute(        // line 21
($context["settings"] ?? null), "wrapper_class", [])) ? (\Drupal\Component\Utility\Html::getClass($this->sandbox->ensureToStringAllowed($this->getAttribute(($context["settings"] ?? null), "wrapper_class", [])))) : (""))];
        // line 24
        if ($this->getAttribute(($context["settings"] ?? null), "load", [])) {
            // line 25
            echo "  ";
            // line 26
            $context["classes"] = [0 => (( !$this->getAttribute(            // line 27
($context["settings"] ?? null), "vertical", [])) ? ("linkicon--inline") : ("")), 1 => (($this->getAttribute(            // line 28
($context["settings"] ?? null), "color", [])) ? ("linkicon--color") : ("")), 2 => (($this->getAttribute(            // line 29
($context["settings"] ?? null), "no_text", [])) ? ("linkicon--no-text") : ("")), 3 => (($this->getAttribute(            // line 30
($context["settings"] ?? null), "color", [])) ? (("linkicon--" . \Drupal\Component\Utility\Html::getClass($this->sandbox->ensureToStringAllowed($this->getAttribute(($context["settings"] ?? null), "color", []))))) : ("")), 4 => (($this->getAttribute(            // line 31
($context["settings"] ?? null), "style", [])) ? (("linkicon--" . \Drupal\Component\Utility\Html::getClass($this->sandbox->ensureToStringAllowed($this->getAttribute(($context["settings"] ?? null), "style", []))))) : ("")), 5 => ((($this->getAttribute(            // line 32
($context["settings"] ?? null), "size", []) &&  !$this->getAttribute(($context["settings"] ?? null), "_preview", []))) ? (("linkicon--" . \Drupal\Component\Utility\Html::getClass($this->sandbox->ensureToStringAllowed($this->getAttribute(($context["settings"] ?? null), "size", []))))) : ("")), 6 => (($this->getAttribute(            // line 33
($context["settings"] ?? null), "tooltip", [])) ? ("linkicon--tooltip") : ("")), 7 => (($this->getAttribute(            // line 34
($context["settings"] ?? null), "position", [])) ? (("linkicon--" . \Drupal\Component\Utility\Html::getClass($this->sandbox->ensureToStringAllowed($this->getAttribute(($context["settings"] ?? null), "position", []))))) : (""))];
        }
        // line 38
        echo "
";
        // line 39
        ob_start(function () { return ''; });
        // line 40
        echo "<div";
        echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($this->getAttribute(($context["wrapper_attributes"] ?? null), "addClass", [0 => ($context["wrapper_classes"] ?? null)], "method")), "html", null, true);
        echo ">
  <ul";
        // line 41
        echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($this->getAttribute(($context["attributes"] ?? null), "addClass", [0 => "linkicon", 1 => ($context["classes"] ?? null)], "method")), "html", null, true);
        echo ">";
        // line 42
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["items"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
            // line 43
            echo "<li>
        ";
            // line 44
            echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($context["item"]), "html", null, true);
            echo "
      </li>";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 47
        echo "</ul>
</div>
";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    public function getTemplateName()
    {
        return "modules/contrib/linkicon/templates/linkicon.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  101 => 47,  93 => 44,  90 => 43,  86 => 42,  83 => 41,  78 => 40,  76 => 39,  73 => 38,  70 => 34,  69 => 33,  68 => 32,  67 => 31,  66 => 30,  65 => 29,  64 => 28,  63 => 27,  62 => 26,  60 => 25,  58 => 24,  56 => 21,  55 => 18,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "modules/contrib/linkicon/templates/linkicon.html.twig", "C:\\wamp64\\www\\drupal-am\\web\\modules\\contrib\\linkicon\\templates\\linkicon.html.twig");
    }
}
