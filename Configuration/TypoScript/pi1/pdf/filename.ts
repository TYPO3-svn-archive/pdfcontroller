plugin.tx_pdfcontroller_pi1 {
    // empty statement for proper comments only
  pdf {
  }
    // filename
  pdf =
  pdf {
      // empty statement for proper comments only
    filename {
    }
      // page-title, _date, .pdf
    filename = COA
    filename {
        // page : title
      10 = TEXT
      10 {
        stdWrap {
          data = page : title
          htmlSpecialChars = 1
        }
        wrap = |
      }
        // date
      30 = TEXT
      30 {
        data      = date : U
        strftime  = %Y%m%d%H%M%S
        wrap      = _|
      }
        // extension pdf
      50 = TEXT
      50 {
        value = pdf
        wrap  = .|
      }
    }
  }
}