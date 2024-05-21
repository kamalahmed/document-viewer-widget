import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls, MediaUpload } from '@wordpress/block-editor';
import { Button, PanelBody, ToggleControl, TextControl } from '@wordpress/components';
import './editor.scss';

// Import SVG files
import pdfIcon from './assets/pdf.svg';
import markdownIcon from './assets/markdown.svg';
import docxIcon from './assets/docx.svg';
import excelIcon from './assets/excel.svg';

// SVG icons for each document type
const fileIcons = {
	pdf: <img src={pdfIcon} alt="PDF" style={{ width: '100px', height: '130px' }} />,
	markdown: <img src={markdownIcon} alt="Markdown" style={{ width: '100px', height: '130px' }} />,
	docx: <img src={docxIcon} alt="DOCX" style={{ width: '100px', height: '130px' }} />,
	excel: <img src={excelIcon} alt="Excel" style={{ width: '100px', height: '130px' }} />,
};
// excel and docx has long subtype name so make it consistent with other types
const fileTypes = {
	"vnd.openxmlformats-officedocument.spreadsheetml.sheet": "excel",
	"vnd.openxmlformats-officedocument.wordprocessingml.document":"docx",
	pdf:"pdf",
	markdown:"markdown",
}

const Edit = ({ attributes, setAttributes }) => {
	const { docType, docUrl, showDownloadButton, downloadButtonText } = attributes;

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Document Viewer Settings', 'document-viewer')}>
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
				{!docUrl && (
					<MediaUpload
						onSelect={(media) => {
							setAttributes({docUrl: media.url, docType: fileTypes[media.subtype]})
						}}
						allowedTypes={[
							'application/pdf',
							'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
							'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
							'text/markdown',
							'text/plain'
						]}
						render={({ open }) => (
							<Button onClick={open} isPrimary>
								{__('Select a Document', 'document-viewer')}
							</Button>
						)}
					/>
				)}
				{docUrl && (
					<div className="document-viewer-preview dv-container">
						{fileIcons[docType]}
						<p className="document-note">{__('Document will be rendered in the frontend.', 'document-viewer')}</p>
						{showDownloadButton && (
							<div className="dv-btn-container">
								<a href={docUrl} target="_blank" className="wp-block-file__button"
								   download>
									{downloadButtonText}
								</a>
							</div>

						)}

						<Button onClick={() => setAttributes({docUrl: '', docType: ''})} isSecondary>
							{__('Remove Document', 'document-viewer')}
						</Button>
					</div>
				)}
			</div>
		</>
	);
};

export default Edit;
