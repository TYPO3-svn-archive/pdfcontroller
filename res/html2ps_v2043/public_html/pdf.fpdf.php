<?php
/*******************************************************************************
 * Software: FPDF                                                               *
 * Version:  1.53                                                               *
 * Date:     2004-12-31                                                         *
 * Author:   Olivier PLATHEY                                                    *
 * License:  Freeware                                                           *
 *                                                                              *
 * You may use, modify and redistribute this software as you wish.              *
 *******************************************************************************/

/**
 * Heavily patched to adapt to the HTML2PS/HTML2PDF script requirements by
 * Konstantin Bournayev (bkon@bkon.ru)
 *
 * Note: this FPDF variant assumes that magic_quotes_runtime are disabled;
 * the reason is that HTML2PS/PDF explicitly disables them during pipeline
 * processing, thus all calls to FPDF API are "safe"
 */

if (!class_exists('FPDF')) {
  define('FPDF_VERSION','1.53');

  /**
   * FPDF state flags
   */
  define('FPDF_STATE_UNINITIALIZED',    0);
  define('FPDF_STATE_DOCUMENT_STARTED', 1);
  define('FPDF_STATE_PAGE_STARTED',     2);
  define('FPDF_STATE_COMPLETED',        3);

  /**
   * See PDF Reference 1.6 p.664 for explanation of flags specific to submit form action
   */
  define('PDF_SUBMIT_FORM_HTML',        1 << 2); // 1 - HTML, 0 - FDF
  define('PDF_SUBMIT_FORM_COORDINATES', 1 << 4);

  /**
   * See PDF Reference 1.6 p.656 for explanation of flags specific to choice fields
   */
  define('PDF_FIELD_CHOICE_COMBO', 1 << 17);

  /**
   * See PDF Reference 1.6 p.573 for explanation of flag specific to annotations
   */
  define('PDF_ANNOTATION_INVISIBLE',    1 << 0);
  define('PDF_ANNOTATION_HIDDEN',       1 << 1);
  define('PDF_ANNOTATION_PRINTABLE',    1 << 2);
  define('PDF_ANNOTATION_NOZOOM',       1 << 3);
  define('PDF_ANNOTATION_NOROTATE',     1 << 4);
  define('PDF_ANNOTATION_NOVIEW',       1 << 5);
  define('PDF_ANNOTATION_READONLY',     1 << 6);
  define('PDF_ANNOTATION_LOCKED',       1 << 7);
  define('PDF_ANNOTATION_TOGGLENOVIEW', 1 << 8);

  /**
   * See PDF Reference 1.6 p.653 for explanation of flags specific to text fields
   */
  define('PDF_FIELD_TEXT_MULTILINE',1 << 12);
  define('PDF_FIELD_TEXT_PASSWORD', 1 << 13);
  define('PDF_FIELD_TEXT_FILE',     1 << 20);

  /**
   * See PDF Reference 1.6 p.663 for examplanation of flags specific to for submit actions
   */
  define("PDF_FORM_SUBMIT_EXCLUDE", 1 << 0);
  define("PDF_FORM_SUBMIT_NOVALUE", 1 << 1);
  define("PDF_FORM_SUBMIT_EFORMAT", 1 << 2);
  define("PDF_FORM_SUBMIT_GET",     1 << 3);

  class PDFIndirectObject {
    var $object_id;
    var $generation_id;

    function get_object_id() {
      return $this->object_id;
    }

    function get_generation_id() {
      return $this->generation_id;
    }

    /**
     * Outputs the PDF indirect object to PDF file.
     *
     * To pervent infinite loop on circular references, this method checks
     * if current object have been already written to the file.
     *
     * Note that, in general, nested objects should be written to PDF file
     * here too; this task is accomplished by calling _out_nested method,
     * which should be overridden by children classes.
     *
     * @param FPDF $handler PDF file wrapper (FPDF object)
     *
     * @final
     *
     * @see FPDF::is_object_written
     * @see PDFIndirectObject::_out_nested
     */
    function out(&$handler) {
      if (!$handler->is_object_written($this->get_object_id())) {
        $handler->offsets[$this->get_object_id()] = strlen($handler->buffer);
        $handler->_out($handler->_indirect_object($this));

        $this->_out_nested($handler);
      }
    }

    /**
     * Writes all nested objects to the PDF file. Should be overridden by
     * PDFIndirectObject descendants.
     *
     * @param FPDF $handler PDF file wrapper (FPDF object)
     *
     * @see PDFIndirectObject::out
     */
    function _out_nested(&$handler) {
      return true;
    }

    function PDFIndirectObject(&$handler,
                               $object_id,
                               $generation_id) {
      $this->object_id = $object_id;
      $this->generation_id = $generation_id;
    }

    function pdf(&$handler) {
      return $handler->_dictionary($this->_dict($handler));
    }

    function _dict() {
      return array();
    }
  }

  class PDFCMap extends PDFIndirectObject {
    var $_content;

    function PDFCMap($mapping, &$handler, $object_id, $generation_id) {
      $this->PDFIndirectObject($handler,
                               $object_id,
                               $generation_id);

      $num_chars = count($mapping);

      $chars     = "";
      foreach ($mapping as $code => $utf) {
        $chars .= sprintf("<%02X> <%04X> \n", $code, $utf);
      }

      $this->_content = <<<EOF
/CIDInit /ProcSet findresource begin
12 dict begin
begincmap
CIDSystemInfo
<< /Registry (Adobe)
/Ordering (UCS) /Supplement 0 >> def
/CMapName /Adobe-Identity-UCS def
/CMapType 2 def
1 begincodespacerange
<0000> <FFFF>
endcodespacerange
${num_chars} beginbfchar
${chars}
endbfchar
endcmap CMapName currentdict /CMap defineresource pop end end
EOF
;
    }

    function pdf(&$handler) {
      $dict_content   = array(
                              'Length'   => strlen($this->_content)
                              );

      $content = $handler->_dictionary($dict_content);
      $content .= "\n";
      $content .= $handler->_stream($this->_content);

      return $content;
    }
  }

  class PDFPage extends PDFIndirectObject {
    var $annotations;
    var $_width;
    var $_height;

    function PDFPage(&$handler,
                     $width,
                     $height,
                     $object_id,
                     $generation_id) {
      $this->PDFIndirectObject($handler,
                               $object_id,
                               $generation_id);

      $this->set_width($width);
      $this->set_height($height);
    }

    function add_annotation(&$annotation) {
      $this->annotations[] =& $annotation;
    }

    function _annotations(&$handler) {
      return $handler->_reference_array($this->annotations);
    }

    function get_height() {
      return $this->_height;
    }

    function get_width() {
      return $this->_width;
    }

    function set_height($height) {
      $this->_height = $height;
    }

    function set_width($width) {
      $this->_width = $width;
    }
  }

  class PDFAppearanceStream extends PDFIndirectObject {
    var $_content;

    function PDFAppearanceStream(&$handler,
                                 $object_id,
                                 $generation_id,
                                 $content) {
      $this->PDFIndirectObject($handler,
                               $object_id,
                               $generation_id);

      $this->_content = $content;
    }

    function pdf(&$handler) {
      $dict_content   = array(
                              'Type'     => "/XObject",
                              'Subtype'  => "/Form",
                              'FormType' => "1",
                              'BBox'     => "[0 0 100 100]",
                              'Matrix'   => "[1 0 0 1 0 0]",
                              'Resources'=> "2 0 R",
                              'Length'   => strlen($this->_content)
                              );

      $content = $handler->_dictionary($dict_content);
      $content .= "\n";
      $content .= $handler->_stream($this->_content);

      return $content;
    }
  }

  class PDFAnnotation extends PDFIndirectObject {
    function PDFAnnotation(&$handler,
                           $object_id,
                           $generation_id) {
      $this->PDFIndirectObject($handler,
                               $object_id,
                               $generation_id);
    }

    function _dict(&$handler) {
      return array_merge(parent::_dict($handler),
                         array("Type" => $handler->_name("Annot")));
    }
  }

  class PDFRect {
    var $x;
    var $y;
    var $w;
    var $h;

    function PDFRect($x,$y,$w,$h) {
      $this->x = $x;
      $this->y = $y;
      $this->w = $w;
      $this->h = $h;
    }

    function left(&$handler) {
      return $handler->x_coord($this->x);
    }

    function right(&$handler) {
      return $handler->x_coord($this->x+$this->w);
    }

    function top(&$handler) {
      return $handler->y_coord($this->y);
    }

    function bottom(&$handler) {
      return $handler->y_coord($this->y+$this->h);
    }

    function pdf(&$handler) {
      return $handler->_array(sprintf("%.2f %.2f %.2f %.2f",
                                      $this->left($handler),
                                      $this->top($handler),
                                      $this->right($handler),
                                      $this->bottom($handler)));
    }
  }

  class PDFAnnotationExternalLink extends PDFAnnotation {
    var $rect;
    var $link;

    function PDFAnnotationExternalLink(&$handler,
                                       $object_id,
                                       $generation_id,
                                       $rect,
                                       $link) {
      $this->PDFAnnotation($handler,
                           $object_id,
                           $generation_id);

      $this->rect = $rect;
      $this->link = $link;
    }

    function _dict(&$handler) {
      return array_merge(parent::_dict($handler),
                         array(
                               'Subtype' => "/Link",
                               'Rect'    => $this->rect->pdf($handler),
                               'Border'  => "[0 0 0]",
                               'A'       => "<</S /URI /URI ".$handler->_textstring($this->link).">>"
                               ));
    }
  }

  class PDFAnnotationInternalLink extends PDFAnnotation {
    var $rect;
    var $link;

    function PDFAnnotationInternalLink(&$handler,
                                       $object_id,
                                       $generation_id,
                                       $rect,
                                       $link) {
      $this->PDFAnnotation($handler,
                           $object_id,
                           $generation_id);

      $this->rect = $rect;
      $this->link = $link;
    }

    function pdf(&$handler) {
      if ($handler->DefOrientation=='P') {
        $wPt=$handler->fwPt;
        $hPt=$handler->fhPt;
      } else {
        $wPt=$handler->fhPt;
        $hPt=$handler->fwPt;
      }
      $l = $handler->links[$this->link];
      $h = $hPt;

      /**
       * Sometimes hyperlinks may refer to pages NOT present in PDF document
       * Example: a very long frame content; it it trimmed to one page, as
       * framesets newer take more than one frame. A link targe which should be rendered
       * on third page without frames will be never rendered at all.
       *
       * In this case we should disable link at all to prevent error from appearing
       */

      if (!isset($handler->_pages[$l[0]-1])) {
        return "";
      }

      $content = $handler->_dictionary(array(
                                             'Type'    => "/Annot",
                                             'Subtype' => "/Link",
                                             'Rect'    => $this->rect->pdf($handler),
                                             'Border'  => "[0 0 0]",
                                             'Dest'    => sprintf("[%s /XYZ 0 %.2f null]",
                                                                  $handler->_reference($handler->_pages[$l[0]-1]),
                                                                  $h-$l[1]*$handler->k)
                                             ));
      return $content;
    }
  }

  class PDFAnnotationWidget extends PDFAnnotation {
    var $_rect;

    function PDFAnnotationWidget(&$handler,
                                 $object_id,
                                 $generation_id,
                                 $rect) {
      $this->PDFAnnotation($handler,
                           $object_id,
                           $generation_id);

      $this->_rect = $rect;
    }

    function _dict(&$handler) {
      return array_merge(parent::_dict($handler),
                         array("Subtype" => $handler->_name("Widget"),
                               'Rect'    => $this->_rect->pdf($handler)));
    }
  }

  /**
   * Generic PDF Form
   */
  class PDFFieldGroup extends PDFIndirectObject {
    var $_kids;
    var $_group_name;

    function PDFFieldGroup(&$handler,
                           $object_id,
                           $generation_id,
                           $group_name) {
      $this->PDFIndirectObject($handler,
                               $object_id,
                               $generation_id);

      /**
       * Generate default group name, if needed
       */
      if (is_null($group_name) || $group_name == "") {
        $group_name = sprintf("FieldGroup%d", $this->get_object_id());
      }
      $this->_group_name = $group_name;

      $this->_kids = array();
    }

    function _check_field_name($field) {
      /**
       * Check if field name is empty
       */
      if (trim($field->get_field_name()) == "") {
        error_log(sprintf("Found form field with empty name"));
        return false;
      }

      /**
       * Check if field name is unique inside this form! If we will not do it,
       * some widgets may become inactive (ignored by PDF Reader)
       */
      foreach ($this->_kids as $kid) {
        if ($kid->get_field_name() == $field->get_field_name()) {
          error_log(sprintf("Interactive form '%s' already contains field named '%s'",
                            $this->_group_name,
                            $kid->get_field_name()));
          return false;
        }
      }

      return true;
    }

    function add_field(&$field) {
      if (!$this->_check_field_name($field)) {
        /**
         * Field name is not unique; replace it with automatically-generated one
         */
        $field->set_field_name(sprintf("%s_FieldObject%d",
                                       $field->get_field_name(),
                                       $field->get_object_id()));
      }

      $this->_kids[] =& $field;
      $field->set_parent($this);
    }

    function _dict(&$handler) {
      return array_merge(parent::_dict($handler),
                         array("Kids" => $handler->_reference_array($this->_kids),
                               "T"    => $handler->_textstring($this->_group_name)));
      //return $content;
    }

    function _out_nested(&$handler) {
      parent::_out_nested($handler);

      foreach ($this->_kids as $field) {
        $field->out($handler);
      }
    }
  }

  /**
   * Generic superclass for all PDF interactive field widgets
   */
  class PDFField extends PDFAnnotationWidget {
    /**
     * @var string Partial field name (see PDF Specification 1.6 p.638 for explanation on "partial" and
     * "fully qualified" field names
     * @access private
     */
    var $_field_name;

    /**
     * @var PDFFieldGroup REference to a containing form object
     * @access private
     */
    var $_parent;

    function PDFField(&$handler,
                      $object_id,
                      $generation_id,
                      $rect,
                      $field_name) {
      $this->PDFAnnotationWidget($handler,
                                 $object_id,
                                 $generation_id,
                                 $rect);

      /**
       * Generate default field name, if needed
       * @TODO: validate field_name contents
       */
      if (is_null($field_name) || $field_name == "") {
        $field_name = sprintf("FieldObject%d", $this->get_object_id());
      }

      $this->_field_name = $field_name;
    }

    function get_field_name() {
      if ($this->_field_name) {
        return $this->_field_name;
      } else {
        return sprintf("FormObject%d", $this->get_object_id());
      }
    }

    function _dict(&$handler) {
      return array_merge(parent::_dict($handler),
                         array("Parent" => $handler->_reference($this->_parent),
                               "T"      => $handler->_textstring($this->get_field_name()),
                               'F'      => PDF_ANNOTATION_PRINTABLE));
    }

    function pdf(&$handler) {
      return $handler->_dictionary($this->_dict($handler));
    }

    function set_field_name($value) {
      $this->_field_name = $value;
    }

    function set_parent(&$form) {
      $this->_parent =& $form;
    }

    function get_parent() {
      return $this->_parent;
    }
  }

  /**
   * Checkbox interactive form widget
   */
  class PDFFieldCheckBox extends PDFField {
    var $_value;
    var $_appearance_on;
    var $_appearance_off;
    var $_checked;

    function PDFFieldCheckBox(&$handler,
                              $object_id,
                              $generation_id,
                              $rect,
                              $field_name,
                              $value,
                              $checked) {
      $this->PDFField($handler,
                      $object_id,
                      $generation_id,
                      $rect,
                      $field_name);

      $this->_value = $value;
      $this->_checked = $checked;

      $this->_appearance_on = new PDFAppearanceStream($handler,
                                                      $handler->_generate_new_object_number(),
                                                      $generation_id,
                                                      "Q 0 0 1 rg BT /F1 10 Tf 0 0 Td (8) Tj ET q");

      $this->_appearance_off = new PDFAppearanceStream($handler,
                                                       $handler->_generate_new_object_number(),
                                                       $generation_id,
                                                       "Q 0 0 1 rg BT /F1 10 Tf 0 0 Td (8) Tj ET q");
    }

    function _dict(&$handler) {
      return array_merge(parent::_dict($handler),
                         array(
                               'FT'      => '/Btn',
                               'Ff'      => sprintf("%d", 0),
                               'TU'      => "<FEFF>",
                               'MK'      => "<< /CA (3) >>",
                               'DV'      => $this->_checked ? $handler->_name($this->_value) : "/Off",
                               'V'       => $this->_checked ? $handler->_name($this->_value) : "/Off",
                               'AP'      => sprintf("<< /N << /%s %s /Off %s >> >>",
                                                    $this->_value,
                                                    $handler->_reference($this->_appearance_on),
                                                    $handler->_reference($this->_appearance_off))
                               )
                         );
    }

    function _out_nested(&$handler) {
      parent::_out_nested($handler);

      $this->_appearance_on->out($handler);
      $this->_appearance_off->out($handler);
    }
  }

  class PDFFieldPushButton extends PDFField {
    var $_appearance;
    var $fontindex;
    var $fontsize;

    function _out_nested(&$handler) {
      parent::_out_nested($handler);

      $this->_appearance->out($handler);
    }

    function PDFFieldPushButton(&$handler,
                                $object_id,
                                $generation_id,
                                $rect,
                                $fontindex,
                                $fontsize) {
      $this->PDFField($handler,
                      $object_id,
                      $generation_id,
                      $rect,
                      null);
      $this->fontindex = $fontindex;
      $this->fontsize  = $fontsize;

      $this->_appearance = new PDFAppearanceStream($handler,
                                                   $handler->_generate_new_object_number(),
                                                   $generation_id,
                                                   "Q 0 0 1 rg BT /F1 10 Tf 0 0 Td (8) Tj ET q");
    }

    function _action(&$handler) {
      return "<< >>";
    }

    function _dict(&$handler) {
      return array_merge(parent::_dict($handler),
                         array(
                               'FT'      => '/Btn',
                               'Ff'      => sprintf("%d", 1 << 16),
                               'TU'      => "<FEFF>",
                               'DR'      => "2 0 R",
                               'DA'      => sprintf("(0 0 0 rg /F%d %.2f Tf)",
                                                    $this->fontindex,
                                                    $this->fontsize),
                               'AP'      => "<< /N ".$handler->_reference($this->_appearance)." >>",
                               'AA'      => $this->_action($handler)
                               ));
    }
  }

  class PDFFieldPushButtonImage extends PDFFieldPushButton {
    var $_link;

    function PDFFieldPushButtonImage(&$handler,
                                      $object_id,
                                      $generation_id,
                                      $rect,
                                      $fontindex,
                                      $fontsize,
                                      $field_name,
                                      $value,
                                      $link) {
      $this->PDFFieldPushButton($handler,
                                $object_id,
                                $generation_id,
                                $rect,
                                $fontindex,
                                $fontsize);

      $this->_link  = $link;
      $this->set_field_name($field_name);
    }

    function _action(&$handler) {
      $action = $handler->_dictionary(array(
                                            'S'     => "/SubmitForm",
                                            'F'     => $handler->_textstring($this->_link),
                                            'Fields'=> $handler->_reference_array(array($this->get_parent())),
                                            'Flags' => PDF_SUBMIT_FORM_HTML | PDF_SUBMIT_FORM_COORDINATES
                                            )
                                      );
      return $handler->_dictionary(array('U' => $action));
    }
  }

  class PDFFieldPushButtonSubmit extends PDFFieldPushButton {
    var $_link;
    var $_caption;

    function PDFFieldPushButtonSubmit(&$handler,
                                      $object_id,
                                      $generation_id,
                                      $rect,
                                      $fontindex,
                                      $fontsize,
                                      $field_name,
                                      $value,
                                      $link) {
      $this->PDFFieldPushButton($handler,
                                $object_id,
                                $generation_id,
                                $rect,
                                $fontindex,
                                $fontsize);

      $this->_link    = $link;
      $this->_caption = $value;
      $this->set_field_name($field_name);
    }

    function _action(&$handler) {
      $action = $handler->_dictionary(array(
                                            'S'     => "/SubmitForm",
                                            'F'     => $handler->_textstring($this->_link),
                                            'Fields'=> $handler->_reference_array(array($this->get_parent())),
                                            'Flags' => PDF_SUBMIT_FORM_HTML
                                            )
                                      );
      return $handler->_dictionary(array('U' => $action));
    }
  }

  class PDFFieldPushButtonReset extends PDFFieldPushButton {
    function PDFFieldPushButtonReset(&$handler,
                                     $object_id,
                                     $generation_id,
                                     $rect,
                                     $fontindex,
                                     $fontsize) {
      $this->PDFFieldPushButton($handler,
                                $object_id,
                                $generation_id,
                                $rect,
                                $fontindex,
                                $fontsize);
    }

    function _action(&$handler) {
      $action = $handler->_dictionary(array('S' => "/ResetForm"));
      return $handler->_dictionary(array('U' => $action));
    }
  }

  /**
   * Radio button inside the group.
   *
   * Note that radio button is not a field itself; only a group of radio buttons
   * should have name.
   */
  class PDFFieldRadio extends PDFAnnotationWidget {
    /**
     * @var PDFFieldRadioGroup reference to a radio button group
     * @access private
     */
    var $_parent;

    /**
     * @var String value of this radio button
     * @access private
     */
    var $_value;

    var $_appearance_on;
    var $_appearance_off;

    function PDFFieldRadio(&$handler,
                           $object_id,
                           $generation_id,
                           $rect,
                           $value) {
      $this->PDFAnnotationWidget($handler,
                                 $object_id,
                                 $generation_id,
                                 $rect);

      $this->_value = $value;

      $this->_appearance_on = new PDFAppearanceStream($handler,
                                                      $handler->_generate_new_object_number(),
                                                      $generation_id,
                                                      "Q 0 0 1 rg BT /F1 10 Tf 0 0 Td (8) Tj ET q");

      $this->_appearance_off = new PDFAppearanceStream($handler,
                                                       $handler->_generate_new_object_number(),
                                                       $generation_id,
                                                       "Q 0 0 1 rg BT /F1 10 Tf 0 0 Td (8) Tj ET q");
    }

    function _dict(&$handler) {
      return array_merge(parent::_dict($handler),
                         array(
                               'MK'      => "<< /CA (l) >>",
                               'Parent'  => $handler->_reference($this->_parent),
                               'AP'      => sprintf("<< /N << /%s %s /Off %s >> >>",
                                                    $this->_value,
                                                    $handler->_reference($this->_appearance_on),
                                                    $handler->_reference($this->_appearance_off))
                               ));
    }

    function _out_nested(&$handler) {
      parent::_out_nested($handler);

      $this->_appearance_on->out($handler);
      $this->_appearance_off->out($handler);
    }

    /**
     * Set a reference to the radio button group containing this group
     *
     * @param PDFFieldRadioGroup $parent reference to a group object
     */
    function set_parent(&$parent) {
      $this->_parent =& $parent;
    }
  }

  /**
   * Create new group of radio buttons
   */
  class PDFFieldRadioGroup extends PDFFieldGroup {
    var $_parent;
    var $_checked;

    function _dict($handler) {
      return array_merge(parent::_dict($handler),
                         array(
                               'DV'      => $this->_checked ? $handler->_name($this->_checked) : "/Off",
                               'V'       => $this->_checked ? $handler->_name($this->_checked) : "/Off",
                               "FT"      => $handler->_name('Btn'),
                               "Ff"      => sprintf("%d", 1 << 15),
                               "Parent"  => $handler->_reference($this->_parent)
                               ));
    }

    function _check_field_name($field) {
      /**
       * As radio buttons always have same field name, no checking should be made here
       */

      return true;
    }

    function PDFFieldRadioGroup(&$handler,
                                $object_id,
                                $generation_id,
                                $group_name) {
      $this->PDFFieldGroup($handler,
                           $object_id,
                           $generation_id,
                           $group_name);

      $this->_checked = null;
    }

    /**
     * @return String name of the radio group
     */
    function get_field_name() {
      return $this->_group_name;
    }

    function set_checked($value) {
      $this->_checked = $value;
    }

    function set_parent(&$parent) {
      $this->_parent =& $parent;
    }
  }

  class PDFFieldSelect extends PDFField {
    var $_options;
    var $_value;

    function _dict(&$handler) {
      $options = array();
      foreach ($this->_options as $arr) {
        $options[] = $handler->_array(sprintf("%s %s",
                                              $handler->_textstring($arr[0]),
                                              $handler->_textstring($arr[1])));
      }

      $options_str = $handler->_array(implode(" ",$options));

      return array_merge(parent::_dict($handler),
                         array('FT'      => '/Ch',
                               'Ff'      => PDF_FIELD_CHOICE_COMBO,
                               'V'       => $handler->_textstring($this->_value), // Current value
                               'DV'      => $handler->_textstring($this->_value), // Default value
                               'DR'      => "2 0 R",
                               'Opt'     => $options_str));
    }

    function PDFFieldSelect(&$handler,
                            $object_id,
                            $generation_id,
                            $rect,
                            $field_name,
                            $value,
                            $options) {
      $this->PDFField($handler,
                      $object_id,
                      $generation_id,
                      $rect,
                      $field_name);

      $this->_options = $options;
      $this->_value   = $value;
    }
  }

  /**
   * Interactive text input
   */
  class PDFFieldText extends PDFField {
    var $fontindex;
    var $fontsize;

    var $_appearance;

    /**
     * @var String contains the default value of this text field
     * @access private
     */
    var $_value;

    function _dict(&$handler) {
      return array_merge(parent::_dict($handler),
                         array(
                               'FT'      => '/Tx',
                               'V'       => $handler->_textstring($this->_value), // Current value
                               'DV'      => $handler->_textstring($this->_value), // Default value
                               'DR'      => "2 0 R",
                               // @TODO fix font references
                               'DA'      => sprintf("(0 0 0 rg /FF%d %.2f Tf)",
                                                    $this->fontindex,
                                                    $this->fontsize),
//                                'AP'      => $handler->_dictionary(array("N" => $handler->_reference($this->_appearance))),
                               ));
    }

    function _out_nested(&$handler) {
      //      $this->_appearance->out($handler);
    }

    function PDFFieldText(&$handler,
                          $object_id,
                          $generation_id,
                          $rect,
                          $field_name,
                          $value,
                          $fontindex,
                          $fontsize) {
      $this->PDFField($handler,
                      $object_id,
                      $generation_id,
                      $rect,
                      $field_name);

      $this->fontindex = $fontindex;
      $this->fontsize  = $fontsize;
      $this->_value = $value;

//       $this->_appearance = new PDFAppearanceStream($handler,
//                                                    $handler->_generate_new_object_number(),
//                                                    $generation_id,
//                                                    "/Tx BMC EMC");
    }
  }

  class PDFFieldMultilineText extends PDFFieldText {
    function _dict(&$handler) {
      return array_merge(parent::_dict($handler),
                         array('Ff'      => PDF_FIELD_TEXT_MULTILINE));
    }
  }

  /**
   * "Password" text input field
   */
  class PDFFieldPassword extends PDFFieldText {
    function PDFFieldPassword(&$handler,
                              $object_id,
                              $generation_id,
                              $rect,
                              $field_name,
                              $value,
                              $fontindex,
                              $fontsize) {
      $this->PDFFieldText($handler,
                          $object_id,
                          $generation_id,
                          $rect,
                          $field_name,
                          $value,
                          $fontindex,
                          $fontsize);
    }

    function _dict(&$handler) {
      return array_merge(parent::_dict($handler),
                         array('Ff'      => PDF_FIELD_TEXT_PASSWORD));
    }
  }

  class FPDF {
  }
}
?>
