<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class DV_Document_Viewer_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'document-viewer';
	}

	public function get_title() {
		return __('Document Viewer', 'document-viewer-widget');
	}

	public function get_icon() {
		return 'fa fa-file';
	}

	public function get_categories() {
		return ['general'];
	}
	/**
	 * @inheritDoc
	 */
	public function get_script_depends() {
		$scripts   = parent::get_script_depends();
//		$scripts[] = 'handle';

		return apply_filters( 'dv/elementor/scripts', $scripts );
	}
	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		$styles   = parent::get_style_depends();
//		$styles[] = 'csshandle';

		return apply_filters( 'dv/elementor/styles', $styles );
	}
	/**
	 * @inheritDoc
	 */
	public function get_keywords() {
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
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'doc_type',
			[
				'label' => __('Document Type', 'document-viewer-widget'),
				'type' => \Elementor\Controls_Manager::SELECT,
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
				'type' => \Elementor\Controls_Manager::MEDIA,
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

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$doc_type = $settings['doc_type'];
		$doc_url = esc_url($settings['document_file']['url']);

		if ($doc_url) {
			echo '<div id="document-viewer"></div>';
			if ($doc_type === 'pdf') {
				echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        PDFObject.embed("' . $doc_url . '", "#document-viewer");
                    });
                </script>';
			} elseif ($doc_type === 'markdown') {
				echo '<script>
                    fetch("' . $doc_url . '")
                        .then(response => response.text())
                        .then(text => {
                            document.getElementById("document-viewer").innerHTML = marked(text);
                        });
                </script>';
			} elseif ($doc_type === 'docx') {
				echo '<script>
                    fetch("' . $doc_url . '")
                        .then(response => response.arrayBuffer())
                        .then(arrayBuffer => mammoth.convertToHtml({arrayBuffer: arrayBuffer}))
                        .then(result => {
                            document.getElementById("document-viewer").innerHTML = result.value;
                        })
                        .catch(error => console.log(error));
                </script>';
			} elseif ($doc_type === 'excel') {
				echo '<script>
                    fetch("' . $doc_url . '")
                        .then(response => response.arrayBuffer())
                        .then(arrayBuffer => {
                            var data = new Uint8Array(arrayBuffer);
                            var workbook = XLSX.read(data, {type: "array"});
                            var html = XLSX.utils.sheet_to_html(workbook.Sheets[workbook.SheetNames[0]]);
                            document.getElementById("document-viewer").innerHTML = html;
                        })
                        .catch(error => console.log(error));
                </script>';
			}
			echo "<a href='$doc_url' target='_blank'>Or Download the Document</a><style>
.pdfobject-container { max-height: 80vh; height: 700px; min-height: 400px; border: 1px solid #ccc; }
</style>";
		}
	}

	protected function x_content_template() {
		?>
        <#
        if (settings.document_file.url) {
        if (settings.doc_type === 'pdf') { #>
        <div id="document-viewer"></div>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                PDFObject.embed("{{ settings.document_file.url }}", "#document-viewer");
            });
        </script>
        <# } else if (settings.doc_type === 'markdown') { #>
        <div id="document-viewer"></div>
        <script>
            fetch("{{ settings.document_file.url }}")
                .then(response => response.text())
                .then(text => {
                    document.getElementById("document-viewer").innerHTML = marked(text);
                });
        </script>
        <# } else if (settings.doc_type === 'docx') { #>
        <div id="document-viewer"></div>
        <script>
            fetch("{{ settings.document_file.url }}")
                .then(response => response.arrayBuffer())
                .then(arrayBuffer => mammoth.convertToHtml({arrayBuffer: arrayBuffer}))
                .then(result => {
                    document.getElementById("document-viewer").innerHTML = result.value;
                })
                .catch(error => console.log(error));
        </script>
        <# } else if (settings.doc_type === 'excel') { #>
        <div id="document-viewer"></div>
        <script>
            fetch("{{ settings.document_file.url }}")
                .then(response => response.arrayBuffer())
                .then(arrayBuffer => {
                    var data = new Uint8Array(arrayBuffer);
                    var workbook = XLSX.read(data, {type: "array"});
                    var html = XLSX.utils.sheet_to_html(workbook.Sheets[workbook.SheetNames[0]]);
                    document.getElementById("document-viewer").innerHTML = html;
                })
                .catch(error => console.log(error));
        </script>
        <# }
        } #>
		<?php
	}
}