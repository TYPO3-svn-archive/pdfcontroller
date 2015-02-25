plugin.tx_pdfcontroller_pi2 {
    // empty statement for proper comments only
  master_templates {
  }
    // pdf_and_print_button_rawUrlEncode
  master_templates =
  master_templates {
      // empty statement for proper comments only
    pdf_and_print_button_rawUrlEncode {
    }
      // Example with rawUrlEncode. See typolink.additionalParams below ...
    pdf_and_print_button_rawUrlEncode = COA
    pdf_and_print_button_rawUrlEncode {
      wrap = <div class="tx-pdfcontroller-pi2"><ul> | </ul></div>
      10 < plugin.tx_pdfcontroller_pi2.master_templates.pdf_and_print_button.10
      10 {
        imageLinkWrap {
          typolink {
            additionalParams >
              // Example:  ...ADDITIONALPARAMS...%26type=98&tx_pdfcontroller_pi1[pixels]=580
            additionalParams =  ###ADDITIONALPARAMS###%26type={$plugin.pdfcontroller.pages.print.typeNum}
          }
        }
      }
      20 < plugin.tx_pdfcontroller_pi2.master_templates.pdf_and_print_button.20
    }
  }
}
