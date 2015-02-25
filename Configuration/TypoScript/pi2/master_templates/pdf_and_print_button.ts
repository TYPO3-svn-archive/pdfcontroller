plugin.tx_pdfcontroller_pi2 {
    // empty statement for proper comments only
  master_templates {
  }
    // pdf_and_print_button (TYPO3 4.5)
  master_templates =
  master_templates {
      // empty statement for proper comments only
    pdf_and_print_button {
    }
      // Example until TYPO3 4.5
    pdf_and_print_button = COA
    pdf_and_print_button {
      wrap = <div class="tx-pdfcontroller-pi2"><ul> | </ul></div>
        // PDF icon
      10 = IMAGE
      10 {
        wrap = <li>|</li>
        file = typo3conf/ext/pdfcontroller/Resources/Public/Images/pdf.png
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
            parameter         = {$plugin.pdfcontroller.userinterface.id}
            //additionalParams  = TEXT
            additionalParams {
              wrap = &tx_pdfcontroller_pi1[URL]={getIndpEnv:TYPO3_SITE_URL}|
              insertData = 1
              typolink {
                parameter = {page:uid},{$plugin.pdfcontroller.pages.print.typeNum}
                parameter {
                  insertData = 1
                }
                addQueryString  = 1
                addQueryString {
                  exclude = id
                }
                returnLast      = url
              }
            }
            ATagParams    = rel="nofollow"
            rawUrlEncode  = 1
          }
        }
      }
        // Print icon
      20 = IMAGE
      20 {
        wrap = <li>|</li>
        file = typo3conf/ext/pdfcontroller/Resources/Public/Images/printer.png
        altText = TEXT
        altText {
          value = Optimized printing
          lang {
            de = Optimierter Druck
          }
        }
        titleText < .altText
        imageLinkWrap = 1
        imageLinkWrap {
          enable = 1
          typolink {
            parameter = {page:uid},{$plugin.pdfcontroller.pages.print.typeNum}
            parameter {
              insertData = 1
            }
            addQueryString  = 1
            addQueryString {
              exclude = id
            }
            ATagParams      = rel="nofollow"
          }
        }
      }
    }
  }
}
