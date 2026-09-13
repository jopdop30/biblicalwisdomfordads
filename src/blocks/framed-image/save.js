import { useBlockProps } from '@wordpress/block-editor';
import { frameClassName, imageStyle } from './props';

export default function save( { attributes } ) {
	const { id, url, alt, panel, maxWidth, aspectRatio, width, height } = attributes;
	if ( ! url ) {
		return null;
	}
	const blockProps = useBlockProps.save( {
		className: frameClassName( attributes ),
		style: maxWidth ? { maxWidth } : undefined,
	} );

	return (
		<figure { ...blockProps }>
			{ panel && <span className="bwfd-framed-image__panel" aria-hidden="true" /> }
			<img
				src={ url }
				alt={ alt }
				className={ id ? `wp-image-${ id }` : undefined }
				width={ width || undefined }
				height={ height || undefined }
				style={ imageStyle( aspectRatio ) }
			/>
		</figure>
	);
}
