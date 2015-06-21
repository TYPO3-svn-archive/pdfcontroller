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
          cObject = COA
          cObject {
              // url
            10 = TEXT
            10 {
              data = page:uid
            }
              // target
            20 = TEXT
            20 {
              value       = _blank
              noTrimWrap  = | "|"|
            }
              // class
            30 = TEXT
            30 {
              value       = -
              noTrimWrap  = | "|"|
            }
              // title
            40 = TEXT
            40 {
              value       = -
              stdWrap {
                stripHtml         = 1
                htmlSpecialChars  = 1
                crop              = 30
              }
              noTrimWrap  = | "|"|
            }
          }
        }
        additionalParams  = &type={$plugin.pdfcontroller.pages.pdf.typeNum}
        ATagParams        = rel="nofollow"
      }
    }
  }
}
