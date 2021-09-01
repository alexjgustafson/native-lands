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
	const { blockId, cta, description, headline } = attributes;
	return (
		<div { ...useBlockProps.save() }>
			<div className="wp-block-ajgnl-ajg-native-lands-search__intro">
				{headline.length > 0 &&
					<h4 className="wp-block-ajgnl-ajg-native-lands-search__headline">{headline}</h4>
				}
				{description.length > 0 &&
					<p className="wp-block-ajgnl-ajg-native-lands-search__description">{description}</p>
				}
			</div>
			<form action="javascript:void(0);" className="wp-block-ajgnl-ajg-native-lands-search__search" data-ajgnls-search={blockId}>
				<input type="text" data-ajgnls-query={blockId} />
				<input type="submit" value={cta} data-ajgnls-submit={blockId} />
			</form>
			<p className="wp-block-ajgnl-ajg-native-lands-search__error">There was a problem searching for that location.<br/><a href="https://native-land.ca" target="_blank" rel="noopener">Learn more -- explore the full map.</a></p>
			<p className="wp-block-ajgnl-ajg-native-lands-search__no-results">There are no indigenous nations that correspond to that location; results are most likely for folks in the Americans and Oceania.<br/><a href="https://native-land.ca" target="_blank" rel="noopener">Learn more -- explore the full map.</a></p>
			<div className="wp-block-ajgnl-ajg-native-lands-search__success">
				<p>That location has historically been the home of the:</p>
				<ul className="wp-block-ajgnl-ajg-native-lands-search__list"></ul>
				<p><a href="https://native-land.ca" target="_blank" rel="noopener">Learn more -- explore the full map.</a></p>
			</div>
		</div>
	);
}
