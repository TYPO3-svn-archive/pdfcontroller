plugin.tx_pdfcontroller_pi2 {
    // empty statement for proper comments only
  master_templates {
  }
    // pdf_link_realUrl
  master_templates =
  master_templates {
      // empty statement for proper comments only
    pdf_link_realUrl {
    }
      // Link to PDF controller with realUrl
    pdf_link_realUrl = TEXT
    pdf_link_realUrl {
      typolink {
        parameter = {$plugin.pdfcontroller.userinterface.id}
          // Example:  ...ADDITIONALPARAMS...print.html&tx_pdfcontroller_pi1[pixels]=580
        additionalParams =  ###ADDITIONALPARAMS###{$plugin.pdfcontroller.realUrl.print}
        ATagParams    = rel="nofollow"
        rawUrlEncode  = 1
        returnLast    = url
      }
    }
  }
}
