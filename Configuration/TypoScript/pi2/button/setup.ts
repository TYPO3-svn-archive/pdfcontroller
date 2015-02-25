plugin.tx_pdfcontroller_pi2 {
    // empty statement for proper comments only
  button {
  }
  button = IMAGE
  button {
    wrap = |&nbsp;
    file = ###IMAGEFILE###
    altText = TEXT
    altText {
      value = Download content as PDF file
      lang {
        de = Inhalt als PDF-Datei herunterladen
      }
    }
    titleText < .altText
    imageLinkWrap = 1
    imageLinkWrap {
      enable = 1
      typolink {
        parameter         = ###PARAMETER###
        additionalParams  = ###ADDITIONALPARAMS###
        ATagParams        = rel="nofollow"
      }
    }
  }
}
