/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!*********************!*\
  !*** ./src/view.js ***!
  \*********************/
/**
 * Use this file for JavaScript code that you want to run in the front-end
 * on posts/pages that contain this block.
 *
 * When this file is defined as the value of the `viewScript` property
 * in `block.json` it will be enqueued on the front end of the site.
 *
 * Example:
 *
 * ```js
 * {
 *   "viewScript": "file:./view.js"
 * }
 * ```
 *
 * If you're not making any changes to this file because your project doesn't need any
 * JavaScript running in the front-end, then you should delete this file and remove
 * the `viewScript` property from `block.json`.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/#view-script
 */

(function () {
  function renderDocument() {
    const containers = document.querySelectorAll('.dv-container');
    containers.forEach(container => {
      const docType = container.getAttribute('data-doc-type');
      const docUrl = container.getAttribute('data-doc-url');
      const domId = container.id;
      if (docType === 'pdf') {
        PDFObject.embed(docUrl, `#${domId}`);
      } else if (docType === 'markdown') {
        fetch(docUrl).then(response => response.text()).then(text => {
          container.innerHTML = marked.parse(text);
        });
      } else if (docType === 'docx') {
        fetch(docUrl).then(response => response.arrayBuffer()).then(arrayBuffer => mammoth.convertToHtml({
          arrayBuffer
        })).then(result => {
          container.innerHTML = result.value;
        }).catch(console.error);
      } else if (docType === 'excel') {
        fetch(docUrl).then(response => response.arrayBuffer()).then(arrayBuffer => {
          const data = new Uint8Array(arrayBuffer);
          const workbook = XLSX.read(data, {
            type: 'array'
          });
          const html = XLSX.utils.sheet_to_html(workbook.Sheets[workbook.SheetNames[0]]);
          container.innerHTML = html;
        }).catch(console.error);
      }
    });
  }
  document.addEventListener('DOMContentLoaded', renderDocument);
})();
/******/ })()
;
//# sourceMappingURL=view.js.map