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

/* themes/drupalam/templates/system/page--front.html.twig */
class __TwigTemplate_ce98a82cc6554503f438c2da14deada9f9c9b22c32c75bc1d2ee07b62238c312 extends \Twig\Template
{
    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = [
            'navbar' => [$this, 'block_navbar'],
            'main' => [$this, 'block_main'],
            'header' => [$this, 'block_header'],
            'sidebar_first' => [$this, 'block_sidebar_first'],
            'highlighted' => [$this, 'block_highlighted'],
            'breadcrumb' => [$this, 'block_breadcrumb'],
            'action_links' => [$this, 'block_action_links'],
            'help' => [$this, 'block_help'],
            'content' => [$this, 'block_content'],
            'sidebar_second' => [$this, 'block_sidebar_second'],
            'footer' => [$this, 'block_footer'],
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $tags = ["set" => 59, "if" => 62, "block" => 63];
        $filters = ["escape" => 71, "t" => 81];
        $functions = [];

        try {
            $this->sandbox->checkSecurity(
                ['set', 'if', 'block'],
                ['escape', 't'],
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
        // line 59
        $context["container"] = ((($this->getAttribute($this->getAttribute(($context["theme"] ?? null), "settings", []), "fluid_container", []) || ($context["is_front"] ?? null))) ? ("container-fluid") : ("container"));
        // line 60
        $context["nav_container"] = "container";
        // line 62
        if (($this->getAttribute(($context["page"] ?? null), "navigation", []) || $this->getAttribute(($context["page"] ?? null), "navigation_collapsible", []))) {
            // line 63
            echo "  ";
            $this->displayBlock('navbar', $context, $blocks);
        }
        // line 111
        echo "
";
        // line 113
        $this->displayBlock('main', $context, $blocks);
        // line 192
        echo "
";
        // line 193
        if ($this->getAttribute(($context["page"] ?? null), "footer", [])) {
            // line 194
            echo "  ";
            $this->displayBlock('footer', $context, $blocks);
        }
    }

    // line 63
    public function block_navbar($context, array $blocks = [])
    {
        // line 64
        echo "    ";
        // line 65
        $context["navbar_classes"] = [0 => "navbar", 1 => (($this->getAttribute($this->getAttribute(        // line 67
($context["theme"] ?? null), "settings", []), "navbar_inverse", [])) ? ("navbar-inverse") : ("navbar-default")), 2 => (($this->getAttribute($this->getAttribute(        // line 68
($context["theme"] ?? null), "settings", []), "navbar_position", [])) ? (("navbar-" . $this->sandbox->ensureToStringAllowed($this->getAttribute($this->getAttribute(($context["theme"] ?? null), "settings", []), "navbar_position", [])))) : (""))];
        // line 71
        echo "    <header";
        echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($this->getAttribute(($context["navbar_attributes"] ?? null), "addClass", [0 => ($context["navbar_classes"] ?? null)], "method")), "html", null, true);
        echo " id=\"navbar\" role=\"banner\">
      ";
        // line 72
        if ( !$this->getAttribute(($context["navbar_attributes"] ?? null), "hasClass", [0 => ($context["container"] ?? null)], "method")) {
            // line 73
            echo "        <div class=\"";
            echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["nav_container"] ?? null)), "html", null, true);
            echo "\">
      ";
        }
        // line 75
        echo "      <div class=\"row\">
        <div class=\"navbar-header col-sm-2\">
          ";
        // line 77
        echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($this->getAttribute(($context["page"] ?? null), "navigation", [])), "html", null, true);
        echo "
          ";
        // line 79
        echo "          ";
        if ($this->getAttribute(($context["page"] ?? null), "navigation_collapsible", [])) {
            // line 80
            echo "            <button type=\"button\" class=\"navbar-toggle\" data-toggle=\"collapse\" data-target=\"#navbar-collapse\">
              <span class=\"sr-only\">";
            // line 81
            echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->renderVar(t("Toggle navigation"));
            echo "</span>
              <span class=\"icon-bar\"></span>
              <span class=\"icon-bar\"></span>
              <span class=\"icon-bar\"></span>
            </button>
          ";
        }
        // line 87
        echo "        </div>

        ";
        // line 90
        echo "        <div class=\"col-md-8 col-sm-8\">
        ";
        // line 91
        if ($this->getAttribute(($context["page"] ?? null), "navigation_collapsible", [])) {
            // line 92
            echo "          <div id=\"navbar-collapse\" class=\"navbar-collapse collapse\">
            ";
            // line 93
            echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($this->getAttribute(($context["page"] ?? null), "navigation_collapsible", [])), "html", null, true);
            echo "
          </div>
        ";
        }
        // line 96
        echo "        </div>
        
        ";
        // line 99
        echo "        ";
        if ($this->getAttribute(($context["page"] ?? null), "top_right", [])) {
            // line 100
            echo "          <div class=\"col-md-2 col-sm-2 hidden-xs\">
            ";
            // line 101
            echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($this->getAttribute(($context["page"] ?? null), "top_right", [])), "html", null, true);
            echo "
          </div>
        ";
        }
        // line 104
        echo "      </div>
      ";
        // line 105
        if ( !$this->getAttribute(($context["navbar_attributes"] ?? null), "hasClass", [0 => ($context["container"] ?? null)], "method")) {
            // line 106
            echo "        </div>
      ";
        }
        // line 108
        echo "    </header>
  ";
    }

    // line 113
    public function block_main($context, array $blocks = [])
    {
        // line 114
        echo "  <div role=\"main\" class=\"main-container ";
        echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["container"] ?? null)), "html", null, true);
        echo " js-quickedit-main-content\">
    <div class=\"row\">

      ";
        // line 118
        echo "      ";
        if ($this->getAttribute(($context["page"] ?? null), "header", [])) {
            // line 119
            echo "        ";
            $this->displayBlock('header', $context, $blocks);
            // line 124
            echo "      ";
        }
        // line 125
        echo "
      ";
        // line 127
        echo "      ";
        if ($this->getAttribute(($context["page"] ?? null), "sidebar_first", [])) {
            // line 128
            echo "        ";
            $this->displayBlock('sidebar_first', $context, $blocks);
            // line 133
            echo "      ";
        }
        // line 134
        echo "
      ";
        // line 136
        echo "      ";
        // line 137
        $context["content_classes"] = [0 => ((($this->getAttribute(        // line 138
($context["page"] ?? null), "sidebar_first", []) && $this->getAttribute(($context["page"] ?? null), "sidebar_second", []))) ? ("col-sm-6") : ("")), 1 => ((($this->getAttribute(        // line 139
($context["page"] ?? null), "sidebar_first", []) && twig_test_empty($this->getAttribute(($context["page"] ?? null), "sidebar_second", [])))) ? ("col-sm-9") : ("")), 2 => ((($this->getAttribute(        // line 140
($context["page"] ?? null), "sidebar_second", []) && twig_test_empty($this->getAttribute(($context["page"] ?? null), "sidebar_first", [])))) ? ("col-sm-9") : ("")), 3 => (((twig_test_empty($this->getAttribute(        // line 141
($context["page"] ?? null), "sidebar_first", [])) && twig_test_empty($this->getAttribute(($context["page"] ?? null), "sidebar_second", [])))) ? ("col-sm-12") : (""))];
        // line 144
        echo "      <section";
        echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($this->getAttribute(($context["content_attributes"] ?? null), "addClass", [0 => ($context["content_classes"] ?? null)], "method")), "html", null, true);
        echo ">

        ";
        // line 147
        echo "        ";
        if ($this->getAttribute(($context["page"] ?? null), "highlighted", [])) {
            // line 148
            echo "          ";
            $this->displayBlock('highlighted', $context, $blocks);
            // line 151
            echo "        ";
        }
        // line 152
        echo "
        ";
        // line 154
        echo "        ";
        if (($context["breadcrumb"] ?? null)) {
            // line 155
            echo "          ";
            $this->displayBlock('breadcrumb', $context, $blocks);
            // line 158
            echo "        ";
        }
        // line 159
        echo "
        ";
        // line 161
        echo "        ";
        if (($context["action_links"] ?? null)) {
            // line 162
            echo "          ";
            $this->displayBlock('action_links', $context, $blocks);
            // line 165
            echo "        ";
        }
        // line 166
        echo "
        ";
        // line 168
        echo "        ";
        if ($this->getAttribute(($context["page"] ?? null), "help", [])) {
            // line 169
            echo "          ";
            $this->displayBlock('help', $context, $blocks);
            // line 172
            echo "        ";
        }
        // line 173
        echo "
        ";
        // line 175
        echo "        ";
        $this->displayBlock('content', $context, $blocks);
        // line 179
        echo "      </section>

      ";
        // line 182
        echo "      ";
        if ($this->getAttribute(($context["page"] ?? null), "sidebar_second", [])) {
            // line 183
            echo "        ";
            $this->displayBlock('sidebar_second', $context, $blocks);
            // line 188
            echo "      ";
        }
        // line 189
        echo "    </div>
  </div>
";
    }

    // line 119
    public function block_header($context, array $blocks = [])
    {
        // line 120
        echo "          <div class=\"col-sm-12\" role=\"heading\">
            ";
        // line 121
        echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($this->getAttribute(($context["page"] ?? null), "header", [])), "html", null, true);
        echo "
          </div>
        ";
    }

    // line 128
    public function block_sidebar_first($context, array $blocks = [])
    {
        // line 129
        echo "          <aside class=\"col-sm-3\" role=\"complementary\">
            ";
        // line 130
        echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($this->getAttribute(($context["page"] ?? null), "sidebar_first", [])), "html", null, true);
        echo "
          </aside>
        ";
    }

    // line 148
    public function block_highlighted($context, array $blocks = [])
    {
        // line 149
        echo "            <div class=\"highlighted\">";
        echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($this->getAttribute(($context["page"] ?? null), "highlighted", [])), "html", null, true);
        echo "</div>
          ";
    }

    // line 155
    public function block_breadcrumb($context, array $blocks = [])
    {
        // line 156
        echo "            ";
        echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["breadcrumb"] ?? null)), "html", null, true);
        echo "
          ";
    }

    // line 162
    public function block_action_links($context, array $blocks = [])
    {
        // line 163
        echo "            <ul class=\"action-links\">";
        echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["action_links"] ?? null)), "html", null, true);
        echo "</ul>
          ";
    }

    // line 169
    public function block_help($context, array $blocks = [])
    {
        // line 170
        echo "            ";
        echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($this->getAttribute(($context["page"] ?? null), "help", [])), "html", null, true);
        echo "
          ";
    }

    // line 175
    public function block_content($context, array $blocks = [])
    {
        // line 176
        echo "          <a id=\"main-content\"></a>
          ";
        // line 177
        echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($this->getAttribute(($context["page"] ?? null), "content", [])), "html", null, true);
        echo "
        ";
    }

    // line 183
    public function block_sidebar_second($context, array $blocks = [])
    {
        // line 184
        echo "          <aside class=\"col-sm-3\" role=\"complementary\">
            ";
        // line 185
        echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($this->getAttribute(($context["page"] ?? null), "sidebar_second", [])), "html", null, true);
        echo "
          </aside>
        ";
    }

    // line 194
    public function block_footer($context, array $blocks = [])
    {
        // line 195
        echo "    <footer class=\"footer\" role=\"contentinfo\">
      ";
        // line 196
        echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($this->getAttribute(($context["page"] ?? null), "footer", [])), "html", null, true);
        echo "
    </footer>
  ";
    }

    public function getTemplateName()
    {
        return "themes/drupalam/templates/system/page--front.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  400 => 196,  397 => 195,  394 => 194,  387 => 185,  384 => 184,  381 => 183,  375 => 177,  372 => 176,  369 => 175,  362 => 170,  359 => 169,  352 => 163,  349 => 162,  342 => 156,  339 => 155,  332 => 149,  329 => 148,  322 => 130,  319 => 129,  316 => 128,  309 => 121,  306 => 120,  303 => 119,  297 => 189,  294 => 188,  291 => 183,  288 => 182,  284 => 179,  281 => 175,  278 => 173,  275 => 172,  272 => 169,  269 => 168,  266 => 166,  263 => 165,  260 => 162,  257 => 161,  254 => 159,  251 => 158,  248 => 155,  245 => 154,  242 => 152,  239 => 151,  236 => 148,  233 => 147,  227 => 144,  225 => 141,  224 => 140,  223 => 139,  222 => 138,  221 => 137,  219 => 136,  216 => 134,  213 => 133,  210 => 128,  207 => 127,  204 => 125,  201 => 124,  198 => 119,  195 => 118,  188 => 114,  185 => 113,  180 => 108,  176 => 106,  174 => 105,  171 => 104,  165 => 101,  162 => 100,  159 => 99,  155 => 96,  149 => 93,  146 => 92,  144 => 91,  141 => 90,  137 => 87,  128 => 81,  125 => 80,  122 => 79,  118 => 77,  114 => 75,  108 => 73,  106 => 72,  101 => 71,  99 => 68,  98 => 67,  97 => 65,  95 => 64,  92 => 63,  86 => 194,  84 => 193,  81 => 192,  79 => 113,  76 => 111,  72 => 63,  70 => 62,  68 => 60,  66 => 59,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "themes/drupalam/templates/system/page--front.html.twig", "C:\\wamp64\\www\\drupal-am\\web\\themes\\drupalam\\templates\\system\\page--front.html.twig");
    }
}
