<?php
namespace App\Libraries;

class Breadcrumb
{

    private $breadcrumbs = array();
    private $tags;

    public function __construct()
    {
        $this->URI = service('uri');

        // SHOULD THE LAST BREADCRUMB BE A CLICKABLE LINK? iF SO SET TO TRUE
        $this->clickable = false;

        // create our bootstrap html elements
        $this->tags['navopen']  = "<div class=\"c-subheader justify-content-between\">";
        $this->tags['navclose'] = "</div>";
        $this->tags['olopen']   = "<ol class=\"breadcrumb border-0 m-0 px-0 px-md-3\">";
        $this->tags['olclose']  = "</ol>";
        $this->tags['liopen']   = "<li class=\"breadcrumb-item text-capitalize\">";
        $this->tags['liclose']  = "</li>";
    }

    public function add($crumb, $href)
    {

        if (!$crumb or !$href) return; // if the title or Href not set return 

        $this->breadcrumbs[] = array(
            'crumb' => $crumb,
            'href' => $href,
        );
    }


    public function render()
    {

        $output  = $this->tags['navopen'];
        $output .= $this->tags['olopen'];

        $count = count($this->breadcrumbs) - 1;

        foreach ($this->breadcrumbs as $index => $breadcrumb) {

            if ($index == $count) {
                $output .= $this->tags['liopen'];
                $output .= $breadcrumb['crumb'];
                $output .= $this->tags['liclose'];
            } else {
                $output .= $this->tags['liopen'];
                $output .= '<a href="' . '/' . $breadcrumb['href'] . '">';
                $output .= $breadcrumb['crumb'];
                $output .= '</a>';
                $output .= $this->tags['liclose'];
            }
        }

        $output .= $this->tags['olclose'];
        $output .= $this->tags['navclose'];

        return $output;
    }

    public function buildAuto()
    {

        $urisegments = $this->URI->getSegments();

        $output  = $this->tags['navopen'];
        $output .= $this->tags['olopen'];

        $crumbs = array_filter($urisegments);

        $result = array();
        $path = '';

        // SUBTRACT 1 FROM COUNT IF THE LAST LINK IS TO NOT BE A LINK
        $count = count($crumbs) ;

        if ($this->clickable){

            $count = count($crumbs) -1;
        }
        

        foreach ($crumbs as $k => $crumb) {

            $path .= '/' . $crumb;

            $name = ucwords(str_replace(array(".php", "_"), array("", " "), $crumb));
            $name = ucwords(str_replace('-', ' ', $name));

            if ($k != $count) {

                $result[] = $this->tags['liopen'] . '<a href="' . $path . '"> ' . $name . '</a>' . $this->tags['liclose'];

            } else {

                $result[] = $this->tags['liopen'] . $name . $this->tags['liclose'];

            }
        }

        $output .= implode($result);
        $output .= $this->tags['olclose'];
        $output .= $this->tags['navclose'];

        return $output;
    }
}
