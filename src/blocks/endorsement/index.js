import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import metadata from './block.json';
import './style.scss';

registerBlockType( metadata.name, {
	edit: ( { attributes, setAttributes } ) => {
		const blockProps = useBlockProps();
		return (
			<figure { ...blockProps }>
				<blockquote>
					<RichText
						tagName="p"
						className="bwfd-endorsement__quote"
						value={ attributes.quote }
						onChange={ ( quote ) => setAttributes( { quote } ) }
						placeholder={ __( 'Endorsement text…', 'bwfd' ) }
						allowedFormats={ [ 'core/italic', 'core/bold' ] }
					/>
				</blockquote>
				<RichText
					tagName="figcaption"
					className="bwfd-endorsement__who"
					value={ attributes.attribution }
					onChange={ ( attribution ) => setAttributes( { attribution } ) }
					placeholder={ __( 'Name, role, organisation', 'bwfd' ) }
					allowedFormats={ [ 'core/italic' ] }
				/>
			</figure>
		);
	},
	save: ( { attributes } ) => {
		const blockProps = useBlockProps.save();
		return (
			<figure { ...blockProps }>
				<blockquote>
					<RichText.Content tagName="p" className="bwfd-endorsement__quote" value={ attributes.quote } />
				</blockquote>
				<RichText.Content tagName="figcaption" className="bwfd-endorsement__who" value={ attributes.attribution } />
			</figure>
		);
	},
} );
