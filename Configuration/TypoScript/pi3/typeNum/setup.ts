plugin.tx_pdfcontroller {
    // empty statement for proper comments only
  settings {
  }
    // typeNum
  settings =
  settings {
      // empty statement for proper comments only
    typeNum {
    }
      // pdf, print
    typeNum =
    typeNum {
      pdf   = {$plugin.pdfcontroller.pages.pdf.typeNum}
      print = {$plugin.pdfcontroller.pages.print.typeNum}
    }
  }
}