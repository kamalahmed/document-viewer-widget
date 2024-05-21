<?php
/**
 * Server-side rendering of the `create-block/document-viewer` block.
 *
 * @param array $attributes The block attributes.
 * @param string $content The block default content.
 * @param WP_Block $block The block instance.
 */

$doc_type = $attributes['docType'] ?? '';
$doc_url =  $attributes['docUrl'] ?? '';
$show_download_button = $attributes['showDownloadButton'] ?? '';
$download_button_text = $attributes['downloadButtonText'] ?? '';
$dom_id = 'document-viewer-' . wp_generate_uuid4();

// only loads the scripts that is needed for better performance
switch ( $doc_type ) {
	case 'pdf':
		wp_enqueue_script('dv-pdfobject');
		break;
	case 'markdown':
		wp_enqueue_script('dv-marked');
	case 'docx':
		wp_enqueue_script('dv-mammoth');
		break;
	case 'excel':
		wp_enqueue_script('dv-xlsx');
		break;
}

?>
<div <?php echo get_block_wrapper_attributes(['class'=> 'dv-container']); ?> data-doc-type="<?php echo esc_attr( $doc_type ); ?>" data-doc-url="<?php echo esc_url( $doc_url ); ?>">
	<!-- Placeholder for rendering the document -->
	<div id="<?php echo esc_attr($dom_id); ?>"></div>
	<?php if ( $show_download_button ) : ?>
		<p class="dv-btn-container">
			<a href="<?php echo esc_url( $doc_url ); ?>" target="_blank" class="wp-block-file__button" download>
				<?php echo esc_html( $download_button_text ); ?>
			</a>
		</p>
	<?php endif; ?>
</div>
<script>
	(function() {
		const domId = '<?php echo esc_js($dom_id); ?>';
		const docType = '<?php echo esc_js($doc_type); ?>';
		const docUrl = '<?php echo esc_js($doc_url); ?>';

		function renderDocument() {
			if (docType === 'pdf') {
				PDFObject.embed(docUrl, `#${domId}`);
			} else if (docType === 'markdown') {
				fetch(docUrl)
					.then(response => response.text())
					.then(text => {
						document.getElementById(domId).innerHTML = marked.parse(text);
					});
			} else if (docType === 'docx') {
				fetch(docUrl)
					.then(response => response.arrayBuffer())
					.then(arrayBuffer => mammoth.convertToHtml({ arrayBuffer }))
					.then(result => {
						document.getElementById(domId).innerHTML = result.value;
					})
					.catch(console.error);
			} else if (docType === 'excel') {
				fetch(docUrl)
					.then(response => response.arrayBuffer())
					.then(arrayBuffer => {
						const data = new Uint8Array(arrayBuffer);
						const workbook = XLSX.read(data, { type: "array" });
						const html = XLSX.utils.sheet_to_html(workbook.Sheets[workbook.SheetNames[0]]);
						document.getElementById(domId).innerHTML = html;
					})
					.catch(console.error);
			}
		}


		document.addEventListener("DOMContentLoaded", renderDocument);

	})();
</script>
