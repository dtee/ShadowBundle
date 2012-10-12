<?php
namespace Odl\ShadowBundle\Grid\Columns;

use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;

use Dtc\GridBundle\Grid\Source\DocumentGridSource;
use Dtc\GridBundle\Grid\Column\TwigBlockGridColumn;

use Twig_Environment;
use ArrayObject;

class GameGridColumns
    extends ArrayObject
{
    public function __construct(Twig_Environment $twig, GlobalVariables $globals = null)
    {
        $columns = array();

        $template = $twig->loadTemplate('OdlShadowBundle:Game:_grid.html.twig');

        $env = array(
                'app' => $globals
        );

        $columns[] = new TwigBlockGridColumn('name', 'Name', $template, $env);

        parent::__construct($columns);
    }
}
