<?php   
 /* CAT:Line chart */

 /* pChart library inclusions */
 include("../class/pData.class.php");
 include("../class/pDraw.class.php");
 include("../class/pImage.class.php");
 include("../../fonctions.php");
 
    
 
 /* Create and populate the pData object */
 $MyData = new pData();  
 //$MyData->addPoints(array(-4,VOID,VOID,12,8,3),"Probe 1");
 
 /* left4Dead style !!!!! */
 
	$bdd = connect_bdd();
    $matchs = mysqli_query($bdd,"select distinct id_match from calcul where id_match is not null and id_methode = 0 order by id_match;"); 
    
    while($data = mysqli_fetch_assoc($matchs))
    {
		$MyData->addPoints($data['id_match'],"Matchs");
    } 
    mysqli_free_result($matchs);
 
 $scores = mysqli_query($bdd,"select elo, pseudo from calcul, joueurs where id_match is not null and id_methode = 0 and calcul.id_joueur = joueurs.id order by id_match;"); 
 
 while($data = mysqli_fetch_assoc($scores))
    {
		$MyData->addPoints($data['elo'],$data['pseudo']);
    } 
    mysqli_free_result($scores);
 
 $joueurs = mysqli_query($bdd,"select distinct pseudo from calcul, joueurs where id_match is not null and id_methode = 0 and calcul.id_joueur = joueurs.id order by id_match;"); 
 
 while($data = mysqli_fetch_assoc($joueurs))
    {
		$MyData->setSerieWeight($data['pseudo'],1);
    } 
    mysqli_free_result($joueurs);
 
 /*$MyData->addPoints(array(1737.91,1741.42,1762.45,1762.48,1762.63,1770.3,1769.14,1769.09),"Spilbout");
 $MyData->addPoints(array(1720.71,1716.14,1688.74,1688.74,1688.76,1688.59,1686.61,1686.61),"RedXV");
 $MyData->addPoints(1603.52,"TOTO");
 $MyData->addPoints(1702.52,"TOTO");*/
 //$MyData->setSerieTicks("Probe 2",4);
 //$MyData->setSerieWeight("Spilbout",5);
 //$MyData->setSerieWeight("RedXV",2);
 
 $MyData->setAxisName(0,"Elo");
 //$MyData->addPoints(array("47","46","45","44","43","42","41","40"),"Matchs");
 $MyData->setSerieDescription("Matchs","Months");
 $MyData->setAbscissa("Matchs");

 /* Create the pChart object */
 $myPicture = new pImage(900,600,$MyData);

 /* Draw the background */
 $Settings = array("R"=>170, "G"=>183, "B"=>87, "Dash"=>1, "DashR"=>190, "DashG"=>203, "DashB"=>107);
 $myPicture->drawFilledRectangle(0,0,900,600,$Settings);

 /* Overlay with a gradient */
 $Settings = array("StartR"=>219, "StartG"=>231, "StartB"=>139, "EndR"=>1, "EndG"=>138, "EndB"=>68, "Alpha"=>50);
 $myPicture->drawGradientArea(0,0,900,600,DIRECTION_VERTICAL,$Settings);
 $myPicture->drawGradientArea(0,0,900,20,DIRECTION_VERTICAL,array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>80));

 /* Add a border to the picture */
 //$myPicture->drawRectangle(0,0,699,229,array("R"=>0,"G"=>0,"B"=>0));
 
 /* Write the picture title */ 
 $myPicture->setFontProperties(array("FontName"=>"../fonts/Silkscreen.ttf","FontSize"=>6));
 $myPicture->drawText(10,13,"drawPlotChart() - draw a plot chart",array("R"=>255,"G"=>255,"B"=>255));

 /* Write the chart title */ 
 $myPicture->setFontProperties(array("FontName"=>"../fonts/Forgotte.ttf","FontSize"=>11));
 $myPicture->drawText(450,55,"Lestate le vampire !",array("FontSize"=>20,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE));

 /* Draw the scale and the 1st chart */
 $myPicture->setGraphArea(60,60,850,550);
 $myPicture->drawFilledRectangle(60,60,850,550,array("R"=>255,"G"=>255,"B"=>255,"Surrounding"=>-200,"Alpha"=>10));
 $myPicture->drawScale(array("DrawSubTicks"=>TRUE));
 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
 $myPicture->setFontProperties(array("FontName"=>"../fonts/pf_arma_five.ttf","FontSize"=>6));
 $myPicture->drawLineChart(array("DisplayValues"=>TRUE,"DisplayColor"=>DISPLAY_AUTO));
 $myPicture->setShadow(FALSE);

 /* Draw the scale and the 2nd chart */
 /*$myPicture->setGraphArea(600,60,670,190);
 $myPicture->drawFilledRectangle(600,60,670,190,array("R"=>255,"G"=>255,"B"=>255,"Surrounding"=>-200,"Alpha"=>10));
 $myPicture->drawScale(array("Pos"=>SCALE_POS_TOPBOTTOM,"DrawSubTicks"=>TRUE));
 $myPicture->setShadow(TRUE,array("X"=>-1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
 $myPicture->drawLineChart();
 $myPicture->setShadow(FALSE);*/

 /* Write the chart legend */
 $myPicture->drawLegend(10,580,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawLineChart.png");
?>