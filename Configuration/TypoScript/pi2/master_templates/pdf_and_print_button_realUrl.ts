plugin.tx_pdfcontroller_pi2 {
    // empty statement for proper comments only
  master_templates {
  }
    // pdf_and_print_button_realUrl
  master_templates =
  master_templates {
      // empty statement for proper comments only
    pdf_and_print_button_realUrl {
    }
      // Example with a realUrl configuration. See typolink.additionalParams below ...
    pdf_and_print_button_realUrl = COA
    pdf_and_print_button_realUrl {
      wrap = <div class="tx-pdfcontroller-pi2"><ul> | </ul></div>
      10 < plugin.tx_pdfcontroller_pi2.master_templates.pdf_and_print_button.10
      10 {
        imageLinkWrap {
          typolink {
            additionalParams >
              // Example:  ...ADDITIONALPARAMS...print.html&tx_pdfcontroller_pi1[pixels]=580
            additionalParams =  ###ADDITIONALPARAMS###{$plugin.pdfcontroller.realUrl.print}
          }
        }
      }
      20 < plugin.tx_pdfcontroller_pi2.master_templates.pdf_and_print_button.20
    }
  }
}