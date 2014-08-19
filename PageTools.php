<?php
/**
 * Provides parser functions to retrieve/set useful page level information.
 *
 * @author   Jean-Lou Dupont (http://www.bluecortex.com)
 * @author   Stephan Gambke
 *
 * @defgroup PageTools PageTools
 *
 * Features:
 * *********
 *
 * {{#pageincategory: 'category' }}
 *    returns 'true' if the current page is categorised with 'category'
 *
 * {{#pagenumcategories:}}
 *    returns the number of categories found for the current page.
 *
 * {{#pagecategory: 'index' }}
 *    returns the category title indexed with 'index'
 *
 * {{#pagetitle: new title name}}
 *
 * {{#pagetitleadd: text to be added to the title name}}
 *
 * {{#pagesubtitle: text to be added to the page's subtitle }}
 *
 * <deflate>text  to be    deflated</deflate>
 *    replaces all accumulations of whitespace (incl. newlines and tabs) by one single whitespace
 *
 * HISTORY:
 * -- Version 1.0: initial availability
 * -- Version 1.1: Added 'pagetitle': to modify the page's title.
 *                 Added 'pagetitleadd': to add to the current page title.
 *                 Added 'pagesubtitle': to modify the page's subtitle
 * -- Version 2.0: Complete overhaul for compatibility to MW 1.22+.
 * -- Version 2.1.0: Internationalisation format migrated to JSON.
 */

/**
 * The main file of the PageTools extension
 *
 * @copyright (C) 2013, Jean-Lou Dupont, Stephan Gambke
 * @license       http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 (or later)
 *
 * This file is part of the MediaWiki extension PageTools.
 * The PageTools extension is free software: you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * The PageTools extension is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @file
 * @ingroup       PageTools
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is part of a MediaWiki extension, it is not a valid entry point.' );
}

define( 'PT_VERSION', '2.2.0-alpha' );

$wgExtensionCredits[ 'parserhook' ][ ] = array (
	'path'           => __FILE__,
	'name'           => 'PageTools',
	'author'         => array ( '[http://www.mediawiki.org/wiki/User:Jldupont Jean-Lou Dupont]', '[http://www.mediawiki.org/wiki/User:F.trott Stephan Gambke]' ),
	'version'        => PT_VERSION,
	'url'            => 'https://www.mediawiki.org/wiki/Extension:PageTools',
	'descriptionmsg' => 'pagetools-desc',
);

$wgMessagesDirs[ 'PageTools' ]           = __DIR__ . '/i18n';
$wgExtensionMessagesFiles[ 'PageTools' ] = __DIR__ . '/PageTools.i18n.php';
$wgAutoloadClasses[ 'PageTools' ]        = __DIR__ . '/PageTools.class.php';

// Specify the function that will initialize the parser functions
$wgHooks[ 'ParserFirstCallInit' ][ ] = 'PageToolsSetupParserFunction';

/**
 * @param Parser $parser
 *
 * @return bool
 */
function PageToolsSetupParserFunction( Parser &$parser ) {

	$mgwords = array (
		'pageincategory'    => 'PageTools::renderPageInCategory',
		'pagenumcategories' => 'PageTools::renderPageNumCategories',
		'pagecategory'      => 'PageTools::renderPageCategory',
		'pagetitle'         => 'PageTools::renderPageTitle',
		'pagetitleadd'      => 'PageTools::renderPageTitleAdd',
		'pagesubtitle'      => 'PageTools::renderPageSubtitle',
	);

	// Create function hooks associating the magic words with the render functions and register magic words
	foreach ( $mgwords as $word => $handler ) {
		MagicWord::$mObjects[ $word ] = new MagicWord( $word, array ( $word ) );
		$parser->setFunctionHook( $word, $handler );
	}

	$parser->setHook( 'deflate', 'PageTools::renderDeflate' );

	// Return true so that MediaWiki continues to load extensions.
	return true;
}
