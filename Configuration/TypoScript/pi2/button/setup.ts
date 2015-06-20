plugin.tx_pdfcontroller_pi2 {
    // empty statement for proper comments only
  button {
  }
  button = IMAGE
  button {
    wrap = |&nbsp;
    file = {$plugin.pdfcontroller.button.icon}
    altText {
      stdWrap {
        cObject = TEXT
        cObject {
          value = Download content as PDF file
          lang {
            de = Inhalt als PDF-Datei herunterladen
          }
        }
      }
    }
    titleText < .altText
    imageLinkWrap = 1
    imageLinkWrap {
      enable = 1
      typolink {
        parameter {
          data = page:uid
        }
        additionalParams  = &type={$plugin.pdfcontroller.pages.pdf.typeNum}
        ATagParams        = rel="nofollow"
      }
    }
  }
}
