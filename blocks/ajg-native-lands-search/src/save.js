/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into `post_content`.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#save
 *
 * @return {WPElement} Element to render.
 */
export default function save({attributes}) {
	const { blockId } = attributes;
	return (
		<div { ...useBlockProps.save() }>
			<div className="wp-block-ajgnl-ajg-native-lands-search__intro">
				<h4 className="wp-block-ajgnl-ajg-native-lands-search__headline">Are you on unceded indigenous land?</h4>
				<p className="wp-block-ajgnl-ajg-native-lands-search__description">Enter a street address, town, or zip code to see whose.</p>
			</div>
			<form action="javascript:void(0);" className="wp-block-ajgnl-ajg-native-lands-search__search" data-ajgnls-search={blockId}>
				<input type="text" data-ajgnls-query={blockId} />
				<input type="submit" value="Tell me." data-ajgnls-submit={blockId} />
			</form>
			<p className="wp-block-ajgnl-ajg-native-lands-search__error">There was a problem searching for that location.<br/><a href="https://native-land.ca" target="_blank" rel="noopener">Learn more -- explore the full map.</a></p>
			<p className="wp-block-ajgnl-ajg-native-lands-search__no-results">There are no indigenous nations that correspond to that location; results are most likely for folks in the Americans and Oceania.<br/><a href="https://native-land.ca" target="_blank" rel="noopener">Learn more -- explore the full map.</a></p>
			<div className="wp-block-ajgnl-ajg-native-lands-search__success">
				<p>That location has historically been the home of the:</p>
				<ul className="wp-block-ajgnl-ajg-native-lands-search__list"></ul>
				<p><a href="https://native-land.ca" target="_blank" rel="noopener">Learn more -- explore the full map.</a></p>
			</div>
			<p className="wp-block-ajgnl-ajg-native-lands-search__thanks">Thanks to <a href="https://native-land.ca" target="_blank" rel="noopener">Native Land Digital</a> for creating and maintaining the map and API that power this tool.</p>
		</div>
	);
}