<?php

class MDContainer
{
    /// <summary>
    ///
    /// </summary>
    public function start($id = '', $class = '')
    {
        // MDContainer element

        echo '<div';

        // id attribute

        if (!empty($id))
        {
            echo ' id="', $id, '"';
        }

        // class attribue

        echo ' class="MDContainer';

        if (!empty($class))
        {
            echo ' ', $class;
        }

        echo '">';

        // MDHeader element

        echo '<div class="MDHeader">';
    }

    /// <summary>
    ///
    /// </summary>
    public function startBody()
    {
        echo '</div><div class="MDBody">';
    }

    /// <summary>
    ///
    /// </summary>
    public function startFooter()
    {
        echo '</div><div class="MDFooter">';
    }

    /// <summary>
    ///
    /// </summary>
    public function end()
    {
        echo '</div></div>';
    }
}
