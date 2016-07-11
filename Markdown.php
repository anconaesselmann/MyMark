<?php
/**
 *
 */
namespace aae\ui {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\ui
	 */
	class Markdown {
		const ALLOW_HTML = 1;
		const DISPLAY_ATTRIBUTION = 2;

		private $_allowHTML = false, $_displayAttribution = false, $_fileName = false;
		public function __construct($settings = false) {
			if ($settings & Markdown::ALLOW_HTML) {
				$this->_allowHTML = true;
			}
			if ($settings & Markdown::DISPLAY_ATTRIBUTION) {
				$this->_displayAttribution = true;
			}
			$this->paragraphOpenTag   = "<p>";
			$this->paragraphCloseTag  = "</p>";
		}
		public function displayAttribution() {
			$this->_displayAttribution = true;
		}
		public function getHTML($markdownText) {
			if (!$this->_allowHTML) $markdownText = $this->_removeHtml($markdownText);
			$markdownText = $this->_resolveEscapeCharacters($markdownText);

			$markdownText = $this->_insertGlobalLinks($markdownText);
			$links    = $this->_extractLinks($markdownText);
			$markdownText = $this->_replaceLinks($markdownText, $links);


			$markdownText = $this->_insertCssIdSpan($markdownText);
			$markdownText = $this->_insertCssClassSpan($markdownText);

			$markdownText = $this->_replaceHorizontalLine($markdownText);
			$markdownText = $this->_insertCodeTags($markdownText);
			$markdownText = $this->_replaceHeadings($markdownText);
			$markdownText = $this->_replaceBold($markdownText);
			$markdownText = $this->_replaceItalic($markdownText);
			$markdownText = $this->_buildTables($markdownText);
			$markdownText = $this->_replaceLists($markdownText);
			$markdownText = $this->_insertParagraphs($markdownText);
			$markdownText = $this->_replaceNewLines($markdownText);
			$markdownText = $this->_removeTrailingSpaces($markdownText);
			$markdownText = $this->_removeEmptyParagraphs($markdownText);

			$markdownText = $this->_insertAttribution($markdownText);

			$this->_fileName = false;

			return trim($markdownText);
		}
		public function fileGetContentsAsHtml($fileName) {
			$contents = file_get_contents($fileName);
			$this->_fileName = $fileName;
			$html = $this->getHTML($contents);
			return $html;
		}

		private function _insertAttribution($markdownText) {
			if ($this->_displayAttribution === true) {
				$originalContentStatement = "";
				if ($this->_fileName) {
					$fileName = realpath($this->_fileName);
					$documentRoot = $_SERVER["DOCUMENT_ROOT"];

					if (substr($documentRoot, -1) != "/") {
						$documentRoot .= "/";
					}

					$strPos = strpos($fileName, $documentRoot);

					if ($strPos !== false) {
						$originalContentLink = substr($fileName, strlen($documentRoot));
						$originalContentStatement = " View the original file <a href=\"$originalContentLink\">here</a>.";
					}
				}
				$markdownText .= '<div class="attribution">This page was rendered with <a href="https://github.com/anconaesselmann/MyMark">MyMark</a>, a Markdown flavor developed by <a href="http://anconaesselmann.com">Axel Ancona Esselmann</a>.'.$originalContentStatement.'</div>';

			}
			return $markdownText;
		}

		private function _removeEmptyParagraphs($markdownText) {
			$regex = "/<p[^>]*>([\s\n]*)<\\/p[^>]*>/";
			return preg_replace($regex, "", $markdownText);
		}

		private function _replaceNewLines($markdownText){
			$newlienTag         = "<br />";
			$regex = "/(\\n)/";
			return preg_replace($regex, $newlienTag, $markdownText);
		}
		private function _removeTrailingSpaces($markdownText) {
			$markdownText = $this->paragraphOpenTag.$markdownText.$this->paragraphCloseTag;
			$regex = "/(\<br \/\>)*([\s\n]*)(\<\/p[^>]*>)/";
			$markdownText = preg_replace($regex, "$3", $markdownText);
			$regex = "/(\<p[^>]*>)([\s\n]*)(\<br \/\>)*/";
			$markdownText = preg_replace($regex, "$1", $markdownText);
			return $markdownText;
		}

		private function _removeHtml($markdownText) {
			$regex = "/<[^>]*>/";
			return preg_replace($regex, "", $markdownText);
		}

		private function _replaceBold($markdownText) {
			$strongOpenTag      = "<strong>";
			$strongCloseTag     = "</strong>";
			$regex = "/(\*\*)([^\s^\*])([^\*]*)(\*\*)/";
			return preg_replace($regex, $strongOpenTag."$2$3".$strongCloseTag, $markdownText);
		}

		private function _insertCssIdSpan($markdownText) {
			$regex      = '/
				(\{\#)
				(?<cssId>[^\s]+)
				(\s+)
				(?<body>[^\}]+)
				(\})
			/sx';
			$callback = function ($matches) {
				$htmlHeading      = "<span id=\"".$matches["cssId"]."\">".$matches["body"]."</span>";
				return $htmlHeading;
			};

			$markdownText = preg_replace_callback($regex, $callback, $markdownText);

			return $markdownText;
		}
		// TODO: extract only links to prevent possible security vulnerability
		private function _insertGlobalLinks($markdownText) {
			$regex = "/
				(?<beginning>\[__GLOBAL__\]\:\s*)
				(?<fileName>[^\s]*)
				(?<end>\s*\n|\s*$)
			/sx";
			$callback = function ($matches) {
				$fileName = $matches["fileName"];
				$fileContent = file_get_contents($_SERVER["DOCUMENT_ROOT"].DIRECTORY_SEPARATOR.$fileName);
				return $fileContent;
			};

			$markdownText = preg_replace_callback($regex, $callback, $markdownText);

			return $markdownText;
		}
		//_insertGlobalLinks($markdownText)
		private function _insertCssClassSpan($markdownText) {
			$regex      = '/
				(\{\.)
				(?<cssClass>[^\s]+)
				(\s+)
				(?<body>[^\}]+)
				(\})
			/sx';
			$callback = function ($matches) {
				$htmlHeading      = "<span class=\"".$matches["cssClass"]."\">".$matches["body"]."</span>";
				return $htmlHeading;
			};

			$markdownText = preg_replace_callback($regex, $callback, $markdownText);

			return $markdownText;
		}

		private function _replaceItalic($markdownText) {
			$italicOpenTag      = "<i>";
			$italicCloseTag     = "</i>";
			$regex = "/(\*)([^\s^\*])([^\*]*)(\*)/";
			return preg_replace($regex, $italicOpenTag."$2$3".$italicCloseTag, $markdownText);
		}

		public function _insertParagraphs($markdownText) {
			$regex = "/(\\n\\n)/";
			return preg_replace($regex, $this->paragraphCloseTag.$this->paragraphOpenTag, $markdownText);
		}

		private function _extractLinks(&$markdownText) {
			$assoc = array();
			$result = array();
			$regex = "/(\[)([^\]]+)(\])(\:\s*)([^\n]*?)(\s*\n|\s*$)/";
			preg_match_all($regex, $markdownText, $assoc);
			for ($i=0; $i < count($assoc[0]); $i++) {
				$result[$assoc[2][$i]] = $assoc[5][$i];
			}
			$markdownText = preg_replace($regex, "", $markdownText);
			return $result;
		}

		private function _replaceLinks($markdownText, $links) {
			foreach ($links as $linkNbr => $link) {
				$linkParts = explode(";", $linkNbr);
				if (count($linkParts) > 1) {
					$linkNbr = $linkParts[0];
					$linkType = $linkParts[1];
					switch ($linkType) {
						case 'i':
						case 'img':
							$markdownText = $this->_replaceImages($markdownText, $linkNbr, $link);
							break;

						case 'v':
						case 'youtube':
						case 'flickr':
						case 'iframe':
							$markdownText = $this->_replaceImbedded($markdownText, $linkNbr, $link);
							break;

						default:
							break;
					}
				} else {
					$markdownText = $this->_replaceHyperLink($markdownText, $linkNbr, $link);
				}
			}
			$markdownText = $this->_replaceExplicitLinks($markdownText);
			return $markdownText;
		}

		private function _replaceExplicitLinks($markdownText) {
			$patternExplicitLink = "/
				(?<=^|[^w])
				(\[)
				(?P<link>[^\]]+)
				(\])
				(?=[^w]|$)
			/x";
			$explicitLinkCallback = function ($matches) {
				return "<a href=\"".$matches["link"]."\">".$matches["link"]."</a>";
			};
			return preg_replace_callback($patternExplicitLink, $explicitLinkCallback, $markdownText);
		}

		private function _replaceHyperLink($markdownText, $linkNbr, $link) {
			$regex = "/([^\s^\]]+)(\[$linkNbr\])/";
			$markdownText = preg_replace($regex, "<a href=\"$link\">$1</a>", $markdownText);
			$regex = "/(\[)([^\]]+)([\]])(\[$linkNbr\])/";
			return preg_replace($regex, "<a href=\"$link\">$2</a>", $markdownText);
		}

		private function _replaceImages($markdownText, $linkNbr, $link) {
			$linkAttributes = $this->_getLinkAttributes($link);
			$regex = "/(\[)([^\]]+)([\]])(\[$linkNbr\])/";
			$markdownText = preg_replace($regex, "<img$linkAttributes alt=\"$2\">", $markdownText);
			return $markdownText;
		}
		private function _replaceImbedded($markdownText, $linkNbr, $link) {
			$linkAttributes = $this->_getLinkAttributes($link, 420, 315);
			$regex = "/(\[)([^\]]+)([\]])(\[$linkNbr\])/";
			$markdownText = preg_replace($regex, "<iframe$linkAttributes frameborder=\"0\" allowfullscreen webkitallowfullscreen mozallowfullscreen oallowfullscreen msallowfullscreen></iframe>", $markdownText);
			return $markdownText;
		}


		private function _getLinkAttributes($link, $defaultWidth = null, $defaultHeight = null) {
			if (!is_null($defaultHeight) && !is_null($defaultWidth)) {
				$height = " height=\"$defaultHeight\"";
				$width = " width=\"$defaultWidth\"";
			} else {
				$height = "";
				$width = "";
			}
			$class = "";
			$array = explode("|", $link);
			if (count($array) > 1) {
				$link       = $array[1];
				$parts      = explode(";", $array[0]);
				$dimensions = explode("x", $parts[0]);
				if (count($dimensions) > 1) {
					$height = " height=\"".$dimensions[0]."\"";
					$width = " width=\"".$dimensions[1]."\"";
				}
				if (count($parts) > 1) {
					$class = " class=\"".$parts[1]."\"";
				} else {
					$class = " class=\"".$parts[0]."\"";
				}
			} else {
				$link = $array[0];
			}
			return $class.$width.$height." src=\"$link\"";
		}



		private function _replaceHeadings($markdownText) {
			$regex      = '/			# Format: ^(1-6 hash-tags)(?: space)(heading)(?: optional space and hash-tags)$

				(?<headingType> 		# The beginning of a header:
					(?<=
						^|\n 			# 	the beginning of a string or a newline.
					)\#+				# 	followed by 1 to 6 #
				)
				(?:\s*)					# 	throw away all whitespace between hash-tags and heading
				(?<heading>
					[^\#]+?				#	the heading body
				)
				(?:[\s\#]*)				#	throw away all whitespace and hash-tags
				(?=
					\n|$
				)
			/sx';

			$headingCallback = function ($matches) {
				$headingType      = (string)strlen(trim($matches["headingType"]));
				$headingOpening   = "<h$headingType>";
				$headingCosing    = "</h$headingType>";
				$htmlHeading      = $headingOpening.$matches["heading"].$headingCosing;
				return "</p>".$htmlHeading."<p>";
			};

			$markdownText = preg_replace_callback($regex, $headingCallback, $markdownText);

			return $markdownText;
		}

		public function _insertCodeTags($markdownText) {
			$regex = "/(\n\s*\n)(    )(.+?)(\n\n+(?=[^\s])|$)/s";
			$matches    = array();
			$hasMatches = preg_match_all($regex, $markdownText, $matches);
			if ($hasMatches) {
				for ($i=0; $i < count($matches[0]); $i++) {
					$markdownCodeBlock = $matches[3][$i];
					$replacementRegex = "/\n    /";
					$codeBlock     = preg_replace($replacementRegex, "<br />", $markdownCodeBlock);
					$htmlCodeBlock = "\n".$this->paragraphCloseTag."<pre class=\"prettyprint\"><code>".$codeBlock."</code></pre>".$this->paragraphOpenTag;
					$markdownText = str_replace($matches[0][$i], $htmlCodeBlock, $markdownText);
				}
			}
			return $markdownText;
		}

		public function _resolveEscapeCharacters($markdownText) {
			$markdownText = str_replace("\\*", "&#42;", $markdownText);
			$markdownText = str_replace("\\<", "&lt;", $markdownText);
			$markdownText = str_replace("\\>", "&gt;", $markdownText);
			$markdownText = str_replace("\\[", "&#91;", $markdownText);
			$markdownText = str_replace("\\]", "&#93;", $markdownText);
			$markdownText = str_replace("\\-", "&mdash;", $markdownText);
			return $markdownText;
		}

		public function _replaceHorizontalLine($markdownText) {
			$regex = "/-{3,}/";
			$markdownText = preg_replace($regex, $this->paragraphCloseTag."<hr />".$this->paragraphOpenTag, $markdownText);
			return $markdownText;
		}

		public function _replaceLists($markdownText) {
			$listTypes = array(
				new ListType("[\?]+\s", "<dl>", "</dl>"),
				new ListType("[IVX]+[\.\)]\s", "<ol type=\"I\">", "</ol>"),
				new ListType("[ivx]+[\.\)]\s", "<ol type=\"i\">", "</ol>"),
				new ListType("[\d]+[\.\)]\s" , "<ol>"           , "</ol>"),
				new ListType("[a-h][\.\)]\s" , "<ol type=\"a\">", "</ol>"),
				new ListType("[A-H][\.\)]\s" , "<ol type=\"A\">", "</ol>"),
				#new ListType("[\-]\s|[\+]\s" , "<ul>"           , "</ul>"),
				new ListType("[\-]\s" , "<ul>"           , "</ul>"),
				new ListType("[\+]\s" , "<ul>"           , "</ul>"),
			);
			foreach ($listTypes as $listType) {
				$rxListStart = "(\s*".$listType->listRegexIdentifyer."\s*)";
				$regex = '/			# matches the whole list
					( 					# group 1
							\n 			#		the beginning of a line
						| 				#	or
							^			#		the beginning of the string
						|
						\<p\>  # this could introduce error...
					)
					(?P<list>		# group 2
						'.$rxListStart.'#	a number followed by a period and a space
						.*?				#	everything after that
					)
					(?=					# group 3
						\n(				#	until a new line is encountered that does not start with a new list item or space
								[^\s\d\-\+a-hA-HIiVvXx\?]
							|
								[\d]+[^\.\)] 	# not a numbered list
							|					#
								[IVX]+[^\.\)]	# not an uppercase list of roman numerals
							|					#
								[ivx]+[^\.\)]	# not a lowercase list of roman numerals
							|					#
								[a-h][^\.\)]	# not a list of lowercase alphabetic characters
							|					#
								[A-H][^\.\)]	# not a list of uppercase alphabetic characters
							|					#
								\n              # two line-breaks terminates any list

						)
						|
							$
						|
							\<\/p\>(?!\<table\>) # this could introduce error...

					)
				/sx';
				$list = array();
				$match = preg_match_all($regex, $markdownText, $list);

				if ($match) {
					#print_r($list);
					for ($i=0; $i < count($list[0]); $i++) {
						$markdownList = $list[2][$i];

						$regex = '/ # matches a list item
							( 					# group 1
								\n 				#	the beginning of a line
								| 				#	or
								^				#	the beginning of the string
							)
							(?P<listType>		# group listType
								'.$rxListStart.'#	a number followed by a period and a space
							)
							(?P<listItem>			# group listItem
								.*?				#	the list item content
							)
							(?= 				# lookahead
								\n'.$rxListStart.'#		terminates when a newline and a list item at the same level is encountered
							 |					#	or
							 	$				#		terminates at the end of the string
							)
						/sx';

						$listItemCallback = function ($matches) {
							$regex = "/\n\s/";
							$listItem = preg_replace($regex, "\n", $matches['listItem']);
							if ($matches["listType"] == "? ") {
								$dashPos = strpos($listItem, "-");
								$term = trim(substr($listItem, 0, $dashPos));
								$definition = trim(substr($listItem, $dashPos+1));
								$listItem = "<dt>$term</dt><dd><p>$definition</p></dd>";
							} else {

								$listItem = "<li><p>".$listItem."</p></li>";
							}

						    return $listItem;
						};

						$htmlList     = $listType->listTagOpen.
									    preg_replace_callback($regex, $listItemCallback, $markdownList).
									    $listType->listTagClose;

						$replacement  = $this->paragraphCloseTag.$htmlList.$this->paragraphOpenTag;
						$markdownText = str_replace($markdownList, $replacement, $markdownText);
					}
				}
			}
			return $markdownText;
		}

		public function _getTableHeader($markdownTable) {
			$tableHeader = new TableHeader($markdownTable);
			#print_r($tableHeader->columnNames);
			#print_r($tableHeader->columnFormat);
			return $tableHeader;
		}
		public function _getTableBody($markdownTable) {
			$firstLB            = strpos($markdownTable, "\n" );
			$columnNamesString  = substr($markdownTable, 0,  $firstLB);
			$secondLb           = strpos($markdownTable, "\n", $firstLB+1);
			return substr($markdownTable, $secondLb+1);
		}



		public function _buildTables($markdownText) {

			$patternTable = '/
				(
					(^|\n)\s*\|
				)
				(?P<table>
					.*?
				)
				(?=
					\n\s*[^\|\s]
					|
					$
				)
			/sx';

			$tableCallback = function ($matches) {
				$markdownTable = $matches["table"];
				$tableHeader = $this->_getTableHeader($markdownTable);
				$tableBody = $this->_getTableBody($markdownTable);

				#echo "\n+".$matches["table"]."+\n";

				$rows = explode("\n", $tableBody);
				$table = array();
				for ($i=0; $i < count($rows); $i++) {
					$table[$i] = array_map('trim', explode("|", $rows[$i]));
					if ($table[$i][count($table[$i]) - 1] == "") {
						array_pop($table[$i]);
					}
					if ($table[$i][0] == "") {
						array_shift($table[$i]);
					}
				}

				$htmlTable = "<table><thead><tr>";
				for ($i=0; $i < count($tableHeader->columnNames); $i++) {
				 	$format = $tableHeader->getAlignAttribute($i);
					$htmlTable .= "<th$format>".$tableHeader->columnNames[$i]."</th>";
				 }
				$htmlTable .= "</tr></thead><tbody>";
				foreach ($table as $row) {
					$htmlTable .= "<tr>";
					for ($i=0; $i < count($row); $i++) {
						$format = $tableHeader->getAlignAttribute($i);
						$htmlTable .= "<td$format>".$row[$i]."</td>";
					}
					$htmlTable .= "</tr>";
				}
				$htmlTable .= "</table>";
				return "\n".$tableHeader->indent."</p>".$htmlTable."<p>";
			};
			$markdownText = preg_replace_callback($patternTable, $tableCallback, $markdownText);


			#echo "\n+".$markdownText."+\n";

			return $markdownText;
		}
	}
	class TableHeader {
		const ALIGN_DEFAULT = 0;
		const ALIGN_LEFT = 1;
		const ALIGN_RIGHT = 2;
		const ALIGN_CENTER = 3;

		public $columnNames, $columnFormat;
		public function __construct($markdownTable) {
			$firstLB            = strpos($markdownTable, "\n" );
			$columnNamesString  = substr($markdownTable, 0,  $firstLB);
			$secondLb           = strpos($markdownTable, "\n", $firstLB+1);
			$columnFormatString = substr($markdownTable, $firstLB+1,  $secondLb - $firstLB-2);

			$this->indent = substr($columnFormatString, 0, strlen($columnFormatString) - strlen(ltrim($columnFormatString)));
			#echo "-".$this->indent."-\n";

			$this->columnNames = array_map('trim', explode("|", $columnNamesString));
			$this->columnFormat = explode("|", $columnFormatString);

			if ($this->columnNames[count($this->columnNames)-1] == "") {
				array_pop($this->columnNames);
			}
			if ($this->columnNames[0] == "") {
				array_shift($this->columnNames);
			}
			if ($this->columnFormat[count($this->columnFormat)-1] == "") {
				array_pop($this->columnFormat);
			}
			if ($this->columnFormat[0] == "") {
				array_shift($this->columnFormat);
			}
			for ($i=0; $i < count($this->columnFormat); $i++) {
				$firstChar = $this->columnFormat[$i][0];
				$lastChar = $this->columnFormat[$i][strlen($this->columnFormat[$i])-1];
				$firstVal = ($firstChar == ":") ? 1 : 0;
				$lastVal = ($lastChar == ":") ? 2 : 0;
				$this->columnFormat[$i] = $firstVal + $lastVal;
			}
			#print_r($this->columnNames);
		}
		public function getAlignAttribute($columnNbr) {
			$format = "";
			if ($columnNbr < count($this->columnFormat)) {
				switch ($this->columnFormat[$columnNbr]) {
				 	case 1:
				 		$format = " align=\"left\"";
				 		break;
				 	case 2:
				 		$format = " align=\"right\"";
				 		break;
				 	case 3:
				 		$format = " align=\"center\"";
				 		break;

				 	case 0:
				 	default:
				 		$format = "";
				 		break;
				}
			}
			return $format;
		}
	}

	class ListType {
		public function __construct($listRegexIdentifyer, $listTagOpen, $listTagClose) {
			$this->listTagOpen = $listTagOpen;
			$this->listTagClose = $listTagClose;
			$this->listRegexIdentifyer = $listRegexIdentifyer;
		}
	}
}