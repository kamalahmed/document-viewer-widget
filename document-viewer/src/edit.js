/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps, InspectorControls, MediaUpload } from '@wordpress/block-editor';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

import { PanelBody, SelectControl, ToggleControl, TextControl, Button } from '@wordpress/components';

const Edit = ({ attributes, setAttributes }) => {
	const { docType, docUrl, showDownloadButton, downloadButtonText } = attributes;

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Document Viewer Settings', 'document-viewer')}>
					<SelectControl
						label={__('Document Type', 'document-viewer')}
						value={docType}
						options={[
							{ label: __('PDF', 'document-viewer'), value: 'pdf' },
							{ label: __('Markdown', 'document-viewer'), value: 'markdown' },
							{ label: __('DOCX', 'document-viewer'), value: 'docx' },
							{ label: __('Excel', 'document-viewer'), value: 'excel' }
						]}
						onChange={(newDocType) => setAttributes({ docType: newDocType })}
					/>
					<MediaUpload
						onSelect={(media) => setAttributes({ docUrl: media.url })}
						allowedTypes={[
							'application/pdf',
							'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
							'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
							'text/markdown',
							'text/plain'
						]}
						render={({ open }) => (
							<Button onClick={open} isPrimary>{__('Select Document', 'document-viewer')}</Button>
						)}
					/>
					<ToggleControl
						label={__('Show Download Button', 'document-viewer')}
						checked={showDownloadButton}
						onChange={(newShowDownloadButton) => setAttributes({ showDownloadButton: newShowDownloadButton })}
					/>
					{showDownloadButton && (
						<TextControl
							label={__('Download Button Text', 'document-viewer')}
							value={downloadButtonText}
							onChange={(newDownloadButtonText) => setAttributes({ downloadButtonText: newDownloadButtonText })}
						/>
					)}
				</PanelBody>
			</InspectorControls>
			<div { ...useBlockProps() }>
				{__('Document Viewer', 'document-viewer')}
				{docUrl && <p>{__('Document URL:', 'document-viewer')} {docUrl}</p>}
			</div>
		</>
	);
};

export default Edit;
