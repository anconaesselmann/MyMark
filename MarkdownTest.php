<?php
namespace aae\ui {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class MarkdownTest extends \PHPUnit_Framework_TestCase {
		protected $paragraphOpenTag = "<p>";
		protected $paragraphCloseTag = "</p>";



		public function test___construct() {
			$obj = new Markdown();
		}

		/**
		 * @dataProvider dataProvider_getHTML_test__asterisc
		 */
		public function test_getHTML_test__asterisc($markdownText, $expected) {
			// Setup
			$obj = new Markdown();

			// Testing
			$result = $obj->getHTML($markdownText);

			// Verification
			$this->assertEquals($expected, $result);
		}
		public function dataProvider_getHTML_test__asterisc() {
			return array(
				array("this is a ***test string***.", "".$this->paragraphOpenTag."this is a <i><strong>test string</strong></i>.".$this->paragraphCloseTag.""),
				array("this is a ***1 test string***.", "".$this->paragraphOpenTag."this is a <i><strong>1 test string</strong></i>.".$this->paragraphCloseTag.""),
				array("***this is*** a test string.", "".$this->paragraphOpenTag."<i><strong>this is</strong></i> a test string.".$this->paragraphCloseTag.""),
				array("this is a test ***string.***", "".$this->paragraphOpenTag."this is a test <i><strong>string.</strong></i>".$this->paragraphCloseTag.""),
				array("this is a * test string*.", "".$this->paragraphOpenTag."this is a * test string*.".$this->paragraphCloseTag.""),
				array("this is a ** test string**.", "".$this->paragraphOpenTag."this is a ** test string**.".$this->paragraphCloseTag.""),
				array("this is a tes**ts**tring.", "".$this->paragraphOpenTag."this is a tes<strong>ts</strong>tring.".$this->paragraphCloseTag.""),
			);
		}

		/**
		 * @dataProvider dataProvider_getHTML_test_double_asterisc
		 */
		public function test_getHTML_test_double_asterisc($markdownText, $expected) {
			// Setup
			$obj = new Markdown();

			// Testing
			$result = $obj->getHTML($markdownText);

			// Verification
			$this->assertEquals($expected, $result);
		}
		public function dataProvider_getHTML_test_double_asterisc() {
			return array(
				array("this is a **test string**.", "".$this->paragraphOpenTag."this is a <strong>test string</strong>.".$this->paragraphCloseTag.""),
				array("**this is** a test string.", "".$this->paragraphOpenTag."<strong>this is</strong> a test string.".$this->paragraphCloseTag.""),
				array("this is a test **string.**", "".$this->paragraphOpenTag."this is a test <strong>string.</strong>".$this->paragraphCloseTag.""),
			);
		}

		/**
		 * @dataProvider dataProvider_getHTML_test_single_asterisc
		 */
		public function test_getHTML_test_single_asterisc($markdownText, $expected) {
			// Setup
			$obj = new Markdown();

			// Testing
			$result = $obj->getHTML($markdownText);

			// Verification
			$this->assertEquals($expected, $result);
		}
		public function dataProvider_getHTML_test_single_asterisc() {
			return array(
				array("this is a *test string*.", "".$this->paragraphOpenTag."this is a <i>test string</i>.".$this->paragraphCloseTag.""),
				array("*this is* a test string.", "".$this->paragraphOpenTag."<i>this is</i> a test string.".$this->paragraphCloseTag.""),
				array("this is a test *string.*", "".$this->paragraphOpenTag."this is a test <i>string.</i>".$this->paragraphCloseTag.""),
			);
		}

		/**
		 * @dataProvider dataProvider_getHTML_line_breaks
		 */
		public function test_getHTML_line_breaks($markdownText, $expected) {
			// Setup
			$obj = new Markdown();

			// Testing
			$result = $obj->getHTML($markdownText);

			// Verification
			$this->assertEquals($expected, $result);
		}
		public function dataProvider_getHTML_line_breaks() {
			return array(
				array("this is a\ntest string.", "".$this->paragraphOpenTag."this is a<br />test string.".$this->paragraphCloseTag.""),
				array("this is a\n\ntest string.", "".$this->paragraphOpenTag."this is a".$this->paragraphCloseTag."".$this->paragraphOpenTag."test string.".$this->paragraphCloseTag.""),
			);
		}

		/**
		 * @dataProvider dataProvider_getHTML_allow_HTML
		 */
		public function test_getHTML_allow_HTML($markdownText, $allowHTML, $expected) {
			// Setup
			$obj = new Markdown($allowHTML);

			// Testing
			$result = $obj->getHTML($markdownText);

			// Verification
			$this->assertEquals($expected, $result);
		}
		public function dataProvider_getHTML_allow_HTML() {
			return array(
				array("this is a <br />test <script>string</script>.", false, "".$this->paragraphOpenTag."this is a test string.".$this->paragraphCloseTag.""),
				array("this is a <br />test <script>string</script>.", true, "".$this->paragraphOpenTag."this is a <br />test <script>string</script>.".$this->paragraphCloseTag.""),
			);
		}

		/**
		 * @dataProvider dataProvider_getHTML_links
		 */
		public function test_getHTML_links($markdownText, $expected) {
			// Setup
			$obj = new Markdown();

			// Testing
			$result = $obj->getHTML($markdownText);

			// Verification
			$this->assertEquals($expected, $result);
		}
		public function dataProvider_getHTML_links() {
			return array(
				array("This is a link[1] to a website.\n\n[1]: /link.com\n[5]: /link2.com", "".$this->paragraphOpenTag."This is a <a href=\"/link.com\">link</a> to a website.".$this->paragraphCloseTag.""),
				array("This is a link[1] to a website.\n\n[1]:   /link.com    \n[5]: /link2.com", "".$this->paragraphOpenTag."This is a <a href=\"/link.com\">link</a> to a website.".$this->paragraphCloseTag.""),
				array("This is a link[1] to a website.\n\n[1]:/link.com\n[5]: /link2.com", "".$this->paragraphOpenTag."This is a <a href=\"/link.com\">link</a> to a website.".$this->paragraphCloseTag.""),
				array("This is [a link][1] to a website.\n\n[1]: /link.com\n[5]: /link2.com", "".$this->paragraphOpenTag."This is <a href=\"/link.com\">a link</a> to a website.".$this->paragraphCloseTag.""),
				array("A link to [http://anconaesselmann.com].\n", "".$this->paragraphOpenTag."A link to <a href=\"http://anconaesselmann.com\">http://anconaesselmann.com</a>.".$this->paragraphCloseTag.""),
				array("[http://anconaesselmann.com]\n", "".$this->paragraphOpenTag."<a href=\"http://anconaesselmann.com\">http://anconaesselmann.com</a>".$this->paragraphCloseTag.""),
				array("\n[http://anconaesselmann.com]", "".$this->paragraphOpenTag."<a href=\"http://anconaesselmann.com\">http://anconaesselmann.com</a>".$this->paragraphCloseTag.""),
				array("[http://anconaesselmann.com]", "".$this->paragraphOpenTag."<a href=\"http://anconaesselmann.com\">http://anconaesselmann.com</a>".$this->paragraphCloseTag.""),
			);
		}

	    	/**
		 * @dataProvider dataProvider_getHTML_images
		 */
		public function test_getHTML_images($markdownText, $expected) {
			// Setup
			$obj = new Markdown();

			// Testing
			$result = $obj->getHTML($markdownText);

			// Verification
			$this->assertEquals($expected, $result);
		}
		public function dataProvider_getHTML_images() {
			return array(
				array("This this image [image of a kitten][p1] was inserted with markdown.\n\n[p1;i]: http://localhost/kitten.jpg", "".$this->paragraphOpenTag."This this image <img src=\"http://localhost/kitten.jpg\" alt=\"image of a kitten\"> was inserted with markdown.".$this->paragraphCloseTag.""),
				array("This this image [image of a kitten][p1] was inserted with markdown.\n\n[p1;img]: imageClass|http://localhost/kitten.jpg", "".$this->paragraphOpenTag."This this image <img class=\"imageClass\" src=\"http://localhost/kitten.jpg\" alt=\"image of a kitten\"> was inserted with markdown.".$this->paragraphCloseTag.""),

			);
		}

		/**
		 * @dataProvider dataProvider_getHTML_videos
		 */
		public function test_getHTML_videos($markdownText, $expected) {
			// Setup
			$obj = new Markdown();

			// Testing
			$result = $obj->getHTML($markdownText);

			// Verification
			$this->assertEquals($expected, $result);
		}
		public function dataProvider_getHTML_videos() {
			return array(
				array("This a video: [video of a cat][i1].\n\n[i1;v]: http://www.youtube.com/embed/ousXAO4I7nY", "".$this->paragraphOpenTag."This a video: <iframe width=\"420\" height=\"315\" src=\"http://www.youtube.com/embed/ousXAO4I7nY\" frameborder=\"0\" allowfullscreen webkitallowfullscreen mozallowfullscreen oallowfullscreen msallowfullscreen></iframe>.".$this->paragraphCloseTag.""),
				array("This a video: [video of a cat][i1].\n\n[i1;v]: 200x100;videoClass|http://www.youtube.com/embed/ousXAO4I7nY", "".$this->paragraphOpenTag."This a video: <iframe class=\"videoClass\" width=\"100\" height=\"200\" src=\"http://www.youtube.com/embed/ousXAO4I7nY\" frameborder=\"0\" allowfullscreen webkitallowfullscreen mozallowfullscreen oallowfullscreen msallowfullscreen></iframe>.".$this->paragraphCloseTag.""),
			);
		}

		/**
		 * @dataProvider dataProvider_getHTML_headings
		 */
		public function test_getHTML_headings($markdownText, $expected) {
			// Setup
			$obj = new Markdown();

			// Testing
			$result = $obj->getHTML($markdownText);

			// Verification
			$this->assertEquals($expected, $result);
		}
		public function dataProvider_getHTML_headings() {
			return array(
				array("# This is a heading\n"                                  , "<h1>This is a heading</h1>"),
				array("# This is a heading"                                  , "<h1>This is a heading</h1>"),
				array("# This is a heading\nThis is not a heading."            , "<h1>This is a heading</h1>".$this->paragraphOpenTag."This is not a heading.".$this->paragraphCloseTag.""),
				array("# This is a heading     #######\nThis is not a heading.", "<h1>This is a heading</h1>".$this->paragraphOpenTag."This is not a heading.".$this->paragraphCloseTag.""),
				array("# This is a heading#######  \nThis is not a heading."   , "<h1>This is a heading</h1>".$this->paragraphOpenTag."This is not a heading.".$this->paragraphCloseTag.""),
				array("## This is a heading\nThis is not a heading."           , "<h2>This is a heading</h2>".$this->paragraphOpenTag."This is not a heading.".$this->paragraphCloseTag.""),
				array("### This is a heading\nThis is not a heading."          , "<h3>This is a heading</h3>".$this->paragraphOpenTag."This is not a heading.".$this->paragraphCloseTag.""),
				array("#### This is a heading\nThis is not a heading."         , "<h4>This is a heading</h4>".$this->paragraphOpenTag."This is not a heading.".$this->paragraphCloseTag.""),
				array("##### This is a heading\nThis is not a heading."        , "<h5>This is a heading</h5>".$this->paragraphOpenTag."This is not a heading.".$this->paragraphCloseTag.""),
				array("###### This is a heading\nThis is not a heading."       , "<h6>This is a heading</h6>".$this->paragraphOpenTag."This is not a heading.".$this->paragraphCloseTag.""),
				array("# This is a heading\nThis is not a heading.\n###### This is a heading\nThis is not a heading.", "<h1>This is a heading</h1>".$this->paragraphOpenTag."This is not a heading.".$this->paragraphCloseTag."<h6>This is a heading</h6>".$this->paragraphOpenTag."This is not a heading.".$this->paragraphCloseTag.""),
				array("# This is a heading\n###### This is a heading #####\n"                                  , "<h1>This is a heading</h1><h6>This is a heading</h6>"),
			);
		}
		/**
		 * @dataProvider dataProvider_getHTML_codeTags
		 */
		public function test_getHTML_codeTags($markdownText, $expected) {
			// Setup
			$obj = new Markdown();

			// Testing
			$result = $obj->getHTML($markdownText);

			// Verification
			$this->assertEquals($expected, $result);
		}
		public function dataProvider_getHTML_codeTags() {
			return array(
				array("Code Example:\n  \n    code1\n        code2\n    code3\n\nregular text."          , "".$this->paragraphOpenTag."Code Example:".$this->paragraphCloseTag."<pre class=\"prettyprint\"><code>code1<br />    code2<br />code3</code></pre>".$this->paragraphOpenTag."regular text.".$this->paragraphCloseTag.""),
				array("Code Example:\n  \n    code1\n\nText\n\n    code2\n        code3\n\nback to text.", "".$this->paragraphOpenTag."Code Example:".$this->paragraphCloseTag."<pre class=\"prettyprint\"><code>code1</code></pre>".$this->paragraphOpenTag."Text".$this->paragraphCloseTag."<pre class=\"prettyprint\"><code>code2<br />    code3</code></pre>".$this->paragraphOpenTag."back to text.".$this->paragraphCloseTag.""),
				array("Text\n\n    code1\n    \n    code2\n\ntext"                                              , "".$this->paragraphOpenTag."Text".$this->paragraphCloseTag."<pre class=\"prettyprint\"><code>code1<br /><br />code2</code></pre>".$this->paragraphOpenTag."text".$this->paragraphCloseTag.""),
				array("\n\n    code1\n    \n    code2\n\n"                                              , "<pre class=\"prettyprint\"><code>code1<br /><br />code2<br /></code></pre>"),
				array("#Text\n\n    code1\n    \n    code2\n\n"                                              , "<h1>Text</h1><pre class=\"prettyprint\"><code>code1<br /><br />code2<br /></code></pre>"),
			);
		}

		/**
		 * @dataProvider dataProvider_getHTML_horizontalLine
		 */
		public function test_getHTML_horizontalLine($markdownText, $expected) {
			// Setup
			$obj = new Markdown();

			// Testing
			$result = $obj->getHTML($markdownText);

			// Verification
			$this->assertEquals($expected, $result);
		}
		public function dataProvider_getHTML_horizontalLine() {
			return array(
				array("---", "<hr />"),
				array("text\n---", "".$this->paragraphOpenTag."text".$this->paragraphCloseTag."<hr />"),
				array("text\n-----------------------", "".$this->paragraphOpenTag."text".$this->paragraphCloseTag."<hr />"),
				array("#Some title\nSome text\n---", "<h1>Some title</h1>".$this->paragraphOpenTag."Some text".$this->paragraphCloseTag."<hr />"),
			);
		}


		/**
		 * @dataProvider dataProvider_getHTML_list
		 */
		public function test_getHTML_list($markdownText, $expected) {
			// Setup
			$obj = new Markdown();

			// Testing
			$result = $obj->getHTML($markdownText);

			// Verification
			$this->assertEquals($expected, $result);
		}
		public function dataProvider_getHTML_list() {
			return array(
				array("\n1 + 2 = 3\n", "".$this->paragraphOpenTag."1 + 2 = 3".$this->paragraphCloseTag.""),
				array("\n1. First item\n", "<ol><li><p>First item</p></li></ol>"),
				array("\n1) First item\n", "<ol><li><p>First item</p></li></ol>"),
				array("\n 1. First item\n", "<ol><li><p>First item</p></li></ol>"),
				array("\n123456. First item\n", "<ol><li><p>First item</p></li></ol>"),
			##array("\n123456. First item\n123456. Second item\n", "<ol><li><p>First item</p></li><li><p>Second item</p></li></ol>"),
				array("\n1. First item\n2. Second item\n3. Thired item\ntest", "<ol><li><p>First item</p></li><li><p>Second item</p></li><li><p>Thired item</p></li></ol>".$this->paragraphOpenTag."test".$this->paragraphCloseTag.""),
				array("1. First item\n2. Second item\n3. Thired item\ntest", "<ol><li><p>First item</p></li><li><p>Second item</p></li><li><p>Thired item</p></li></ol>".$this->paragraphOpenTag."test".$this->paragraphCloseTag.""),
				array("1. First item", "<ol><li><p>First item</p></li></ol>"),
				array("1. First item\n2. Second item\n3. Thired item", "<ol><li><p>First item</p></li><li><p>Second item</p></li><li><p>Thired item</p></li></ol>"),
				array("1. First item\n2. Second item\n3. Thired item\ntext\n1. First item\n2. Second item\n3. Thired item", "<ol><li><p>First item</p></li><li><p>Second item</p></li><li><p>Thired item</p></li></ol>".$this->paragraphOpenTag."text".$this->paragraphCloseTag."<ol><li><p>First item</p></li><li><p>Second item</p></li><li><p>Thired item</p></li></ol>"),
				array("1. First item\n still first\n2. Second item\n3. Thired item\ntest", "<ol><li><p>First item<br />still first</p></li><li><p>Second item</p></li><li><p>Thired item</p></li></ol>".$this->paragraphOpenTag."test".$this->paragraphCloseTag.""),
				array("1. First item\n still first\n stiiill\n2. Second item\n3. Thired item\ntest", "<ol><li><p>First item<br />still first<br />stiiill</p></li><li><p>Second item</p></li><li><p>Thired item</p></li></ol>".$this->paragraphOpenTag."test".$this->paragraphCloseTag.""),
				array("1. First item\n 12345 test\n2. Second item\n3. Thired item\ntest", "<ol><li><p>First item<br />12345 test</p></li><li><p>Second item</p></li><li><p>Thired item</p></li></ol>".$this->paragraphOpenTag."test".$this->paragraphCloseTag.""),
				array("#header\n1. First item", "<h1>header</h1><ol><li><p>First item</p></li></ol>"),
				array("1. First item\n\n2. Second item\n3. Thired item\ntest", "<ol><li><p>First item</p></li></ol><ol><li><p>Second item</p></li><li><p>Thired item</p></li></ol>".$this->paragraphOpenTag."test".$this->paragraphCloseTag.""),
				array("1. First item\n123 text", "<ol><li><p>First item</p></li></ol>".$this->paragraphOpenTag."123 text".$this->paragraphCloseTag.""),

				array("\n- First item\n", "<ul><li><p>First item</p></li></ul>"),
				array("\n+ First item\n", "<ul><li><p>First item</p></li></ul>"),

				array("\nI. First Item\n", "<ol type=\"I\"><li><p>First Item</p></li></ol>"),
				array("\ni. First Item\n", "<ol type=\"i\"><li><p>First Item</p></li></ol>"),
				array("\nI) First Item\n", "<ol type=\"I\"><li><p>First Item</p></li></ol>"),
				array("\nI. First Item\nI. Second Item\nI. Third Item\n", "<ol type=\"I\"><li><p>First Item</p></li><li><p>Second Item</p></li><li><p>Third Item</p></li></ol>"),
			##array("\nI. First Item\nII. Second Item\nIII. Third Item\n", "<ol type=\"I\"><li><p>First Item</p></li><li><p>Second Item</p></li><li><p>Third Item</p></li></ol>"),
				array("\nI. First Item\nV. Second Item\nX. Third Item\n", "<ol type=\"I\"><li><p>First Item</p></li><li><p>Second Item</p></li><li><p>Third Item</p></li></ol>"),
				array("\ni. First Item\nv. Second Item\nx. Third Item\n", "<ol type=\"i\"><li><p>First Item</p></li><li><p>Second Item</p></li><li><p>Third Item</p></li></ol>"),


				array("\nA. First Item\n", "<ol type=\"A\"><li><p>First Item</p></li></ol>"),
				array("\na. First Item\n", "<ol type=\"a\"><li><p>First Item</p></li></ol>"),
				array("\nA) First Item\n", "<ol type=\"A\"><li><p>First Item</p></li></ol>"),
				array("\nA. First Item\nA. Second Item\nA. Third Item\n", "<ol type=\"A\"><li><p>First Item</p></li><li><p>Second Item</p></li><li><p>Third Item</p></li></ol>"),
				array("\nA. First Item\nB. Second Item\nC. Third Item\n", "<ol type=\"A\"><li><p>First Item</p></li><li><p>Second Item</p></li><li><p>Third Item</p></li></ol>"),
				array("\na. First Item\nb. Second Item\nc. Third Item\n", "<ol type=\"a\"><li><p>First Item</p></li><li><p>Second Item</p></li><li><p>Third Item</p></li></ol>"),

				array("\n1. First item\n First item sentence\n - First bullet\n  First bullet sentence 2\n2. Second item", "<ol><li><p>First item<br />First item sentence</p><ul><li><p>First bullet<br />First bullet sentence 2</p></li></ul></li><li><p>Second item</p></li></ol>"),

				array("\n? Term - Definition\n", "<dl><dt>Term</dt><dd><p>Definition</p></dd></dl>"),
				array("? Term - Definition\n", "<dl><dt>Term</dt><dd><p>Definition</p></dd></dl>"),
				array("\n? Term - Definition", "<dl><dt>Term</dt><dd><p>Definition</p></dd></dl>"),
				array("? Term     -     Definition", "<dl><dt>Term</dt><dd><p>Definition</p></dd></dl>"),

			);
		}

		/**
		 * @dataProvider dataProvider_getHTML_tables
		 */
		public function test_getHTML_tables($markdownText, $expected) {
			// Setup
			$obj = new Markdown();

			// Testing
			$result = $obj->getHTML($markdownText);

			// Verification
			$this->assertEquals($expected, $result);
		}
		public function dataProvider_getHTML_tables() {
			return array(
				array("|Name1|\n|-|\n|value|", "<table><thead><tr><th>Name1</th></tr></thead><tbody><tr><td>value</td></tr></table>"),
				array(" |Name1|\n |-|\n |value|", "<table><thead><tr><th>Name1</th></tr></thead><tbody><tr><td>value</td></tr></table>"),
				array("\n |Name1|\n |-|\n |value|", "<table><thead><tr><th>Name1</th></tr></thead><tbody><tr><td>value</td></tr></table>"),
				array("\n     |Name1|\n           |-|\n           |value|", "<table><thead><tr><th>Name1</th></tr></thead><tbody><tr><td>value</td></tr></table>"),
				array(" |Name1|\n |-|\n |value|\n", "<table><thead><tr><th>Name1</th></tr></thead><tbody><tr><td>value</td></tr></table>"),
				array("text\n\n\n|Name1|\n|-|\n|value|", "<p>text</p><table><thead><tr><th>Name1</th></tr></thead><tbody><tr><td>value</td></tr></table>"),
				array("|Name1|\n|-|\n|value|\n", "<table><thead><tr><th>Name1</th></tr></thead><tbody><tr><td>value</td></tr></table>"),
				array("|Name1|\n|-|\n|value|\n\ntest", "<table><thead><tr><th>Name1</th></tr></thead><tbody><tr><td>value</td></tr></table><p>test</p>"),
				array("|Name1|Name2|Name3|Name4|\n|-|-|-|-|\n|1a|1b|1c|1d|\n|2a|2b|2c|2d|\n|3a|3b|3c|3d|", "<table><thead><tr><th>Name1</th><th>Name2</th><th>Name3</th><th>Name4</th></tr></thead><tbody><tr><td>1a</td><td>1b</td><td>1c</td><td>1d</td></tr><tr><td>2a</td><td>2b</td><td>2c</td><td>2d</td></tr><tr><td>3a</td><td>3b</td><td>3c</td><td>3d</td></tr></table>"),
				array("|Name1|Name2|Name3|Name4|\n|:-|:-:|-:|-|\n|1a|1b|1c|1d|", "<table><thead><tr><th align=\"left\">Name1</th><th align=\"center\">Name2</th><th align=\"right\">Name3</th><th>Name4</th></tr></thead><tbody><tr><td align=\"left\">1a</td><td align=\"center\">1b</td><td align=\"right\">1c</td><td>1d</td></tr></table>"),
				array("|Name1|\n|-|\n|value|\ntext\n|Name1|\n|-|\n|value|", "<table><thead><tr><th>Name1</th></tr></thead><tbody><tr><td>value</td></tr></table><p>text</p><table><thead><tr><th>Name1</th></tr></thead><tbody><tr><td>value</td></tr></table>"),
				array("|Name1|\n|-|\n|value|\ntext\n    |Name1|\n    |-|\n    |value|", "<table><thead><tr><th>Name1</th></tr></thead><tbody><tr><td>value</td></tr></table><p>text</p><table><thead><tr><th>Name1</th></tr></thead><tbody><tr><td>value</td></tr></table>"),
			);
		}

		/**
		 * @dataProvider dataProvider_getHTML_combining_elements
		 */
		public function test_getHTML_combining_elements($markdownText, $expected) {
			// Setup
			$obj = new Markdown();

			// Testing
			$result = $obj->getHTML($markdownText);

			// Verification
			$this->assertEquals($expected, $result);
		}
		public function dataProvider_getHTML_combining_elements() {
			return array(
				array("1) A list with a table\n |Name1|\n |-|\n |value|\ntext", "<ol><li><p>A list with a table</p><table><thead><tr><th>Name1</th></tr></thead><tbody><tr><td>value</td></tr></table></li></ol><p>text</p>"),
			);
		}


		/**
		 * @dataProvider dataProvider_getHTML_span_css_id
		 */
		public function test_getHTML_span_css_id($markdownText, $expected) {
			// Setup
			$obj = new Markdown();

			// Testing
			$result = $obj->getHTML($markdownText);

			// Verification
			$this->assertEquals($expected, $result);
		}
		public function dataProvider_getHTML_span_css_id() {
			return array(
				array("{#id-name this is special}",
					"<p><span id=\"id-name\">this is special</span></p>"),
				array("not special\n{#id-name this is special}\nnot special",
					"<p>not special<br /><span id=\"id-name\">this is special</span><br />not special</p>"),
			);
		}
		/**
		 * @dataProvider dataProvider_getHTML_span_css_class
		 */
		public function test_getHTML_span_css_class($markdownText, $expected) {
			// Setup
			$obj = new Markdown();

			// Testing
			$result = $obj->getHTML($markdownText);

			// Verification
			$this->assertEquals($expected, $result);
		}
		public function dataProvider_getHTML_span_css_class() {
			return array(
				array("{.class-name this is special}",
					"<p><span class=\"class-name\">this is special</span></p>"),
				array("not special\n{.class-name this is special}\nnot special",
					"<p>not special<br /><span class=\"class-name\">this is special</span><br />not special</p>"),
			);
		}



// []:


		/**
		 * @dataProvider dataProvider_getHTML_insert_global_links
		 */
		public function test_getHTML_insert_global_links($markdownText, $expected) {
			// Setup
			$obj = new Markdown();

			$_SERVER["DOCUMENT_ROOT"] = dirname(__FILE__).DIRECTORY_SEPARATOR."MarkdownTestData";

			// echo "Document root:".$_SERVER["DOCUMENT_ROOT"];

			// Testing
			$result = $obj->getHTML($markdownText);

			// Verification
			$this->assertEquals($expected, $result);
		}
		public function dataProvider_getHTML_insert_global_links() {
			return array(
				array("testing\n[hello][link1], [hi][link2]\nend\n[__GLOBAL__]: /links.md", '<p>testing<br /><a href="/link1/abc">hello</a>, <a href="/link2/abc">hi</a><br />end</p>')
			);
		}


		/**
		 * @dataProvider dataProvider_getHTML_set_ids_for_dl_elements
		 */
		public function test_getHTML_set_ids_for_dl_elements($markdownText, $expected) {
			// Setup
			$obj = new Markdown();

			// Testing
			$result = $obj->getHTML($markdownText);

			// Verification
			$this->assertEquals($expected, $result);
		}
		public function dataProvider_getHTML_set_ids_for_dl_elements() {
			return array(
				array("? {Main} Part - Sub", '<dl><dt id="main-list">Main Part</dt><dd><p>Sub</p></dd></dl>'),
				array("? {Main With Other} Part - Sub", '<dl><dt id="main-with-other-list">Main With Other Part</dt><dd><p>Sub</p></dd></dl>'),
				array("[{Caption} With Part][link]\n[link]: /test\n", '<p><a href="/test" id="caption-link">Caption With Part</a></p>'),
				array("? [{Main}][link] Part - Sub\n[link]: /test\n", '<dl><dt><a href="/test" id="main-link">Main</a> Part</dt><dd><p>Sub</p></dd></dl>'),

			);
		}

	}
	/*
	Write test for:
"

"\nthis is not special\n<#id-name this is special>\nthis is not special either\n",
					"<ol><li><p>A list with a table</p><table><thead><tr><th>Name1</th></tr></thead><tbody><tr><td>value</td></tr></table></li></ol><p>text</p>"



## Code
"
	 */
}