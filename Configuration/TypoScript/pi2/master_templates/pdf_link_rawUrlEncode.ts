plugin.tx_pdfcontroller_pi2 {
    // empty statement for proper comments only
  master_templates {
  }
    // pdf_link_rawUrlEncode
  master_templates =
  master_templates {
      // empty statement for proper comments only
    pdf_link_rawUrlEncode {
    }
      // Link to PDF controller without realUrl
    pdf_link_rawUrlEncode = TEXT
    pdf_link_rawUrlEncode {
      typolink {
        parameter = {$plugin.pdfcontroller.userinterface.id}
          // Example:  ...ADDITIONALPARAMS...%26type=98&tx_pdfcontroller_pi1[pixels]=580
        additionalParams =  ###ADDITIONALPARAMS###%26type={$plugin.pdfcontroller.pages.print.typeNum}
        ATagParams    = rel="nofollow"
        rawUrlEncode  = 1
        returnLast    = url
      }
    }
  }
}
