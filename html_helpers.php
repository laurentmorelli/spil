<?php

function histogram_bar( $size, $margin=0 )
{
  $style = "width:". abs(round($size)) ."px;"
         . "margin-left: ".  (($size < 0 ) ? $margin+$size : $margin ) ."px;";
  
  if ($size < 0) 
  {
    $style .= "background-color: #955;";
  }

  return "<div class='histo' style='$style'></div>";
}



?>
