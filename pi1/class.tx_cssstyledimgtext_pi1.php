<?php

require_once(t3lib_extMgm::extPath('css_styled_content').'pi1/class.tx_cssstyledcontent_pi1.php');

class tx_cssstyledimgtext_pi1 extends tx_cssstyledcontent_pi1 {
	var $classDefinitions = array(
		'0' => 'csi-center', // 'textpic-above-centre',
		'1' => 'csi-right', // 'textpic-above-right',
		'2' => 'csi-left', // 'textpic-above-left',
		'8' => 'csi-center', // 'textpic-below-centre',
		'9' => 'csi-right', // 'textpic-below-right',
		'10' => 'csi-left', // 'textpic-below-left',
		'17' => 'csi-intext-right', // 'textpic-intext-right',
		'18' => 'csi-intext-left', // 'textpic-intext-left',
		'25' => 'csi-intext-right-nowrap', // 'textpic-intext-right-nowrap',
		'26' => 'csi-intext-left-nowrap', // 'textpic-intext-left-nowrap'
	);
	function newImgText($conf) {
		//debug($conf, 'testname', __LINE__, __FILE__);
		extract($conf);

		foreach($imgsTag as $imgTag) {
			$images[] = '
				<div class="csi-image">'
					.$imgTag
					.'<div class="csi-caption"> '.$caption.' </div>
	    		</div>
			';
		}

		$images = implode('',$images);
		$imagebox = '<div class="csi-imagewrap">'.$images.'</div>'; 
		$content = '<div class="csi-text">'.$content.'</div>';

		$class = 'csi-textpic ';
		
		if($this->cObj->data['tx_cssstyledimgtext_floathorizontal']) {
			$class .= 'csi-floathorizontal ';
		}
		
		switch ($position)	{
			case 0: // textpic-above-centre
				$output = '<div class="'.$class.$this->classDefinitions[$position].'">'.$imagebox.$content.'</div>';
			break;

			case 8: // textpic-below-centre
				$output = '<div class="'.$class.$this->classDefinitions[$position].'">'.$content.$imagebox.'</div>';
			break;		

			case 1: // textpic-above-right
			case 2: // textpic-above-left
				$output = '<div class="'.$class.$this->classDefinitions[$position].'">'.$images.$content.'</div>';
			break;

			case 9: //textpic-below-right
			case 10: // textpic-below-left
				$output = '<div class="'.$class.$this->classDefinitions[$position].'">'.$content.$images.'<!-- keep it in the box! --><div style="clear:both;"></div></div>';
			break;

			case 17: // textpic-intext-right
			case 18: // textpic-intext-left
			case 25: // textpic-intext-right-nowrap
			case 26: // textpic-intext-left-nowrap
				$output = '<div class="'.$class.$this->classDefinitions[$position].'">'.$images.$content.'<!-- keep it in the box! --><div style="clear:both;"></div></div>';
			break;
		}

		return $output;
	}
	
	
	/**
	 * Rendering the IMGTEXT content element, called from TypoScript (tt_content.textpic.20)
	 * 
	 * @param	string		Content input. Not used, ignore.
	 * @param	array		TypoScript configuration. See TSRef "IMGTEXT". This function aims to be compatible.
	 * @return	string		HTML output.
	 * @access private
	 */

	 function render_textpic($content,$conf) {	
		if (is_array($conf['text.']))	{
			$content.= $this->cObj->stdWrap($this->cObj->cObjGet($conf['text.'],'text.'),$conf['text.']);	// this gets the surrounding content
		}
		$imgList=trim($this->cObj->stdWrap($conf['imgList'],$conf['imgList.']));	// gets images
		if ($imgList)	{
			$imgs = explode(',',$imgList);
			$imgStart = intval($this->cObj->stdWrap($conf['imgStart'],$conf['imgStart.']));
	
			$imgCount= count($imgs)-$imgStart;
	
			$imgMax = intval($this->cObj->stdWrap($conf['imgMax'],$conf['imgMax.']));
			if ($imgMax)	{
				$imgCount = t3lib_div::intInRange($imgCount,0,$conf['imgMax']);	// reduces the number of images.
			}

			$imgPath = $this->cObj->stdWrap($conf['imgPath'],$conf['imgPath.']);

				// initialisation
			$caption='';
			if (is_array($conf['caption.']))	{
				$caption= $this->cObj->stdWrap($this->cObj->cObjGet($conf['caption.'], 'caption.'),$conf['caption.']);
			}
			$captionArray=array();
			if ($conf['captionSplit'])	{
				$capSplit = $this->cObj->stdWrap($conf['captionSplit.']['token'],$conf['captionSplit.']['token.']);
				if (!$capSplit) {$capSplit=chr(10);}
				$caption2= $this->cObj->cObjGetSingle($conf['captionSplit.']['cObject'],$conf['captionSplit.']['cObject.'],'captionSplit.cObject');
				$captionArray=explode($capSplit,$caption2);
				while(list($ca_key,$ca_val)=each($captionArray))	{
					$captionArray[$ca_key] = $this->cObj->stdWrap(trim($captionArray[$ca_key]), $conf['captionSplit.']['stdWrap.']);
				}
			}			
			
			$tablecode='';
			$position=$this->cObj->stdWrap($conf['textPos'],$conf['textPos.']);

			$tmppos = $position&7;
			$contentPosition = $position&24; 
			$align = $this->cObj->align[$tmppos];
			$cap = ($caption)?1:0;
			$txtMarg = intval($this->cObj->stdWrap($conf['textMargin'],$conf['textMargin.']));
			if (!$conf['textMargin_outOfText'] && $contentPosition<16)	{
				$txtMarg=0;
			}
			
			$cols = intval($this->cObj->stdWrap($conf['cols'],$conf['cols.']));
			$rows = intval($this->cObj->stdWrap($conf['rows'],$conf['rows.']));
			$colspacing = intval($this->cObj->stdWrap($conf['colSpace'],$conf['colSpace.']));
			$rowspacing = intval($this->cObj->stdWrap($conf['rowSpace'],$conf['rowSpace.']));

			$border = intval($this->cObj->stdWrap($conf['border'],$conf['border.'])) ? 1:0;
			$borderColor = $this->cObj->stdWrap($conf['borderCol'],$conf['borderCol.']);
			$borderThickness = intval($this->cObj->stdWrap($conf['borderThick'],$conf['borderThick.']));

			$borderColor=$borderColor?$borderColor:'black';
			$borderThickness=$borderThickness?$borderThickness:1;
			
			$caption_align = $this->cObj->stdWrap($conf['captionAlign'],$conf['captionAlign.']);
			if (!$caption_align) {
				$caption_align = $align;
			}
				// generate cols
			$colCount = ($cols > 1) ? $cols : 1;
			if ($colCount > $imgCount)	{$colCount = $imgCount;}
			$rowCount = ($colCount > 1) ? ceil($imgCount / $colCount) : $imgCount;
				// generate rows
			if ($rows>1)  {
				$rowCount = $rows;
				if ($rowCount > $imgCount)	{$rowCount = $imgCount;}
				$colCount = ($rowCount>1) ? ceil($imgCount / $rowCount) : $imgCount;
			}
			
				// max Width
			$colRelations = trim($this->cObj->stdWrap($conf['colRelations'],$conf['colRelations.']));
			$maxW = intval($this->cObj->stdWrap($conf['maxW'],$conf['maxW.']));
			
			$maxWInText = intval($this->cObj->stdWrap($conf['maxWInText'],$conf['maxWInText.']));
			if (!$maxWInText)	{	// If maxWInText is not set, it's calculated to the 70 % of the max...
				$maxWInText = round($maxW/100*50);
			}
			
			if ($maxWInText && $contentPosition>=16)	{	// inText
				$maxW = $maxWInText;
			}

			if ($maxW && $colCount > 0) {	// If there is a max width and if colCount is greater than  column
				$maxW = ceil(($maxW-$colspacing*($colCount-1)-$colCount*$border*$borderThickness*2)/$colCount);
			}
				// create the relation between rows
			$colMaxW = Array();
			if ($colRelations)	{
				$rel_parts = explode(':',$colRelations);
				$rel_total = 0;
				for ($a=0;$a<$colCount;$a++)	{
					$rel_parts[$a] = intval($rel_parts[$a]);
					$rel_total+= $rel_parts[$a];
				}
				if ($rel_total)	{
					for ($a=0;$a<$colCount;$a++)	{
						$colMaxW[$a] = round(($maxW*$colCount)/$rel_total*$rel_parts[$a]);
					}
					if (min($colMaxW)<=0 || max($rel_parts)/min($rel_parts)>10)	{		// The difference in size between the largest and smalles must be within a factor of ten.
						$colMaxW = Array();
					}
				}
			}
			$image_compression = intval($this->cObj->stdWrap($conf['image_compression'],$conf['image_compression.']));
			$image_effects = intval($this->cObj->stdWrap($conf['image_effects'],$conf['image_effects.']));
			$image_frames = intval($this->cObj->stdWrap($conf['image_frames.']['key'],$conf['image_frames.']['key.']));
			
				// fetches pictures
			$splitArr=array();
			$splitArr['imgObjNum']=$conf['imgObjNum'];
			$splitArr = $GLOBALS['TSFE']->tmpl->splitConfArray($splitArr,$imgCount);

			/*$alttext = $this->cObj->stdWrap($conf['altText'],$conf['altText.']);
			if ($alttext) {
				$altP = ' alt="'.htmlspecialchars(strip_tags($alttext)).'"';
			}*/

				// EqualHeight
			$equalHeight = intval($this->cObj->stdWrap($conf['equalH'],$conf['equalH.']));
			if ($equalHeight)	{	// Initiate gifbuilder object in order to get dimensions AND calculate the imageWidth's
				$gifCreator = t3lib_div::makeInstance('tslib_gifbuilder');
				$gifCreator->init();
				$relations = Array();
				$relations_cols = Array();
				$totalMaxW = $maxW*$colCount;
				for($a=0;$a<$imgCount;$a++)	{
					$imgKey = $a+$imgStart;
					$imgInfo = $gifCreator->getImageDimensions($imgPath.$imgs[$imgKey]);
					$relations[$a] = $imgInfo[1] / $equalHeight;	// relationship between the original height and the wished height
					if ($relations[$a])	{	// if relations is zero, then the addition of this value is omitted as the image is not expected to display because of some error.
						$relations_cols[floor($a/$colCount)] += $imgInfo[0]/$relations[$a];	// counts the total width of the row with the new height taken into consideration.
					}
				}
			}

			$imageRowsFinalWidths = Array();	// contains the width of every image row
			$imageRowsMaxHeights = Array();
			$imgsTag=array();
			$origImages=array();
			for($a=0;$a<$imgCount;$a++)	{
				$GLOBALS['TSFE']->register['IMAGE_NUM'] = $a;
				
				$imgKey = $a+$imgStart;
				$totalImagePath = $imgPath.$imgs[$imgKey];
				$this->cObj->data[$this->cObj->currentValKey] = $totalImagePath;
				$imgObjNum = intval($splitArr[$a]['imgObjNum']);
				$imgConf = $conf[$imgObjNum.'.'];

				if ($equalHeight)	{
					$scale = 1;
					if ($totalMaxW)	{
						$rowTotalMaxW = $relations_cols[floor($a/$colCount)];
						if ($rowTotalMaxW > $totalMaxW)	{
							$scale = $rowTotalMaxW / $totalMaxW;
						}
					}
						// transfer info to the imageObject. Please note, that 
					$imgConf['file.']['height'] = round($equalHeight/$scale);

					unset($imgConf['file.']['width']);
					unset($imgConf['file.']['maxW']);
					unset($imgConf['file.']['maxH']);
					unset($imgConf['file.']['minW']);
					unset($imgConf['file.']['minH']);
					unset($imgConf['file.']['width.']);
					unset($imgConf['file.']['maxW.']);
					unset($imgConf['file.']['maxH.']);

					unset($imgConf['file.']['minW.']);
					unset($imgConf['file.']['minH.']);
					$maxW = 0;	// setting this to zero, so that it doesn't disturb
				}
				
				if ($maxW) {
					if (count($colMaxW))	{
						$imgConf['file.']['maxW'] = $colMaxW[($a%$colCount)];
					} else {
						$imgConf['file.']['maxW'] = $maxW;
					}
				}
				
				if ($imgConf || $imgConf['file']) {
					$imgConf['params'].=$altP;
					if ($this->cObj->image_effects[$image_effects])	{
						$imgConf['file.']['params'].= ' '.$this->cObj->image_effects[$image_effects];
					}
					if ($image_frames)	{
						if (is_array($conf['image_frames.'][$image_frames.'.']))	{
							$imgConf['file.']['m.'] = $conf['image_frames.'][$image_frames.'.'];
						}
					}
					if ($image_compression && $imgConf['file']!='GIFBUILDER')	{
						if ($image_compression==1)	{
							$tempImport = $imgConf['file.']['import'];
							$tempImport_dot = $imgConf['file.']['import.'];
							unset($imgConf['file.']);
							$imgConf['file.']['import'] = $tempImport;
							$imgConf['file.']['import.'] = $tempImport_dot;
						} elseif (isset($this->cObj->image_compression[$image_compression])) {
							$imgConf['file.']['params'].= ' '.$this->cObj->image_compression[$image_compression]['params'];
							$imgConf['file.']['ext'] = $this->cObj->image_compression[$image_compression]['ext'];
							unset($imgConf['file.']['ext.']);
						}
					}
					$imgsTag[$imgKey] = $this->cObj->IMAGE($imgConf);
				} else {
					$imgsTag[$imgKey] = $this->cObj->IMAGE(Array('params'=>$altP, 'file'=>$totalImagePath)); 	// currentValKey !!!
				}
					// Store the original filepath
				$origImages[$imgKey]=$GLOBALS['TSFE']->lastImageInfo;

				$imageRowsFinalWidths[floor($a/$colCount)] += $GLOBALS['TSFE']->lastImageInfo[0];
				if ($GLOBALS['TSFE']->lastImageInfo[1]>$imageRowsMaxHeights[floor($a/$colCount)])	{
					$imageRowsMaxHeights[floor($a/$colCount)] = $GLOBALS['TSFE']->lastImageInfo[1];
				}
			}			
				// calculating the tableWidth:
				// TableWidth problems: It creates problems if the pictures are NOT as wide as the tableWidth.
			$tableWidth = max($imageRowsFinalWidths)+ $colspacing*($colCount-1) + $colCount*$border*$borderThickness*2;
			
				// make table for pictures
			$index=$imgStart;
			
			$noRows = $this->cObj->stdWrap($conf['noRows'],$conf['noRows.']);
			$noCols = $this->cObj->stdWrap($conf['noCols'],$conf['noCols.']);
			if ($noRows) {$noCols=0;}	// noRows overrides noCols. They cannot exist at the same time.
			if ($equalHeight) {
				$noCols=1;
				$noRows=0;
			}

			$rowCount_temp=1;
			$colCount_temp=$colCount;
			if ($noRows)	{
				$rowCount_temp = $rowCount;
				$rowCount=1;
			}
			if ($noCols)	{
				$colCount=1;
			}

				// col- and rowspans calculated
			$colspan = (($colspacing) ? $colCount*2-1 : $colCount);
			$rowspan = (($rowspacing) ? $rowCount*2-1 : $rowCount) + $cap;
			
				// put all variables that are needed for the new implementation in one conf array.

			if(t3lib_div::GPVar('old')) {
				$output = $this->oldImgText(compact('content', 'caption','borderColor', 'borderThickness', 'border', 'txtMarg', 'cap', 'align', 'contentPosition', 'tmppos', 'position', 'captionArray', 'imgPath', 'imgsTag', 'origImages', 'imageRowsFinalWidths', 'imageRowsMaxHeights','tableWidth', 'noCols', 'noRows', 'rowCount', 'colCount', 'rowspan', 'colspan', 'conf', 'colCount_temp', 'rowCount_temp', 'splitArr', 'colRelations', 'maxW', 'caption_align'));
			} else {
				$output = $this->newImgText(compact('content', 'caption','borderColor', 'borderThickness', 'border', 'txtMarg', 'cap', 'align', 'contentPosition', 'tmppos', 'position', 'captionArray', 'imgPath', 'imgsTag', 'origImages', 'imageRowsFinalWidths', 'imageRowsMaxHeights','tableWidth', 'noCols', 'noRows', 'rowCount', 'colCount', 'rowspan', 'colspan', 'conf', 'colCount_temp', 'rowCount_temp', 'splitArr', 'colRelations', 'maxW', 'caption_align'));
			}

		} else {
			$output= $content;
		}
		
		if ($conf['stdWrap.']) {
			$output = $this->cObj->stdWrap($output, $conf['stdWrap.']);
		}
		
		return $output;
	}

	function oldImgText($conf) {
		//debug($conf, 'testname', __LINE__, __FILE__);
		extract($conf);

			// Edit icons:
		$editIconsHTML = $conf['editIcons']&&$GLOBALS['TSFE']->beUserLogin ? $this->cObj->editIcons('',$conf['editIcons'],$conf['editIcons.']) : '';
		
			// strech out table:
		$tablecode='';
		$flag=0;
		if ($conf['noStretchAndMarginCells']!=1)	{
			$tablecode.='<tr>';
			if ($txtMarg && $align=='right')	{	// If right aligned, the textborder is added on the right side
				$tablecode.='<td rowspan="'.($rowspan+1).'" valign="top"><img src="'.$GLOBALS['TSFE']->absRefPrefix.'clear.gif" width="'.$txtMarg.'" height="1" alt="" />'.($editIconsHTML?'<br />'.$editIconsHTML:'').'</td>';
				$editIconsHTML='';
				$flag=1;
			}
			$tablecode.='<td colspan="'.$colspan.'"><img src="'.$GLOBALS['TSFE']->absRefPrefix.'clear.gif" width="'.$tableWidth.'" height="1" alt="" /></td>';
			if ($txtMarg && $align=='left')	{	// If left aligned, the textborder is added on the left side
				$tablecode.='<td rowspan="'.($rowspan+1).'" valign="top"><img src="'.$GLOBALS['TSFE']->absRefPrefix.'clear.gif" width="'.$txtMarg.'" height="1" alt="" />'.($editIconsHTML?'<br />'.$editIconsHTML:'').'</td>';
				$editIconsHTML='';
				$flag=1;
			}
			if ($flag) $tableWidth+=$txtMarg+1;
//			$tableWidth=0;
			$tablecode.='</tr>';
		}
			
			// draw table
		for ($c=0;$c<$rowCount;$c++) {	// Looping through rows. If 'noRows' is set, this is '1 time', but $rowCount_temp will hold the actual number of rows!
			if ($c && $rowspacing)	{		// If this is NOT the first time in the loop AND if space is required, a row-spacer is added. In case of "noRows" rowspacing is done further down.
				$tablecode.='<tr><td colspan="'.$colspan.'"><img src="'.$GLOBALS['TSFE']->absRefPrefix.'clear.gif" width="1" height="'.$rowspacing.'" border="0" alt="" /></td></tr>';
			}
			
			$tablecode.='<tr>';	// starting row
			for ($b=0; $b<$colCount_temp; $b++)	{	// Looping through the columns
				if ($b && $colspacing)	{		// If this is NOT the first iteration AND if column space is required. In case of "noCols", the space is done without a separate cell.
					if (!$noCols)	{
						$tablecode.='<td><img src="'.$GLOBALS['TSFE']->absRefPrefix.'clear.gif" width="'.$colspacing.'" height="1" border="0" alt="" /></td>';
					} else {
						$colSpacer='<img src="'.$GLOBALS['TSFE']->absRefPrefix.'clear.gif" width="'.($border?$colspacing-6:$colspacing).'" height="'.($imageRowsMaxHeights[$c]+($border?$borderThickness*2:0)).'" border="0">';
						$colSpacer='<td valign="top">'.$colSpacer.'</td>';	// added 160301, needed for the new "noCols"-table...
						$tablecode.=$colSpacer;
					}
				}
				if (!$noCols || ($noCols && !$b))	{
					$tablecode.='<td valign="top">';	// starting the cell. If "noCols" this cell will hold all images in the row, otherwise only a single image.
					if ($noCols)	{$tablecode.='<table width="'.$imageRowsFinalWidths[$c].'" border="0" cellpadding="0" cellspacing="0"><tr>';}		// In case of "noCols" we must set the table-tag that surrounds the images in the row.
				}
				for ($a=0;$a<$rowCount_temp;$a++)	{	// Looping through the rows IF "noRows" is set. "noRows"  means that the rows of images is not rendered by physical table rows but images are all in one column and spaced apart with clear-gifs. This loop is only one time if "noRows" is not set.
					$imgIndex = $index+$a*$colCount_temp;
					if ($imgsTag[$imgIndex])	{
						if ($rowspacing && $noRows && $a) {		// Puts distance between the images IF "noRows" is set and this is the first iteration of the loop
							$tablecode.= '<img src="'.$GLOBALS['TSFE']->absRefPrefix.'clear.gif" width="1" height="'.$rowspacing.'" alt="" /><br />';
						}

						$imageHTML = $imgsTag[$imgIndex].'<br />';
						$Talign = (!trim($captionArray[$imgIndex]) && !$noRows && !$conf['netprintApplicationLink']) ? ' align="left"' : '';  // this is necessary if the tablerows are supposed to space properly together! "noRows" is excluded because else the images "layer" together. 
						if ($border)	{$imageHTML='<table border="0" cellpadding="'.$borderThickness.'" cellspacing="0" bgcolor="'.$borderColor.'"'.$Talign.'><tr><td>'.$imageHTML.'</td></tr></table>';}		// break-tag added 160301  , ($noRows?'':' align="left"')  removed 160301, break tag removed 160301 (later...)
						$imageHTML.=$editIconsHTML;		$editIconsHTML='';
						if ($conf['netprintApplicationLink'])	{$imageHTML = $this->cObj->netprintApplication_offsiteLinkWrap($imageHTML,$origImages[$imgIndex],$conf['netprintApplicationLink.']);}
						$imageHTML.=$captionArray[$imgIndex];	// Adds caption.
						if ($noCols)	{$imageHTML='<td valign="top">'.$imageHTML.'</td>';}		// If noCols, put in table cell.
						$tablecode.=$imageHTML;
					}
				}
				$index++;
				if (!$noCols || ($noCols && $b+1==$colCount_temp))	{
					if ($noCols)	{$tablecode.='</tr></table>';}	// In case of "noCols" we must finish the table that surrounds the images in the row.
					$tablecode.='</td>';	// Ending the cell. In case of "noCols" the cell holds all pictures!
				}
			}
			$tablecode.='</tr>';	// ending row
			
		}
		if ($c)	{
			// Table-tag is inserted
			$i=$contentPosition;
			$table_align = (($i==16) ? 'align="'.$align.'"' : '');
			$tablecode = '<table'.($tableWidth?' width="'.$tableWidth.'"':'').' border="0" cellspacing="0" cellpadding="0" '.$table_align.' class="imgtext-table">'.$tablecode;
			if ($editIconsHTML)	{	// IF this value is not long since reset.
				$tablecode.='<tr><td colspan="'.$colspan.'">'.$editIconsHTML.'</td></tr>';
				$editIconsHTML='';
			}
			if ($cap)	{
				$tablecode.='<tr><td colspan="'.$colspan.'" align="'.$caption_align.'">'.$caption.'</td></tr>';
			}
			$tablecode.='</table>';
			if ($conf['tableStdWrap.'])	{$tablecode=$this->cObj->stdWrap($tablecode,$conf['tableStdWrap.']);}
		}

		$spaceBelowAbove = intval($this->cObj->stdWrap($conf['spaceBelowAbove'],$conf['spaceBelowAbove.']));
		switch ($contentPosition)	{
			case '0':	// above
				$output= '<div align="'.$align.'">'.$tablecode.'</div>'.$this->cObj->wrapSpace($content, $spaceBelowAbove.'|0');
			break;
			case '8':	// below
				$output= $this->cObj->wrapSpace($content, '0|'.$spaceBelowAbove).'<div align="'.$align.'">'.$tablecode.'</div>';
			break;
			case '16':	// in text
				$output= $tablecode.$content;
			break;
			case '24':	// in text, no wrap
				$theResult = '';
				$theResult.= '<table border="0" cellspacing="0" cellpadding="0" class="imgtext-nowrap"><tr>';
				if ($align=='right')	{
					$theResult.= '<td valign="top">'.$content.'</td><td valign="top">'.$tablecode.'</td>';
				} else {
					$theResult.= '<td valign="top">'.$tablecode.'</td><td valign="top">'.$content.'</td>';
				}
				$theResult.= '</tr></table>';
				$output= $theResult;
			break;
		}

		return $output;
	}
}

?>