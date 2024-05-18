<?php

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class DV_Document_Viewer_Widget extends Widget_Base {

	public function get_name(): string {
		return 'document-viewer';
	}

	public function get_title(): ?string {
		return __('Document Viewer', 'document-viewer-widget');
	}

	public function get_icon(): string {
		return 'eicon-document-file';
	}

	public function get_categories(): array {
		return ['general'];
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_depends(): array {
		return ['dv-pdfobject', 'dv-marked', 'dv-mammoth', 'dv-xlsx'];
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends(): array {
		return ['dv-style'];
	}

	/**
	 * @inheritDoc
	 */
	public function get_keywords(): array {
		return [
			'pdf',
			'dv',
			'doc',
			'docx',
			'document',
			'excel',
			'csv',
			'markdown',
			'file',
			'sheet',
			'spreadsheet',
			'microsoft document',
			'microsoft',
			'ms doc',
			'ms docx',
		];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => __('Content', 'document-viewer-widget'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'doc_type',
			[
				'label' => __('Document Type', 'document-viewer-widget'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'pdf' => __('PDF', 'document-viewer-widget'),
					'markdown' => __('Markdown', 'document-viewer-widget'),
					'docx' => __('DOCX', 'document-viewer-widget'),
					'excel' => __('Excel', 'document-viewer-widget'),
				],
				'default' => 'pdf',
			]
		);

		$this->add_control(
			'document_file',
			[
				'label' => __('Upload Document', 'document-viewer-widget'),
				'type' => Controls_Manager::MEDIA,
				'media_types' => [
					'application/pdf',
					'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
					'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
					'text/markdown',
					'text/plain'
				],
				'default' => [
					'url' => '',
				],
			]
		);

		$this->add_control(
			'show_document',
			[
				'label' => esc_html__('Render Document', 'document-viewer-widget'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', 'document-viewer-widget'),
				'label_off' => esc_html__('No', 'document-viewer-widget'),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_download_button',
			[
				'label' => esc_html__('Download Button', 'document-viewer-widget'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Show', 'document-viewer-widget'),
				'label_off' => esc_html__('Hide', 'document-viewer-widget'),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'download_button_text',
			[
				'label' => esc_html__('Download Text', 'document-viewer-widget'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => esc_html__('Download the Document', 'document-viewer-widget'),
				'condition' => [
					'show_download_button' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$doc_type = $settings['doc_type'];
		$show_document = $settings['show_document'] ?? '';
		$show_download_button = $settings['show_download_button'] ?? '';
		$download_button_text = $settings['download_button_text'] ?? 'Download Document';
		$doc_url = esc_url($settings['document_file']['url']);
		$dom_id = "document-viewer-" . md5($doc_url);

		if ($doc_url) {
			echo '<div class="dv-container">'; // HTML container starts
			if ('yes' === $show_document) {
				?>
                <!-- Placeholder for rendering the document -->
                <div id="<?php echo esc_attr($dom_id); ?>"></div>
                <script>
                    (function() {
                        function embedPDF() {
                            PDFObject.embed("<?php echo esc_js($doc_url); ?>", "#<?php echo esc_js($dom_id); ?>");
                        }

                        function renderMarkdown() {
                            fetch("<?php echo esc_js($doc_url); ?>")
                                .then(response => response.text())
                                .then(text => {
                                    let markdownParsed = marked.parse(text); // Use marked.parse
                                    document.getElementById("<?php echo esc_js($dom_id); ?>").innerHTML = markdownParsed;
                                });
                        }

                        function renderDocx() {
                            fetch("<?php echo esc_js($doc_url); ?>")
                                .then(response => response.arrayBuffer())
                                .then(arrayBuffer => mammoth.convertToHtml({arrayBuffer: arrayBuffer}))
                                .then(result => {
                                    document.getElementById("<?php echo esc_js($dom_id); ?>").innerHTML = result.value;
                                })
                                .catch(error => console.log(error));
                        }

                        function renderExcel() {
                            fetch("<?php echo esc_js($doc_url); ?>")
                                .then(response => response.arrayBuffer())
                                .then(arrayBuffer => {
                                    let data = new Uint8Array(arrayBuffer);
                                    let workbook = XLSX.read(data, {type: "array"});
                                    let html = XLSX.utils.sheet_to_html(workbook.Sheets[workbook.SheetNames[0]]);
                                    document.getElementById("<?php echo esc_js($dom_id); ?>").innerHTML = html;
                                })
                                .catch(error => console.log(error));
                        }

                        if (window.elementorFrontend && window.elementorFrontend.isEditMode()) {
                            // Elementor editor context
                            if ("<?php echo esc_js($doc_type); ?>" === 'pdf') {
                                embedPDF();
                            } else if ("<?php echo esc_js($doc_type); ?>" === 'markdown') {
                                renderMarkdown();
                            } else if ("<?php echo esc_js($doc_type); ?>" === 'docx') {
                                renderDocx();
                            } else if ("<?php echo esc_js($doc_type); ?>" === 'excel') {
                                renderExcel();
                            }
                        } else {
                            // Frontend context
                            document.addEventListener("DOMContentLoaded", function() {
                                if ("<?php echo esc_js($doc_type); ?>" === 'pdf') {
                                    embedPDF();
                                } else if ("<?php echo esc_js($doc_type); ?>" === 'markdown') {
                                    renderMarkdown();
                                } else if ("<?php echo esc_js($doc_type); ?>" === 'docx') {
                                    renderDocx();
                                } else if ("<?php echo esc_js($doc_type); ?>" === 'excel') {
                                    renderExcel();
                                }
                            });
                        }
                    })();
                </script>
				<?php
			}
			if ('yes' === $show_download_button) {
				echo "<p class='dv-btn-container'><a href='$doc_url' target='_blank' class='wp-block-file__button' download=''>$download_button_text</a></p>";
			}
			echo '</div>'; // closing .container
		}
	}
}